<?php

namespace Lab404\Tests;

use Lab404\AuthChecker\Services\AuthChecker;

class AuthCheckerTest extends TestCase
{
    /** @var  AuthChecker */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->app->make(AuthChecker::class);
    }

    /** @test */
    public function it_can_be_accessed_from_container()
    {
        $this->assertInstanceOf(AuthChecker::class, $this->manager);
        $this->assertInstanceOf(AuthChecker::class, $this->app[AuthChecker::class]);
        $this->assertInstanceOf(AuthChecker::class, app('authchecker'));
    }
}
