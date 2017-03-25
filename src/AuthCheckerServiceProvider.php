<?php

namespace Lab404\AuthChecker;

use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Jenssegers\Agent\AgentServiceProvider;
use Lab404\AuthChecker\Events\LoginCreated;
use Lab404\AuthChecker\Listeners\SaveUserDevice;
use Lab404\AuthChecker\Listeners\SaveUserLogin;
use Lab404\AuthChecker\Services\AuthChecker;

/**
 * Class ServiceProvider
 *
 * @package Lab404\AuthChecker
 */
class AuthCheckerServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @var string
     */
    protected $configName = 'laravel-auth-checker';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthChecker::class, AuthChecker::class);

        $this->app->singleton(AuthChecker::class, function ($app) {
            return new AuthChecker($app);
        });

        $this->app->alias(AuthChecker::class, 'authchecker');
        $this->app->register(AgentServiceProvider::class);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig()->mergeConfig()->mergeMigrations()->registerEvents();
    }

    /**
     * Merge migration files.
     *
     * @param   void
     * @return  self
     */
    protected function mergeMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        return $this;
    }

    /**
     * Merge config file.
     *
     * @param   void
     * @return  self
     */
    protected function mergeConfig()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->mergeConfigFrom($configPath, $this->configName);

        return $this;
    }

    /**
     * Publish config file.
     *
     * @param   void
     * @return  self
     */
    protected function publishConfig()
    {
        $configPath = __DIR__ . '/../config/' . $this->configName . '.php';

        $this->publishes([$configPath => config_path($this->configName . '.php')], 'impersonate');

        return $this;
    }

    /**
     * Registers library events.
     *
     * @param   void
     * @return  self
     */
    protected function registerEvents()
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app['events'];

        $dispatcher->listen(Login::class, SaveUserLogin::class);
        $dispatcher->listen(LoginCreated::class, SaveUserDevice::class);

        return $this;
    }
}