<?php

namespace Lab404\AuthChecker\Services;

use Carbon\Carbon;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Events\DeviceCreated;
use Lab404\AuthChecker\Events\FailedAuth;
use Lab404\AuthChecker\Events\LockoutAuth;
use Lab404\AuthChecker\Events\LoginCreated;
use Lab404\AuthChecker\Interfaces\HasLoginsAndDevicesInterface;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;

class AuthChecker
{
    /** @var Application */
    private $app;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    /**
     * AuthChecker
     *
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->config = $app['config'];
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @return  void
     */
    public function handleLogin(HasLoginsAndDevicesInterface $user)
    {
        $device = $this->findOrCreateUserDeviceByAgent($user);

        if ($this->shouldLogDeviceLogin($device)) {
            $this->createUserLoginForDevice($user, $device);
        }
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @return  void
     */
    public function handleFailed(HasLoginsAndDevicesInterface $user)
    {
        $device = $this->findOrCreateUserDeviceByAgent($user);
        $this->createUserLoginForDevice($user, $device, Login::TYPE_FAILED);

        event(new FailedAuth($device->login, $device));
    }

    /**
     * @param   array $payload
     * @return  void
     */
    public function handleLockout(array $payload = [])
    {
        $payload = Collection::make($payload);

        $user = $this->findUserFromPayload($payload);

        if ($user) {
            $device = $this->findOrCreateUserDeviceByAgent($user);
            $this->createUserLoginForDevice($user, $device, Login::TYPE_LOCKOUT);

            event(new LockoutAuth($device->login, $device));
        }
    }

    /**
     * @param HasLoginsAndDevicesInterface $user
     * @param Agent|null                   $agent
     * @return Device
     */
    public function findOrCreateUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent = null)
    {
        $agent = is_null($agent) ? $this->app['agent'] : $agent;
        $device = $this->findUserDeviceByAgent($user, $agent);

        if (is_null($device)) {
            $device = $this->createUserDeviceByAgent($user, $agent);
        }

        return $device;
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @param   Agent                        $agent
     * @return  Device|null
     */
    public function findUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent)
    {
        if (!$user->hasDevices()) {
            return null;
        }

        $matching = $user->devices->filter(function ($item) use ($agent) {
            return $this->deviceMatch($item, $agent);
        })->first();

        return $matching ? $matching : null;
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @param   Agent                        $agent
     * @return  Device
     */
    public function createUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent)
    {
        $device = new Device();

        $device->platform = $agent->platform();
        $device->platform_version = $agent->version($device->platform);
        $device->browser = $agent->browser();
        $device->browser_version = $agent->version($device->browser);
        $device->is_desktop = $agent->isDesktop() ? true : false;
        $device->is_mobile = $agent->isMobile() ? true : false;
        $device->language = count($agent->languages()) ? $agent->languages()[0] : null;
        $device->user_id = $user->getKey();

        $device->save();

        event(DeviceCreated::class);

        return $device;
    }

    /**
     * @param Collection $payload
     * @return HasLoginsAndDevicesInterface|null
     */
    public function findUserFromPayload(Collection $payload)
    {
        $login_column = $this->getLoginColumnConfig();

        if ($payload->has($login_column)) {
            $model = (string)$this->config->get('auth.providers.users.model');
            $login_value = $payload->get($login_column);

            /** @var Builder $model */
            $user = $model::where($login_column, '=', $login_value)->first();
            return $user;
        }

        return null;
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @param   Device                       $device
     * @param   string                       $type
     * @return  Login
     */
    public function createUserLoginForDevice(
        HasLoginsAndDevicesInterface $user,
        Device $device,
        $type = Login::TYPE_LOGIN
    ) {
        $ip = $this->request->ip();

        $login = new Login([
            'user_id' => $user->getKey(),
            'ip_address' => $ip,
            'device_id' => $device->id,
            'type' => $type,
        ]);

        $device->login()->save($login);

        event(new LoginCreated($login));

        return $login;
    }

    /**
     * @param   HasLoginsAndDevicesInterface $user
     * @param   Agent                        $agent
     * @return  false|Device
     */
    public function findDeviceForUser(HasLoginsAndDevicesInterface $user, Agent $agent)
    {
        if (!$user->hasDevices()) {
            return false;
        }

        $device = $user->devices->filter(function ($item) use ($agent) {
            return $this->deviceMatch($item, $agent);
        })->first();

        return is_null($device) ? false : $device;
    }

    /**
     * @param   Device $device
     * @return  bool
     */
    public function shouldLogDeviceLogin(Device $device)
    {
        $throttle = $this->getLoginThrottleConfig();

        if ($throttle === 0 || is_null($device->login)) {
            return true;
        }

        $limit = Carbon::now()->subMinutes($throttle);
        $login = $device->login;

        if (isset($login->created_at) && $login->created_at->gt($limit)) {
            return false;
        }

        return true;
    }

    /**
     * @param   Device     $device
     * @param   Agent      $agent
     * @param   array|null $attributes
     * @return  bool
     */
    public function deviceMatch(Device $device, Agent $agent, array $attributes = null)
    {
        $attributes = is_null($attributes) ? $this->getDeviceMatchingAttributesConfig() : $attributes;
        $matches = count($attributes) > 0 ? false : true;

        if (in_array('platform', $attributes)) {
            $matches = $device->platform === $agent->platform();
        }

        if (in_array('platform_version', $attributes)) {
            $matches = $device->platform_version === $agent->version($device->platform);
        }

        if (in_array('browser', $attributes)) {
            $matches = $device->browser === $agent->browser();
        }

        if (in_array('browser_version', $attributes)) {
            $matches = $device->browser_version === $agent->version($device->browser);
        }

        if (in_array('language', $attributes)) {
            $matches = $device->language === $agent->version($device->language);
        }

        return $matches;
    }

    /**
     * @param   void
     * @return  array
     */
    public function getDeviceMatchingAttributesConfig()
    {
        return $this->config->get('laravel-auth-checker.device_matching_attributes', [
            'ip',
            'platform',
            'platform_version',
            'browser',
        ]);
    }

    /**
     * @param   void
     * @return  int
     */
    public function getLoginThrottleConfig()
    {
        return (int)$this->config->get('laravel-auth-checker.throttle', 0);
    }

    /**
     * @return  string
     */
    public function getLoginColumnConfig()
    {
        return (string)$this->config->get('laravel-auth-checker.login_column', 'email');
    }
}
