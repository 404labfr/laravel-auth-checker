<?php

namespace Lab404\Tests;

use Illuminate\Config\Repository;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;
use Lab404\Tests\Stubs\Models\CustomDevice;
use Lab404\Tests\Stubs\Models\CustomLogin;
use Lab404\Tests\Stubs\Models\User;

class HasLoginsAndDevicesTest extends TestCase
{
    /** @var Agent $agent */
    protected $agent;
    /** @var  Repository $config */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->agent = $this->app->make('agent');
        $this->config = $this->app->make('config');
    }

    /** @test */
    public function it_can_access_logins()
    {
        $user = User::first();
        $user->logins()->saveMany([
            new Login(['ip_address' => '1.2.3.4', 'type' => Login::TYPE_LOGIN]),
            new Login(['ip_address' => '1.2.3.4', 'type' => Login::TYPE_LOGIN]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_FAILED]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_FAILED]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_FAILED]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_LOCKOUT]),
        ]);

        $this->assertEquals(2, $user->auths()->count());
        $this->assertEquals(3, $user->fails()->count());
        $this->assertEquals(1, $user->lockouts()->count());
    }

    /** @test */
    public function it_can_access_logins_with_custom_models()
    {
        $this->config->set('auth-checker.models.login', CustomLogin::class);

        $user = User::first();
        $user->logins()->saveMany([
            new Login(['ip_address' => '1.2.3.4', 'type' => Login::TYPE_LOGIN]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_FAILED]),
            new Login(['ip_address' => '5.4.3.2', 'type' => Login::TYPE_LOCKOUT]),
        ]);

        $this->assertEquals(1, $user->auths()->count());
        $this->assertEquals(1, $user->fails()->count());
        $this->assertEquals(1, $user->lockouts()->count());
        $this->assertInstanceOf(CustomLogin::class, $user->logins->first());

        $this->config->set('auth-checker.models.login', null);
    }

    /** @test */
    public function it_can_access_devices()
    {
        $user = User::first();
        $user->devices()->saveMany([
            new Device(),
            new Device(),
            new Device(),
        ]);

        $this->assertEquals(3, $user->devices()->count());
    }

    /** @test */
    public function it_can_access_devices_with_custom_models()
    {
        $this->config->set('auth-checker.models.device', CustomDevice::class);

        $user = User::first();
        $user->devices()->saveMany([
            new Device(),
            new Device(),
        ]);

        $this->assertEquals(2, $user->devices()->count());
        $this->assertInstanceOf(CustomDevice::class, $user->devices->first());
        $this->config->set('auth-checker.models.device', null);
    }

    /** @test */
    public function it_detects_devices()
    {
        $user = User::first();

        $this->assertFalse($user->hasDevices());

        $user->devices()->saveMany([
            new Device(),
            new Device(),
        ]);

        $this->assertTrue($user->hasDevices());
    }
}
