<?php

namespace EFive\JalaliSchedule;

use Hekmatinasser\Verta\Verta;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Date;

class JalaliScheduleMacros
{
    /**
     * Register all Jalali scheduling macros onto Illuminate\Console\Scheduling\Event.
     */
    public static function register(): void
    {
        // -------------------------------------------------------------
        // Monthly Macros
        // -------------------------------------------------------------

        /**
         * Schedule the event to run on the 1st day of every Jalali month.
         */
        Event::macro('jalaliMonthly', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliMonthlyOn(1, $time);
        });

        Event::macro('monthlyJalali', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliMonthly($time);
        });

        /**
         * Schedule the event to run on a specific day of the Jalali month at a given time.
         */
        Event::macro('jalaliMonthlyOn', function (int $dayOfMonth = 1, string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->when(function () use ($dayOfMonth) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);

                return verta($date)->day === (int) $dayOfMonth;
            });
        });

        Event::macro('monthlyOnJalali', function (int $dayOfMonth = 1, string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliMonthlyOn($dayOfMonth, $time);
        });

        /**
         * Schedule the event to run twice monthly on specific Jalali days (defaults to 1st and 16th).
         */
        Event::macro('jalaliTwiceMonthly', function (int $first = 1, int $second = 16, string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->when(function () use ($first, $second) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);
                $day = verta($date)->day;

                return $day === (int) $first || $day === (int) $second;
            });
        });

        Event::macro('twiceMonthlyJalali', function (int $first = 1, int $second = 16, string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliTwiceMonthly($first, $second, $time);
        });

        /**
         * Schedule the event to run on the last day of the Jalali month.
         * Dynamically accounts for 31-day, 30-day, and 29/30-day (Esfand leap) months.
         */
        Event::macro('jalaliLastDayOfMonth', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->when(function () {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);
                $v = verta($date);

                return $v->day === (int) $v->daysInMonth;
            });
        });

        Event::macro('lastDayOfJalaliMonth', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliLastDayOfMonth($time);
        });

        /**
         * Schedule the event to run on specific days of the Jalali month.
         */
        Event::macro('jalaliDaysOfMonth', function (...$args) {
            /** @var Event $this */
            $time = '0:0';

            if (count($args) > 1 && is_string(end($args)) && str_contains(end($args), ':')) {
                $time = array_pop($args);
            }

            $days = (count($args) === 1 && is_array($args[0])) ? $args[0] : $args;
            $targetDays = array_map('intval', (array) $days);

            $this->dailyAt($time);

            return $this->when(function () use ($targetDays) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);

                return in_array(verta($date)->day, $targetDays, true);
            });
        });

        Event::macro('daysOfJalaliMonth', function (...$args) {
            /** @var Event $this */
            return $this->jalaliDaysOfMonth(...$args);
        });

        // -------------------------------------------------------------
        // Quarterly & Yearly Macros
        // -------------------------------------------------------------

        /**
         * Schedule the event to run on the 1st day of every Jalali quarter:
         * Farvardin 1 (Bahar), Tir 1 (Tabestan), Mehr 1 (Paeez), Dey 1 (Zemestan).
         */
        Event::macro('jalaliQuarterly', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliQuarterlyOn(1, $time);
        });

        Event::macro('quarterlyJalali', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliQuarterly($time);
        });

        /**
         * Schedule the event to run on a given day of the first month of each Jalali quarter (months 1, 4, 7, 10).
         */
        Event::macro('jalaliQuarterlyOn', function (int $dayOfQuarter = 1, string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->when(function () use ($dayOfQuarter) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);
                $v = verta($date);

                return in_array($v->month, [1, 4, 7, 10], true) && $v->day === (int) $dayOfQuarter;
            });
        });

        Event::macro('quarterlyOnJalali', function (int $dayOfQuarter = 1, string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliQuarterlyOn($dayOfQuarter, $time);
        });

        /**
         * Schedule the event to run yearly on Nowruz (Farvardin 1st) at the given time.
         */
        Event::macro('jalaliYearly', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliYearlyOn(1, 1, $time);
        });

        Event::macro('yearlyJalali', function (string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliYearly($time);
        });

        /**
         * Schedule the event to run yearly on a given Jalali month and day.
         */
        Event::macro('jalaliYearlyOn', function (int $month = 1, int $dayOfMonth = 1, string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->when(function () use ($month, $dayOfMonth) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);
                $v = verta($date);

                return $v->month === (int) $month && $v->day === (int) $dayOfMonth;
            });
        });

        Event::macro('yearlyOnJalali', function (int $month = 1, int $dayOfMonth = 1, string $time = '0:0') {
            /** @var Event $this */
            return $this->jalaliYearlyOn($month, $dayOfMonth, $time);
        });

        // -------------------------------------------------------------
        // Condition-only Filters (For custom frequencies, e.g. ->hourly()->whenJalaliDay(1))
        // -------------------------------------------------------------

        /**
         * Filter to only execute on specific Jalali day(s) of the month.
         */
        Event::macro('whenJalaliDay', function (array|int ...$days) {
            /** @var Event $this */
            $daysList = (count($days) === 1 && is_array($days[0])) ? $days[0] : $days;
            $targetDays = array_map('intval', (array) $daysList);

            return $this->when(function () use ($targetDays) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);

                return in_array(verta($date)->day, $targetDays, true);
            });
        });

        /**
         * Filter to only execute on the last day of the Jalali month.
         */
        Event::macro('whenJalaliLastDayOfMonth', function () {
            /** @var Event $this */
            return $this->when(function () {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);
                $v = verta($date);

                return $v->day === (int) $v->daysInMonth;
            });
        });

        /**
         * Filter to only execute in specific Jalali month(s).
         */
        Event::macro('whenJalaliMonth', function (array|int ...$months) {
            /** @var Event $this */
            $monthsList = (count($months) === 1 && is_array($months[0])) ? $months[0] : $months;
            $targetMonths = array_map('intval', (array) $monthsList);

            return $this->when(function () use ($targetMonths) {
                /** @var Event $this */
                $tz = $this->timezone ?: config('app.timezone', 'Asia/Tehran');
                $date = Date::now($tz);

                return in_array(verta($date)->month, $targetMonths, true);
            });
        });

        // -------------------------------------------------------------
        // Persian Weekday Helpers (Sat = 6, Sun = 0, Mon = 1, Tue = 2, Wed = 3, Thu = 4, Fri = 5)
        // -------------------------------------------------------------

        /**
         * Schedule the event on Iranian business days (Saturday through Wednesday: Shanbeh to Chaharshanbeh).
         */
        Event::macro('iranWeekdays', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->days(['6', '0', '1', '2', '3']);
        });

        /**
         * Schedule the event on Iranian weekend days (Thursday and Friday: Panjshanbeh and Jomeh).
         */
        Event::macro('iranWeekends', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->days(['4', '5']);
        });

        Event::macro('shanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->saturdays();
        });

        Event::macro('yekshanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->sundays();
        });

        Event::macro('doshanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->mondays();
        });

        Event::macro('seshanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->tuesdays();
        });

        Event::macro('chaharshanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->wednesdays();
        });

        Event::macro('panjshanbeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->thursdays();
        });

        Event::macro('jomeh', function (string $time = '0:0') {
            /** @var Event $this */
            $this->dailyAt($time);

            return $this->fridays();
        });
    }
}
