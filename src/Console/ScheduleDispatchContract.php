<?php

namespace CrCms\Framework\Console;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Interface ScheduleDispatchContract
 * @package CrCms\Framework\Artisan
 */
interface ScheduleDispatchContract
{
    /**
     * @param Schedule $schedule
     * @return void
     */
    public function handle(Schedule $schedule): void;
}