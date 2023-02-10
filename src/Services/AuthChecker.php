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
    /** @var Application $app */
    private $app;
    /** @var Request $request */
    private $request;
    /** @var Config $config */
    private $config;

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        $this->config = $app['config'];
    }

    public function handleLogin(HasLoginsAndDevicesInterface $user): void
    {
        $device = $this->findOrCreateUserDeviceByAgent($user);

        if ($this->shouldLogDeviceLogin($device)) {
            $this->createUserLoginForDevice($user, $device);
        }
    }

    public function handleFailed(HasLoginsAndDevicesInterface $user): void
    {
        $device = $this->findOrCreateUserDeviceByAgent($user);
        $this->createUserLoginForDevice($user, $device, Login::TYPE_FAILED);

        event(new FailedAuth($device->login, $device));
    }

    public function handleLockout(array $payload = []): void
    {
        $payload = Collection::make($payload);

        $user = $this->findUserFromPayload($payload);

        if ($user) {
            $device = $this->findOrCreateUserDeviceByAgent($user);
            $this->createUserLoginForDevice($user, $device, Login::TYPE_LOCKOUT);

            event(new LockoutAuth($device->login, $device));
        }
    }

    public function findOrCreateUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent = null): Device
    {
        $agent = is_null($agent) ? $this->app['agent'] : $agent;
        $device = $this->findUserDeviceByAgent($user, $agent);

        if (is_null($device)) {
            $device = $this->createUserDeviceByAgent($user, $agent);
        }

        return $device;
    }

    public function findUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent): ?Device
    {
        if (! $user->hasDevices()) {
            return null;
        }

        return $user
            ->devices()
            ->with('login')
            ->get()
            ->filter(fn (Device $device) => $this->deviceMatch($device, $agent))
            ->first();
    }

    public function createUserDeviceByAgent(HasLoginsAndDevicesInterface $user, Agent $agent): Device
    {
        $model = config('auth-checker.models.device') ?? Device::class;
        $device = new $model;

        $device->platform = $agent->platform();
        $device->platform_version = $agent->version($device->platform);
        $device->browser = $agent->browser();
        $device->browser_version = $agent->version($device->browser);
        $device->is_desktop = $agent->isDesktop();
        $device->is_mobile = $agent->isMobile();
        $device->language = count($agent->languages()) ? $agent->languages()[0] : null;

        $device->user()->associate($user);

        $device->save();

        event(new DeviceCreated($device));

        return $device;
    }

    public function findUserFromPayload(Collection $payload): ?HasLoginsAndDevicesInterface
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

    public function createUserLoginForDevice(
        HasLoginsAndDevicesInterface $user,
        Device $device,
        string $type = Login::TYPE_LOGIN
    ): Login {
        $model = config('auth-checker.models.login') ?? Login::class;
        $ip = $this->request->ip();

        $login = new $model([
            'ip_address' => $ip,
            'device_id' => $device->id,
            'type' => $type,
        ]);

        $login->user()->associate($user);

        $device->login()->save($login);

        event(new LoginCreated($login));

        return $login;
    }

    public function findDeviceForUser(HasLoginsAndDevicesInterface $user, Agent $agent): ?Device
    {
        if (! $user->hasDevices()) {
            return null;
        }

        return $user
            ->devices()
            ->with('login')
            ->get()
            ->filter(fn (Device $device) => $this->deviceMatch($device, $agent))
            ->first();
    }

    public function shouldLogDeviceLogin(Device $device): bool
    {
        $throttle = $this->getLoginThrottleConfig();

        $device->loadMissing('login');

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

    public function deviceMatch(Device $device, Agent $agent, array $attributes = null): bool
    {
        $attributes = is_null($attributes) ? $this->getDeviceMatchingAttributesConfig() : $attributes;
        $matches = 0;

        if (in_array('platform', $attributes)) {
            $matches += $device->platform === $agent->platform();
        }

        if (in_array('platform_version', $attributes)) {
            $agentPlatformVersion = $agent->version($device->platform);
            $agentPlatformVersion = empty($agentPlatformVersion) ? '0' : $agentPlatformVersion;
            $matches += $device->platform_version === $agentPlatformVersion;
        }

        if (in_array('browser', $attributes)) {
            $matches += $device->browser === $agent->browser();
        }

        if (in_array('browser_version', $attributes)) {
            $matches += $device->browser_version === $agent->version($device->browser);
        }

        if (in_array('language', $attributes)) {
            $matches += $device->language === $agent->version($device->language);
        }

        return $matches === count($attributes);
    }

    public function getDeviceMatchingAttributesConfig(): array
    {
        return $this->config->get('auth-checker.device_matching_attributes', [
            'platform',
            'platform_version',
            'browser',
        ]);
    }

    public function getLoginThrottleConfig(): int
    {
        return (int)$this->config->get('auth-checker.throttle', 0);
    }

    public function getLoginColumnConfig(): string
    {
        return (string)$this->config->get('auth-checker.login_column', 'email');
    }
}
