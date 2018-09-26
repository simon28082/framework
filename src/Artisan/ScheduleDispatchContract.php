<?php

namespace CrCms\Foundation\Artisan;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Interface ScheduleDispatchContract
 * @package CrCms\Foundation\Artisan
 */
interface ScheduleDispatchContract
{
    /**
     * @param Schedule $schedule
     * @return void
     */
    public function handle(Schedule $schedule): void;
}