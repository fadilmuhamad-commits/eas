<?php

namespace App\Http\Controllers;

use App\Models\M_Counter_Category;
use App\Models\M_Ticket_Category;
use App\Models\M_Color;
use App\Models\M_Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class C_Category extends Controller
{
  public function index(Request $request)
  {
    $counter_category = M_Counter_Category::query();
    $ticket_category = M_Ticket_Category::query();

    // TYPE
    $data['type'] = $request->input('type');

    if (empty($data['type'])) {
      $data['type'] = 'loket';
    }
    // END TYPE

    // SEARCH
    $data['search'] = $request->input('search');
    $data['searchBy'] = $request->input('search_by');

    $search = $data['search'];
    $searchBy = $data['searchBy'];

    if (!empty($search) && empty($searchBy)) {
      $counter_category
        ->where('counter_categories.name', 'like', '%' . $search . '%')
        ->orWhere('counter_categories.code', 'like', '%' . $search . '%');

      $ticket_category
        ->where('ticket_categories.name', 'like', '%' . $search . '%')
        ->orWhere('ticket_categories.code', 'like', '%' . $search . '%');
    } else if (!empty($search) && !empty($searchBy)) {
      $counter_category
        ->where('counter_categories.' . $searchBy, 'like', '%' . $search . '%');

      $ticket_category
        ->where('ticket_categories.' . $searchBy, 'like', '%' . $search . '%');
    }
    // END SEARCH

    // SORT
    $data['sort'] = $request->input('sort');
    $data['order'] = $request->input('order');

    $sort = $data['sort'];
    $order = $data['order'];

    if (!empty($sort) && !empty($order)) {
      $counter_category->orderBy($sort, $order);
      $ticket_category->orderBy($sort, $order);
    } else {
      $counter_category->orderBy('counter_categories.id', 'asc');
      $ticket_category->orderBy('ticket_categories.id', 'asc');
    }
    // END SORT

    // PER PAGE
    $data['perPage'] = $request->input('perPage');

    if (empty($data['perPage']) || !is_numeric($data['perPage'])) {
      $data['perPage'] = 10;
    }
    // END PER PAGE

    $data['category'] = $data['type'] == 'loket' ? $counter_category->paginate($data['perPage']) : $ticket_category->paginate($data['perPage']);
    $data['length'] = $data['category']->total();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.category.category', $data);
  }

  public function add(Request $request)
  {
    $data['edit'] = false;
    $data['type'] = $request->input('type');
    $data['colors'] = M_Color::orderBy('updated_at', 'desc')->take(7)->get();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.category.category-tambah', $data);
  }

  public function edit(Request $request, $counter_category)
  {
    $data['edit'] = true;
    $data['type'] = $request->input('type');
    if ($data['type'] == 'loket') {
      $categoryModel = new M_Counter_Category();
    } else {
      $categoryModel = new M_Ticket_Category();
    }
    $data['category'] = $categoryModel->findOrFail($counter_category);
    $data['colors'] = M_Color::orderBy('updated_at', 'desc')->take(7)->get();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.category.category-tambah', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'nama-category' => 'required',
      'kode-category' => 'required',
      'color' => 'required'
    ], [
      'nama-category.required' => 'Nama category wajib diisi',
      'kode-category.required' => 'Kode category wajib diisi',
      'color.required' => 'Warna wajib dipilih'
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

    M_Counter_Category::create([
      'name' => $request->input('nama-category'),
      'code' => $request->input('kode-category'),
      'color_id' => $colorId
    ]);

    return redirect()->route('category', ['type' => 'loket'])->with('success', 'Category added successfully');
  }

  public function storeT(Request $request)
  {
    $request->validate([
      'nama-category' => 'required',
      'kode-category' => 'required',
    ], [
      'nama-category.required' => 'Nama category wajib diisi',
      'kode-category.required' => 'Kode category wajib diisi',
    ]);

    M_Ticket_Category::create([
      'name' => $request->input('nama-category'),
      'code' => $request->input('kode-category'),
    ]);

    return redirect()->route('category', ['type' => 'tiket'])->with('success', 'Category added successfully');
  }

  public function update(Request $request, M_Counter_Category $counter_category)
  {
    $request->validate([
      'nama-category' => 'required',
      'kode-category' => 'required',
      'color' => 'required'
    ], [
      'nama-category.required' => 'Nama category wajib diisi',
      'kode-category.required' => 'Kode category wajib diisi',
      'color.required' => 'Warna wajib dipilih'
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

    $counter_category->update([
      'name' => $request->input('nama-category'),
      'code' => $request->input('kode-category'),
      'color_id' => $colorId
    ]);

    return redirect()->route('category', ['type' => 'loket'])->with('success', 'Category updated successfully');
  }

  public function updateT(Request $request, M_Ticket_Category $counter_category)
  {
    $request->validate([
      'nama-category' => 'required',
      'kode-category' => 'required',
    ], [
      'nama-category.required' => 'Nama category wajib diisi',
      'kode-category.required' => 'Kode category wajib diisi',
    ]);

    $counter_category->update([
      'name' => $request->input('nama-category'),
      'code' => $request->input('kode-category')
    ]);

    return redirect()->route('category', ['type' => 'tiket'])->with('success', 'Category updated successfully');
  }

  public function destroy(M_Counter_Category $counter_category)
  {
    $tikets = $counter_category->hasManyTickets;

    foreach ($tikets as $tiket) {
      if ($tiket->status == 1) {
        $tiket->update(['counter_category_id' => null]);
      } else {
        $tiket->delete();
      }
    }

    $counter_category->delete();

    return redirect()->back()->with('success', 'Category deleted successfully');
  }

  public function destroyT(M_Ticket_Category $counter_category)
  {
    $tikets = $counter_category->hasManyTickets;

    foreach ($tikets as $tiket) {
      $tiket->update(['ticket_category_id' => null]);
    }

    $counter_category->delete();

    return redirect()->back()->with('success', 'Category deleted successfully');
  }

  public function categoryDestroySelected(Request $request)
  {
    $data['type'] = $request->input('type');
    if ($data['type'] == 'loket') {
      $categoryModel = new M_Counter_Category();
    } else {
      $categoryModel = new M_Ticket_Category();
    }

    $selectedRows = json_decode($request->input('selectedRows'));

    foreach ($selectedRows as $rowId) {
      $counter_category = $categoryModel->findOrFail($rowId);
      $tikets = $counter_category->hasManyTickets;

      if ($data['type'] == 'loket') {
        foreach ($tikets as $tiket) {
          if ($tiket->status == 1) {
            $tiket->update(['counter_category_id' => null]);
          } else {
            $tiket->delete();
          }
        }
      } else {
        foreach ($tikets as $tiket) {
          $tiket->update(['ticket_category_id' => null]);
        }
      }

      $counter_category->delete();
    }

    return redirect()->back()->with('success', count($selectedRows) . ' row(s) deleted successfully');
  }
}
