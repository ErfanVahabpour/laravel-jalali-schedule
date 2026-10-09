<?php

namespace EFive\JalaliSchedule\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schedule;

class JalaliScheduleTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_jalali_monthly_on(): void
    {
        $event = Schedule::command('reports:monthly')->jalaliMonthlyOn(1, '00:15');

        $this->assertSame('15 0 * * *', $event->expression);

        // Farvardin 1st (1405-01-01) -> 2026-03-21
        Carbon::setTestNow(Carbon::parse('2026-03-21 00:15:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        // Farvardin 2nd (1405-01-02) -> 2026-03-22
        Carbon::setTestNow(Carbon::parse('2026-03-22 00:15:00', 'Asia/Tehran'));
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_jalali_monthly_default(): void
    {
        $event = Schedule::command('reports:monthly')->jalaliMonthly('01:00');

        $this->assertSame('0 1 * * *', $event->expression);

        Carbon::setTestNow(Carbon::parse('2026-03-21 01:00:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        Carbon::setTestNow(Carbon::parse('2026-03-22 01:00:00', 'Asia/Tehran'));
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_jalali_twice_monthly(): void
    {
        $event = Schedule::command('payroll:advances')->jalaliTwiceMonthly(1, 16, '04:00');

        $this->assertSame('0 4 * * *', $event->expression);

        Carbon::setTestNow(Carbon::parse('2026-03-21 04:00:00', 'Asia/Tehran')); // Day 1
        $this->assertTrue($event->filtersPass($this->app));

        Carbon::setTestNow(Carbon::parse('2026-04-05 04:00:00', 'Asia/Tehran')); // Day 16
        $this->assertTrue($event->filtersPass($this->app));

        Carbon::setTestNow(Carbon::parse('2026-04-04 04:00:00', 'Asia/Tehran')); // Day 15
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_jalali_last_day_of_month(): void
    {
        $event = Schedule::command('finance:close')->jalaliLastDayOfMonth('23:59');

        $this->assertSame('59 23 * * *', $event->expression);

        // Farvardin 31 (last day of 31-day month) -> 2026-04-20
        Carbon::setTestNow(Carbon::parse('2026-04-20 23:59:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        // Farvardin 30 (not last day)
        Carbon::setTestNow(Carbon::parse('2026-04-19 23:59:00', 'Asia/Tehran'));
        $this->assertFalse($event->filtersPass($this->app));

        // Mehr 30 (last day of 30-day month) -> 2026-10-22
        Carbon::setTestNow(Carbon::parse('2026-10-22 23:59:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));
    }

    public function test_jalali_days_of_month(): void
    {
        $event = Schedule::command('reminders:send')->jalaliDaysOfMonth([5, 15, 25], '02:30');

        $this->assertSame('30 2 * * *', $event->expression);

        Carbon::setTestNow(Carbon::parse('2026-03-25 02:30:00', 'Asia/Tehran')); // Farvardin 5
        $this->assertTrue($event->filtersPass($this->app));

        Carbon::setTestNow(Carbon::parse('2026-03-26 02:30:00', 'Asia/Tehran')); // Farvardin 6
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_jalali_quarterly(): void
    {
        $event = Schedule::command('tax:quarterly')->jalaliQuarterly('00:00');

        // Q1: Farvardin 1 (2026-03-21)
        Carbon::setTestNow(Carbon::parse('2026-03-21 00:00:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        // Q2: Tir 1 (2026-06-22)
        Carbon::setTestNow(Carbon::parse('2026-06-22 00:00:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        // Ordibehesht 1 (not start of quarter)
        Carbon::setTestNow(Carbon::parse('2026-04-21 00:00:00', 'Asia/Tehran'));
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_jalali_yearly(): void
    {
        $event = Schedule::command('greeting:nowruz')->jalaliYearly('08:00');

        $this->assertSame('0 8 * * *', $event->expression);

        // Nowruz Farvardin 1
        Carbon::setTestNow(Carbon::parse('2026-03-21 08:00:00', 'Asia/Tehran'));
        $this->assertTrue($event->filtersPass($this->app));

        // Esfand 29
        Carbon::setTestNow(Carbon::parse('2026-03-20 08:00:00', 'Asia/Tehran'));
        $this->assertFalse($event->filtersPass($this->app));
    }

    public function test_iran_weekdays_and_weekends(): void
    {
        $workdays = Schedule::command('sync:office')->iranWeekdays('08:00');
        $this->assertSame('0 8 * * 6,0,1,2,3', $workdays->expression);

        $weekends = Schedule::command('backup:weekend')->iranWeekends('12:00');
        $this->assertSame('0 12 * * 4,5', $weekends->expression);
    }
}
