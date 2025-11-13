<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Application;

abstract class TestCase extends BaseTestCase
{
    // use CreatesApplication;

    // override CreatesApplication trait to add HandleExceptions::flushState() call
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // to remove error: `Test code or tested code removed error handlers other than its own.`
        HandleExceptions::flushState();

        return $app;
    }

    /**
     * Creates the application for static data provider.
     *
     * @return Application
     */
    public static function createApplicationForStaticDataProvider(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // to remove error: `Test code or tested code removed error handlers other than its own.`
        HandleExceptions::flushState();

        return $app;
    }
}
