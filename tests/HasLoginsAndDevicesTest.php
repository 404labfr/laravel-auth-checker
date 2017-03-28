<?php

namespace Lab404\Tests;

use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;
use Lab404\Tests\Stubs\Models\User;

class HasLoginsAndDevicesTest extends TestCase
{
    /** @var  Agent */
    protected $agent;

    public function setUp()
    {
        parent::setUp();

        $this->agent = $this->app->make('agent');
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
