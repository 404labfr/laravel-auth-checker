<?php

namespace Lab404\AuthChecker;

use Illuminate\Events\Dispatcher;
use Jenssegers\Agent\AgentServiceProvider;
use Lab404\AuthChecker\Services\AuthChecker;
use Lab404\AuthChecker\Subscribers\AuthCheckerSubscriber;

/**
 * Class AuthCheckerServiceProvider
 *
 * @package Lab404\AuthChecker
 */
class AuthCheckerServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /** @var string $name */
    protected $name = 'auth-checker';

    public function register(): void
    {
        $this->app->bind(AuthChecker::class, AuthChecker::class);

        $this->app->singleton(AuthChecker::class, function ($app) {
            return new AuthChecker($app, $app['request']);
        });

        $this->app->alias(AuthChecker::class, 'authchecker');

        $this->registerDependencies();
    }

    public function boot(): void
    {
        $this->mergeConfig()
            ->mergeLang()
            ->mergeMigrations()
            ->registerEvents();
    }

    protected function registerDependencies(): self
    {
        $this->app->register(AgentServiceProvider::class);

        return $this;
    }

    protected function mergeMigrations(): self
    {
        $path = __DIR__ . '/../migrations';

        $this->publishes([
                $path => database_path('migrations'),
        ], 'migrations');

        return $this;
    }

    protected function mergeConfig(): self
    {
        $configPath = __DIR__ . '/../config/' . $this->name . '.php';

        $this->mergeConfigFrom($configPath, $this->name);

        $this->publishes([$configPath => config_path($this->name . '.php')], 'auth-checker');

        return $this;
    }

    protected function mergeLang(): self
    {
        $langPath = __DIR__ . '/../lang/';

        $this->loadTranslationsFrom($langPath, $this->name);

        return $this;
    }

    protected function registerEvents(): self
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app['events'];
        $dispatcher->subscribe(AuthCheckerSubscriber::class);

        return $this;
    }
}