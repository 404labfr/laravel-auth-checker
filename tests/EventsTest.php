<?php

namespace Lab404\Tests;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Events\DeviceCreated;
use Lab404\AuthChecker\Events\LoginCreated;
use Lab404\AuthChecker\Services\AuthChecker;
use Lab404\Tests\Stubs\Models\User;

class EventsTest extends TestCase
{
    /** @var  AuthChecker */
    protected $manager;

    /** @var  Dispatcher */
    protected $dispatcher;

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->app['authchecker'];
        $this->dispatcher = $this->app['events'];
    }

    /** @test */
    public function it_registers_listeners()
    {
        $this->assertTrue($this->dispatcher->hasListeners(Login::class));
    }

    /** @test */
    public function it_registers_login_on_new_autentication()
    {
        $user = User::first();
        Event::fake();

        $this->assertEquals(2, $user->logins->count());

        Event::fire(new Login($user, false));
        Event::hasDispatched(LoginCreated::class);
    }

    /** @test */
    public function it_registers_device_on_new_login_creation()
    {
        $user = User::first();
        $login = $user->logins->first();

        Event::fake();

        $agent = new Agent();
        $agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2');
        $this->dispatcher->dispatch(new LoginCreated($user, $login, $agent));

        Event::hasDispatched(DeviceCreated::class);
    }
}
