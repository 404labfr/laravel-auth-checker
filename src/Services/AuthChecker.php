<?php

namespace Lab404\AuthChecker\Services;

use Illuminate\Foundation\Application;

class AuthChecker
{
    /**
     * @var Application
     */
    private $app;

    /**
     * UserFinder constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
