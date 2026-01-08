<?php

namespace App\Http\Controllers;

use App\Events\E_CetakWebsocket;
use App\Events\E_ShowWebsocket;
use App\Models\M_Counter_Category;
use App\Models\M_Color;
use App\Models\M_Config;
use App\Models\M_Group;
use App\Models\M_Counter;
use App\Models\M_Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class C_Loket extends Controller
{
  public function loket(Request $request)
  {
    $counter = M_Counter::query()
      ->leftJoin('relation_counter_categories', 'relation_counter_categories.counter_id', 'counters.id')
      ->leftJoin('counter_categories', 'relation_counter_categories.counter_category_id', 'counter_categories.id')
      ->leftJoin('groups', 'counters.group_id', 'groups.id')
      ->select('counters.*')
      ->groupBy(
        'counters.id',
        'counters.name',
        'counters.group_id',
        'counters.status',
        'counters.color_id',
        'counters.created_at',
        'counters.updated_at',
        'groups.id',
        'groups.name',
        'groups.created_at',
        'groups.updated_at',
      );

    // SEARCH
    $data['search'] = $request->input('search');
    $search = $data['search'];

    if (!empty($search)) {
      $counter
        ->where('counters.name', 'like', '%' . $search . '%')
        ->orWhere('counter_category.name', 'like', '%' . $search . '%')
        ->orWhere('groups.name', 'like', '%' . $search . '%');
    }
    // END SEARCH

    // SORT
    $data['sort'] = $request->input('sort');
    $data['order'] = $request->input('order');

    $sort = $data['sort'];
    $order = $data['order'];

    if (!empty($sort) && !empty($order)) {
      $counter->orderBy($sort, $order);
    } else {
      $counter->orderBy('counters.id', 'asc');
    }
    // END SORT

    // PER PAGE
    $data['perPage'] = $request->input('perPage');

    if (empty($data['perPage']) || !is_numeric($data['perPage'])) {
      $data['perPage'] = 10;
    }
    // END PER PAGE

    $data['counters'] = $counter->paginate($data['perPage']);
    $data['length'] = $data['counters']->total();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.loket.loket', $data);
  }

  public function loketTambah()
  {
    $data['edit'] = false;
    $data['colors'] = M_Color::orderBy('updated_at', 'desc')->take(8)->get();
    $data['counter_categories'] = M_Counter_Category::all();
    $data['group'] = M_Group::all();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.loket.loket-tambah', $data);
  }

  public function loketEdit(M_Counter $counter)
  {
    $data['edit'] = true;
    $data['counter'] = $counter;
    $data['colors'] = M_Color::orderBy('updated_at', 'desc')->take(8)->get();
    $data['counter_categories'] = M_Counter_Category::all();
    $data['group'] = M_Group::all();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.loket.loket-tambah', $data);
  }

  public function destroy(M_Counter $counter)
  {
    $users = $counter->hasManyUsers;
    $tiket = $counter->hasManyTickets;

    foreach ($users as $user) {
      $user->update(['counter_id' => null]);
    }

    foreach ($tiket as $tiketItem) {
      $tiketItem->update(['counter_id' => null]);
    }

    $counter->delete();

    // try {
    //   Broadcast(new E_ShowWebsocket);
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }

    return redirect()->back()->with('success', 'Loket deleted successfully');
  }

  public function destroySelected(Request $request)
  {
    $selectedRows = json_decode($request->input('selectedRows'));

    foreach ($selectedRows as $rowId) {
      $counter = M_Counter::findOrFail($rowId);
      $users = $counter->hasManyUsers;
      $tiket = $counter->hasManyTickets;

      foreach ($users as $user) {
        $user->update(['counter_id' => null]);
      }

      foreach ($tiket as $tiketItem) {
        $tiketItem->update(['counter_id' => null]);
      }

      $counter->delete();
    }

    // try {
    //   Broadcast(new E_ShowWebsocket);
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }

    return redirect()->back()->with('success', count($selectedRows) . ' row(s) deleted successfully');
  }

  public function store(Request $request)
  {
    $selectedCategories = json_decode($request->input('categories'));

    $request->validate([
      'nama-loket' => 'required',
      'color' => 'required',
      'categories' => 'required',
      'radio-group' => 'required'
    ], [
      'nama-counters.required' => 'Nama loket wajib diisi',
      'color.required' => 'Warna wajib dipilih',
      'categories.required' => 'Loket wajib memiliki kategori',
      'radio-group.required' => 'Loket wajib memiliki grup'
    ]);
    $existingColor = M_Color::where('hexcode', $request->input('color'))->first();

    if ($existingColor) {
      $existingColor->touch();
      $colorId = $existingColor->id;
    } else {
      $newColor = M_Color::create([
        'hexcode' => $request->input('color')
      ]);

      $colorId = $newColor->id;
    }

    $counter = M_Counter::create([
      'name' => $request->input('nama-loket'),
      'status' => 2,
      'color_id' => $colorId,
      'group_id' => $request->input('radio-group')
    ]);
    $counter->Categories()->attach($selectedCategories);

    // try {
    //   Broadcast(new E_ShowWebsocket);
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return redirect()->route('loket')->with('success', 'Loket added successfully');
  }

  public function update(Request $request, M_Counter $counter)
  {
    $request->validate([
      'nama-loket' => 'required',
      'color' => 'required',
      'categories' => 'required',
      'radio-group' => 'required'
    ], [
      'nama-counters.required' => 'Nama loket wajib diisi',
      'color.required' => 'Warna wajib dipilih',
      'categories.required' => 'Loket wajib memiliki kategori',
      'radio-group.required' => 'Loket wajib memiliki grup'
    ]);

    $existingColor = M_Color::where('hexcode', $request->input('color'))->first();

    if ($existingColor) {
      $existingColor->touch();
      $colorId = $existingColor->id;
    } else {
      $newColor = M_Color::create([
        'hexcode' => $request->input('color')
      ]);

      $colorId = $newColor->id;
    }

    $counter->update([
      'name' => $request->input('nama-loket'),
      'color_id' => $colorId,
      'group_id' => $request->input('radio-group')
    ]);

    $counter->Categories()->detach();
    $selectedCategories = json_decode($request->input('categories'));
    $counter->Categories()->attach($selectedCategories);

    // try {
    //   Broadcast(new E_ShowWebsocket);
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return redirect()->route('loket')->with('success', 'Loket updated successfully');
  }

  public function updateStatus(Request $request, M_Counter $counter)
  {
    $newStatus = $request->input('switch_loket');
    $counter->update(['status' => $newStatus]);

    // try {
    //   Broadcast(new E_ShowWebsocket);
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return response()->json(['message' => 'Status ' . $counter->name . ' updated successfully']);
  }
}
