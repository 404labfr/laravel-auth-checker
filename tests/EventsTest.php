<?php

namespace Lab404\Tests;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
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
    public function it_registers_login_and_device_on_new_autentication()
    {
        /** @var Agent $agent */
        $agent = $this->app->make('agent');
        $agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.00 (KHTML, like Gecko) Chrome/56.0.0000.00 Safari/537.00');

        $user = Auth::loginUsingId(1);

        $this->assertTrue(Auth::check());
        $this->assertInstanceOf(User::class, $user);

        $user->load('devices');
        $this->assertEquals(1, $user->devices->count());

        $device = $user->devices->first();
        $this->assertEquals('OS X', $device->platform);
        $this->assertEquals('10_12_0', $device->platform_version);
        $this->assertEquals('Chrome', $device->browser);
    }

    /** @test */
    public function it_creates_failed_login()
    {
        $user = User::first();
        event(new Failed($user, []));

        $this->assertEquals(0, $user->auths()->count());
        $this->assertEquals(1, $user->fails()->count());
        $this->assertEquals(1, $user->logins()->count());
    }

    /** @test */
    public function it_creates_lockouts_login()
    {
        $user = User::first();

        request()->merge(['email' => 'admin@exemple.com']);
        event(new Lockout(request()));

        $this->assertEquals(0, $user->auths()->count());
        $this->assertEquals(1, $user->lockouts()->count());
        $this->assertEquals(1, $user->logins()->count());
    }
}
