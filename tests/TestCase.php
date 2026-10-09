<?php

namespace EFive\JalaliSchedule\Tests;

use EFive\JalaliSchedule\JalaliScheduleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            JalaliScheduleServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.timezone', 'Asia/Tehran');
    }
}
