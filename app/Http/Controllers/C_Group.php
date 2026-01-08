<?php

namespace App\Http\Controllers;

use App\Models\M_Config;
use App\Models\M_Group;
use Illuminate\Http\Request;

class C_Group extends Controller
{
  public function index(Request $request)
  {
    $group = M_Group::query();

    // SEARCH
    $data['search'] = $request->input('search');
    $search = $data['search'];

    if (!empty($search)) {
      $group
        ->where('group.name', 'like', '%' . $search . '%');
    }
    // END SEARCH

    // SORT
    $data['sort'] = $request->input('sort');
    $data['order'] = $request->input('order');

    $sort = $data['sort'];
    $order = $data['order'];

    if (!empty($sort) && !empty($order)) {
      $group->orderBy($sort, $order);
    } else {
      $group->orderBy('id', 'asc');
    }
    // END SORT

    // PER PAGE
    $data['perPage'] = $request->input('perPage');

    if (empty($data['perPage']) || !is_numeric($data['perPage'])) {
      $data['perPage'] = 10;
    }
    // END PER PAGE

    $data['group'] = $group->paginate($data['perPage']);
    $data['length'] = $data['group']->total();
    $data['config'] = M_Config::first();
    return view('pages.admin.group.group', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'group' => 'required',
    ], [
      'group.required' => 'Grup wajib memiliki nama',
    ]);

    M_Group::create([
      'name' => $request->input('group')
    ]);

    return redirect()->back()->with('success', 'Group added successfully');
  }

  public function update(Request $request, M_Group $group)
  {
    $request->validate([
      'group' => 'required',
    ], [
      'group.required' => 'Grup wajib memiliki nama',
    ]);

    $group->update([
      'name' => $request->input('group')
    ]);

    return redirect()->back()->with('success', 'Group updated successfully');
  }

  public function destroy(M_Group $group)
  {
    $counters = $group->hasManyCounters;

    foreach ($counters as $counter) {
      $counter->update(['group_id' => null]);
    }

    $group->delete();

    return redirect()->back()->with('success', 'Group deleted successfully');
  }

  public function destroySelected(Request $request)
  {
    $selectedRows = json_decode($request->input('selectedRows'));

    foreach ($selectedRows as $rowId) {
      $group = M_Group::findOrFail($rowId);
      $counters = $group->hasManyCounters;

      foreach ($counters as $counter) {
        $counter->update([
          'group_id' => null
        ]);
      }

      $group->delete();
    }

    return redirect()->back()->with('success', count($selectedRows) . ' row(s) deleted successfully');
  }
}
