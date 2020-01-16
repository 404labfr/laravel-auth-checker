<?php

namespace Lab404\Tests;

use Lab404\AuthChecker\AuthCheckerServiceProvider;
use Lab404\Tests\Stubs\Models\User;
use Orchestra\Database\ConsoleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        include_once __DIR__.'/../migrations/create_devices_table.php.stub';
        (new \CreateDevicesTable())->up();

        include_once __DIR__.'/../migrations/create_logins_table.php.stub';
        (new \CreateLoginsTable())->up();

        include_once __DIR__.'/../migrations/update_logins_and_devices_table_user_relation.php.stub';
        (new \UpdateLoginsAndDevicesTableUserRelation())->up();

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--path' => realpath(__DIR__ . '/Stubs/migrations'),
        ]);

        $this->afterApplicationCreated(function () {
            \DB::table('users')->insert([
                ['name' => 'Admin', 'email' => 'admin@exemple.com', 'password' => bcrypt('password')]
            ]);
        });
    }

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

    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
            AuthCheckerServiceProvider::class,
        ];
    }
}
