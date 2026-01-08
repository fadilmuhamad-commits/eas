<?php

namespace App\Events;

use App\Models\M_Config;
use App\Models\M_Loket;
use App\Models\M_Tiket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class E_ShowWebsocket implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   */
  public $data;
  public function __construct()
  {
    $data['active'] = M_Tiket::where('status', 3)->orderBy('updated_at', 'asc')->get();
    $data['queue'] = M_Tiket::where('status', 2)->orderBy('updated_at', 'asc')->get();
    $data['lokets'] = M_Loket::with('Color')->get();
    $data['runningText'] = M_Config::value('running_text');

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
      new Channel('show'),
    ];
  }

  public function broadcastAs()
  {
    return 'WS_Show';
  }
}
