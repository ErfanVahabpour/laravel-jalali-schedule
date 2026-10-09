<?php

// @formatter:off
// phpcs:ignoreFile

/**
 * IDE Helper for Laravel Jalali Schedule Macros.
 * This file provides autocompletion and eliminates "Method not found" warnings in PhpStorm/Intelephense.
 */

namespace Illuminate\Console\Scheduling {
    /**
     * @method $this jalaliMonthly(string $time = '0:0') Schedule the event to run on the 1st day of every Jalali month.
     * @method $this monthlyJalali(string $time = '0:0') Alias for jalaliMonthly.
     * @method $this jalaliMonthlyOn(int $dayOfMonth = 1, string $time = '0:0') Schedule the event to run on a specific day of the Jalali month at a given time.
     * @method $this monthlyOnJalali(int $dayOfMonth = 1, string $time = '0:0') Alias for jalaliMonthlyOn.
     * @method $this jalaliTwiceMonthly(int $first = 1, int $second = 16, string $time = '0:0') Schedule the event to run twice monthly on specific Jalali days.
     * @method $this twiceMonthlyJalali(int $first = 1, int $second = 16, string $time = '0:0') Alias for jalaliTwiceMonthly.
     * @method $this jalaliLastDayOfMonth(string $time = '0:0') Schedule the event to run on the last day of the Jalali month (dynamically handles 29, 30, and 31 days).
     * @method $this lastDayOfJalaliMonth(string $time = '0:0') Alias for jalaliLastDayOfMonth.
     * @method $this jalaliDaysOfMonth(array|int ...$days) Schedule the event to run on specific days of the Jalali month.
     * @method $this daysOfJalaliMonth(array|int ...$days) Alias for jalaliDaysOfMonth.
     * @method $this jalaliQuarterly(string $time = '0:0') Schedule the event to run on the 1st day of every Jalali quarter (Farvardin 1, Tir 1, Mehr 1, Dey 1).
     * @method $this quarterlyJalali(string $time = '0:0') Alias for jalaliQuarterly.
     * @method $this jalaliQuarterlyOn(int $dayOfQuarter = 1, string $time = '0:0') Schedule the event to run on a given day of the first month of each Jalali quarter.
     * @method $this quarterlyOnJalali(int $dayOfQuarter = 1, string $time = '0:0') Alias for jalaliQuarterlyOn.
     * @method $this jalaliYearly(string $time = '0:0') Schedule the event yearly on Nowruz (Farvardin 1st) at the given time.
     * @method $this yearlyJalali(string $time = '0:0') Alias for jalaliYearly.
     * @method $this jalaliYearlyOn(int $month = 1, int $dayOfMonth = 1, string $time = '0:0') Schedule the event yearly on a given Jalali month and day.
     * @method $this yearlyOnJalali(int $month = 1, int $dayOfMonth = 1, string $time = '0:0') Alias for jalaliYearlyOn.
     * @method $this whenJalaliDay(array|int ...$days) Filter the event to only execute on specific Jalali day(s) of the month.
     * @method $this whenJalaliLastDayOfMonth() Filter the event to only execute on the last day of the Jalali month.
     * @method $this whenJalaliMonth(array|int ...$months) Filter the event to only execute in specific Jalali month(s).
     * @method $this iranWeekdays(string $time = '0:0') Schedule the event on Iranian business days (Saturday through Wednesday: Shanbeh to Chaharshanbeh).
     * @method $this iranWeekends(string $time = '0:0') Schedule the event on Iranian weekend days (Thursday and Friday: Panjshanbeh and Jomeh).
     * @method $this shanbeh(string $time = '0:0') Schedule the event on Saturdays (Shanbeh).
     * @method $this yekshanbeh(string $time = '0:0') Schedule the event on Sundays (Yekshanbeh).
     * @method $this doshanbeh(string $time = '0:0') Schedule the event on Mondays (Doshanbeh).
     * @method $this seshanbeh(string $time = '0:0') Schedule the event on Tuesdays (Seshanbeh).
     * @method $this chaharshanbeh(string $time = '0:0') Schedule the event on Wednesdays (Chaharshanbeh).
     * @method $this panjshanbeh(string $time = '0:0') Schedule the event on Thursdays (Panjshanbeh).
     * @method $this jomeh(string $time = '0:0') Schedule the event on Fridays (Jomeh).
     */
    class Event {}
}
