<?php

namespace EFive\JalaliSchedule;

use Illuminate\Support\ServiceProvider;

class JalaliScheduleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        JalaliScheduleMacros::register();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../stubs/_ide_helper_jalali_schedule.php' => base_path('_ide_helper_jalali_schedule.php'),
            ], 'jalali-schedule-ide-helper');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
