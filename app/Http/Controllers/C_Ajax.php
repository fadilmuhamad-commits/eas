<?php

namespace App\Http\Controllers;

use App\Models\M_Counter_Category;
use App\Models\M_Config;
use App\Models\M_Group;
use App\Models\M_Counter;
use App\Models\M_Queue;
use App\Models\M_Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class C_Ajax extends Controller
{
  public function show(M_Group $group)
  {
    $data['counters'] = M_Counter::with('Color')->with(['hasManyTickets' => function ($query) {
      $query->with('Counter_Category')->where('status', 3);
    }])->where('group_id', $group->id)->get();

    $data['categories'] = M_Counter_Category::whereHas('Counters', function ($query) use ($group) {
      $query->where('group_id', $group->id);
    })->with(['hasManyTickets' => function ($query) {
      $query->where('status', 2)->orderBy('position', 'asc');
    }])->get();

    $data['group'] = M_Group::where('id', $group->id)->first();
    $data['runningText'] = M_Config::value('running_text');
    $data['antrian'] = M_Queue::where('group_id', $group->id)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->orderBy('created_at', 'asc')
      ->get();

    return response()->json($data, Response::HTTP_OK);
  }

  public function cetak()
  {
    $data['categories'] = M_Counter_Category::with('Counters')->with('hasManyTickets')
      ->leftJoin('colors', 'counter_categories.color_id', '=', 'colors.id')
      ->select('counter_categories.*', 'colors.hexcode as color')
      ->get();

    return response()->json($data, Response::HTTP_OK);
  }

  public function group()
  {
    $data['group'] = M_Group::get();

    return response()->json($data, Response::HTTP_OK);
  }

  public function queue(Request $request)
  {
    $user = auth()->user();
    $category = $request->category;

    if ($category) {
      $data['queue'] = M_Ticket::where('status', 2)->whereNotNull('tickets.queue_number')->with('Counter_Category')
        ->whereHas('Counter_Category', function ($query) use ($category) {
          $query->where('id', $category);
        })->orderBy('position', 'asc')->get();
    } else {
      $data['queue'] = M_Ticket::where('status', 2)->whereNotNull('tickets.queue_number')->with('Counter_Category')
        ->whereHas('Counter_Category', function ($query) use ($user) {
          $query->whereHas('Counters', function ($query1) use ($user) {
            $query1->where('counter_id', $user->Counter->id);
          });
        })->orderBy('position', 'asc')->get();
    }

    return response()->json($data, Response::HTTP_OK);
  }

  public function deleteAntrian(M_Queue $queue)
  {
    $queue->delete();

    return response()->json(Response::HTTP_OK);
  }
}
