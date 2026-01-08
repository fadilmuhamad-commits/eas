<?php

namespace App\Console;

use App\Jobs\CleanupTikets;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   */
  protected $commands = [
    // Other commands...
  ];

  protected function schedule(Schedule $schedule)
  {
    // $schedule->command('inspire')->hourly();
    // $schedule->job(new CleanupTikets)->everySecond();
  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
