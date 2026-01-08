<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class E_CetakWebsocket implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   */
  public $data;
  public function __construct()
  {
    $data['loket'] = DB::table('loket')
      ->leftJoin('colors', 'loket.color_id', '=', 'colors.id')
      ->select('loket.*', 'colors.hexcode as color')
      ->orderBy('status', 'asc')
      ->get();

    $this->data = $data;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return array<int, \Illuminate\Broadcasting\Channel>
   */
  public function broadcastOn(): array
  {
    return [
      new Channel('cetak'),
    ];
  }

  public function broadcastAs()
  {
    return 'WS_Cetak';
  }
}
