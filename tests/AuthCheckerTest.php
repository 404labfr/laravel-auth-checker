<?php

namespace Lab404\Tests;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Jenssegers\Agent\Agent;
use Lab404\AuthChecker\Models\Device;
use Lab404\AuthChecker\Models\Login;
use Lab404\AuthChecker\Services\AuthChecker;

class AuthCheckerTest extends TestCase
{
    /** @var AuthChecker $manager */
    protected $manager;
    /** @var  Agent $agent */
    protected $agent;
    /** @var  Repository $config */
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->app->make(AuthChecker::class);
        $this->config = $this->app->make('config');
        $this->agent = $this->app->make('agent');
    }

    /** @test */
    public function it_can_be_accessed_from_container()
    {
        $this->assertInstanceOf(AuthChecker::class, $this->manager);
        $this->assertInstanceOf(AuthChecker::class, $this->app[AuthChecker::class]);
        $this->assertInstanceOf(AuthChecker::class, app('authchecker'));

        $this->config->set('auth-checker.throttle', 5);
        $this->assertEquals(5, $this->manager->getLoginThrottleConfig());
        $this->config->set('auth-checker.throttle', 0);
        $this->assertEquals(0, $this->manager->getLoginThrottleConfig());
    }

    /** @test */
    public function it_matches_device_when_attributes_are_empty()
    {
        $device = new Device(['platform' => 'OS X', 'platform_version' => '10_12_2', 'browser' => 'Chrome']);
        $result = $this->manager->deviceMatch($device, $this->agent, []);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_matches_device_with_attributes()
    {
        $device = new Device(['platform' => 'OS X', 'platform_version' => '10_12_0', 'browser' => 'Chrome']);
        $this->agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.00 (KHTML, like Gecko) Chrome/56.0.0000.00 Safari/537.00');
        $result = $this->manager->deviceMatch($device, $this->agent, ['platform', 'platform_version', 'browser']);
        $this->assertTrue($result);

        $device = new Device(['platform' => 'OS X', 'platform_version' => '10_12_0', 'browser' => 'Chrome']);
        $this->agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/602.0.00 (KHTML, like Gecko) Version/10.0.0 Safari/602.00.00');
        $result = $this->manager->deviceMatch($device, $this->agent, ['platform', 'platform_version']);
        $this->assertTrue($result);

        $device = new Device(['platform' => 'Windows', 'platform_version' => '7', 'browser' => 'Safari']);
        $this->agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/602.0.00 (KHTML, like Gecko) Version/10.0.0 Safari/602.00.00');
        $result = $this->manager->deviceMatch($device, $this->agent, ['browser']);
        $this->assertTrue($result);

        $device = new Device(['platform' => 'OS X', 'platform_version' => '10_12_0', 'browser' => 'Safari', 'browser_version' => '602.00.00']);
        $this->agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/602.0.00 (KHTML, like Gecko) Version/10.0.0 Safari/602.00.00');
        $result = $this->manager->deviceMatch($device, $this->agent);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_should_log_device_login()
    {
        $device = new Device();
        $device->user_id = 1;
        $device->user_type = 'App\Models\User';
        $device->save();

        $this->config->set('auth-checker.throttle', 15);
        $result = $this->manager->shouldLogDeviceLogin($device);
        $this->assertTrue($result);

        $login = new Login(['ip_address' => '1.2.3.4']);
        $login->user_id = 1;
        $login->user_type = 'App\Models\User';
        $device->logins()->save($login);

        $this->config->set('auth-checker.throttle', 0);
        $result = $this->manager->shouldLogDeviceLogin($device);
        $this->assertTrue($result);

        $login->created_at = Carbon::now()->subMinutes(10);
        $this->config->set('auth-checker.throttle', 5);
        $result = $this->manager->shouldLogDeviceLogin($device);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_should_not_log_device_login()
    {
        $device = new Device();
        $device->user_id = 1;
        $device->user_type = 'App\Models\User';
        $device->save();

        $login = new Login(['ip_address' => '1.2.3.4']);
        $login->user_id = 1;
        $login->user_type = 'App\Models\User';
        $device->logins()->save($login);

        $this->config->set('auth-checker.throttle', 5);
        $result = $this->manager->shouldLogDeviceLogin($device);
        $this->assertFalse($result);
    }
}
