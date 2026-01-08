<?php

namespace App\Jobs;

use App\Models\M_Ticket;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupTikets implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function handle()
  {
    $twentyFourHoursAgo = Carbon::now()->subDay();

    M_Ticket::whereNull('queue_number')
      ->where('created_at', '<', $twentyFourHoursAgo)
      ->delete();
  }
}
