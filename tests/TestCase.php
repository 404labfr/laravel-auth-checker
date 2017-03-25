<?php

namespace Lab404\Tests;

use Lab404\AuthChecker\AuthCheckerServiceProvider;
use Lab404\Tests\Stubs\Models\User;
use Orchestra\Database\ConsoleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param   void
     * @return  void
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../migrations'),
        ]);

        $this->afterApplicationCreated(function () {
            \DB::table('users')->insert([
                ['name' => 'Admin', 'email' => 'admin@exemple.com', 'password' => bcrypt('password')]
            ]);

            \DB::table('logins')->insert([
                ['user_id' => 1, 'ip_address' => '1.2.3.4', 'device_id' => 1, 'created_at' => \Carbon\Carbon::now()->toDateTimeString()],
                ['user_id' => 1, 'ip_address' => '5.6.7.8', 'device_id' => 1, 'created_at' => \Carbon\Carbon::now()->addDays(5)->toDateTimeString()]
            ]);

            \DB::table('devices')->insert([
                ['user_id' => 1, 'platform' => 'OS X', 'platform_version' => '10_10', 'browser' => 'Chrome', 'browser_version' => 54, 'is_desktop' => 1, 'language' => 'fr_FR', 'created_at' => \Carbon\Carbon::now()->toDateTimeString()]
            ]);
        });
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup the right User class (using stub)
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
            AuthCheckerServiceProvider::class,
        ];
    }
}
