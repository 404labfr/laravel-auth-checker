<?php

namespace Lab404\AuthChecker\Listeners;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Events\DeviceCreated;
use Lab404\AuthChecker\Events\LoginCreated;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;

class SaveUserDevice
{
    /**
     * @param   Login
     * @return  void
     */
    public function handle(LoginCreated $event)
    {
        /** @var Agent $agent */
        $agent = $event->agent;

        /** @var Login $login */
        $login = $event->login;

        /** @var Model $user */
        $user = $event->user;

        if (false === $user->hasDeviceFromAgent($agent)) {
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

            $device->logins()->save($login);

            event(new DeviceCreated($user, $login, $device));
        }
    }
}
