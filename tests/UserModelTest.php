<?php

namespace Lab404\Tests;

use Lab404\AuthChecker\Services\AuthChecker;
use Lab404\Tests\Stubs\Models\User;

class UserModelTest extends TestCase
{
    /** @var  AuthChecker */
    protected $manager;

    /** @var  User */
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->app->make(AuthChecker::class);
        $this->user = User::first();
    }

    /** @test */
    public function it_founds_logins()
    {
        $this->assertCount(2, $this->user->logins);
        $this->assertTrue($this->user->hasLogins());
    }

    /** @test */
    public function it_founds_devices()
    {
        $this->assertCount(1, $this->user->devices);
        $this->assertTrue($this->user->hasDevices());
    }

    /** @test */
    public function it_has_attributes()
    {
        $this->assertEquals($this->user->logins->first()->created_at, $this->user->last_logged_at);
        $this->assertEquals($this->user->logins->first()->ip_address, $this->user->last_ip_address);
        $this->assertContains('1.2.3.4', $this->user->ip_addresses);
        $this->assertContains('5.6.7.8', $this->user->ip_addresses);
    }
}
