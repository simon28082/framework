<?php

namespace CrCms\Foundation\Console;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Interface ScheduleDispatchContract
 * @package CrCms\Foundation\Console
 */
interface ScheduleDispatchContract
{
    /**
     * @param Schedule $schedule
     * @return void
     */
    public function handle(Schedule $schedule);
}