<?php

namespace App\Http\Controllers;

use App\Events\E_ShowWebsocket;
use App\Models\M_Counter_Category;
use App\Models\M_Color;
use App\Models\M_Config;
use App\Models\M_Counter;
use App\Models\M_Customer;
use App\Models\M_Permission;
use App\Models\M_Queue;
use App\Models\M_Role;
use App\Models\M_Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Glide\GlideImage;

class C_Admin extends Controller
{
  public function dashboard(Request $request)
  {
    $queryB = M_Ticket::query();
    $queryP = M_Ticket::query();
    $queryS = M_Ticket::query();
    $queryL = M_Ticket::query();

    // Data
    $booking = $queryB
      ->whereNotNull('booking_code');

    $pengunjung = $queryP
      ->whereNotNull('queue_number');

    $pelanggan = $queryS
      ->where('tickets.status', 1);

    // FILTER DATE
    $data['selectedDate'] = $request->input('date');
    $selectedDate = $data['selectedDate'];

    if (!empty($selectedDate)) {
      $monthIDN = [
        'Januari' => 'January',
        'Februari' => 'February',
        'Maret' => 'March',
        'April' => 'April',
        'Mei' => 'May',
        'Juni' => 'June',
        'Juli' => 'July',
        'Agustus' => 'August',
        'September' => 'September',
        'Oktober' => 'October',
        'November' => 'November',
        'Desember' => 'December',
      ];

      $selectedDate = strtr($selectedDate, $monthIDN);
      $carbonDate = Carbon::createFromFormat('d F Y', $selectedDate);

      $booking
        ->whereDay('tickets.created_at', '=', $carbonDate->format('d'))
        ->whereMonth('tickets.created_at', '=', $carbonDate->format('m'))
        ->whereYear('tickets.created_at', '=', $carbonDate->format('Y'));

      $pengunjung
        ->whereDay('tickets.created_at', '=', $carbonDate->format('d'))
        ->whereMonth('tickets.created_at', '=', $carbonDate->format('m'))
        ->whereYear('tickets.created_at', '=', $carbonDate->format('Y'));

      $pelanggan
        ->whereDay('tickets.created_at', '=', $carbonDate->format('d'))
        ->whereMonth('tickets.created_at', '=', $carbonDate->format('m'))
        ->whereYear('tickets.created_at', '=', $carbonDate->format('Y'));
    } else {
      $booking->whereDate('tickets.created_at', '=', date('Y-m-d'));
      $pengunjung->whereDate('tickets.created_at', '=', date('Y-m-d'));
      $pelanggan->whereDate('tickets.created_at', '=', date('Y-m-d'));
    }
    // END FILTER DATE

    // FILTER YEAR
    $data['selectedYear'] = $request->input('year');
    $selectedYear = $data['selectedYear'];

    if (empty($selectedYear)) {
      $selectedYear = date('Y');
    }
    // END FILTER YEAR

    // FILTER MONTH
    $data['selectedMonth'] = $request->input('month');
    $selectedMonth = $data['selectedMonth'];

    if (empty($selectedMonth)) {
      $selectedMonth = date('m');
    }

    $categoryL = $queryL
      ->leftJoin('counter_categories', 'tickets.counter_category_id', '=', 'counter_categories.id')
      ->select('counter_categories.id', 'counter_categories.name', DB::raw('COUNT(*) as tiket_count'))
      ->whereYear('tickets.created_at', $selectedYear)
      ->whereMonth('tickets.created_at', $selectedMonth)
      ->whereNotNull('queue_number')
      ->groupBy('counter_categories.id', 'counter_categories.name')
      ->orderBy('tiket_count', 'desc')
      ->take(3);
    // END FILTER MONTH

    // MONTHLY CHART DATA
    $monthlyCounts = M_Ticket
      ::select(DB::raw('MONTH(tickets.created_at) as month'), DB::raw('COUNT(*) as count'))
      ->whereYear('tickets.created_at', $selectedYear)
      ->groupBy(DB::raw('MONTH(tickets.created_at)'))
      ->get();

    $monthlyCountsArray = array_fill(1, 12, 0);

    foreach ($monthlyCounts as $count) {
      $monthlyCountsArray[$count->month] = $count->count;
    }
    // END MONTHLY CHART DATA

    $data['monthlyCounts'] = $monthlyCountsArray;
    $data['categoryL'] = $categoryL->get();
    $data['booking'] = $booking->get();
    $data['pengunjung'] = $pengunjung->get();
    $data['pelanggan'] = $pelanggan->get();
    $data['config'] = M_Config::first();
    return view('pages.admin.dashboard', $data);
  }

  public function ambilAntrian()
  {
    $data['categories'] = M_Counter_Category::leftJoin('colors', 'counter_categories.color_id', '=', 'colors.id')
      ->select('counter_categories.*', 'colors.hexcode as color')
      ->get();
    $data['config'] = M_Config::first();
    return view('pages.admin.ambil-antrian', $data);
  }

  public function panggilAntrian(Request $request)
  {
    $user = auth()->user();
    $config = M_Config::first();

    if ($user->Counter) {
      $ticket = M_Ticket::with('Counter_Category')
        ->where('counter_id', $user->Counter->id)
        ->where('status', 3)->first();

      $queue = M_Ticket::where('status', 2)->with('Counter_Category')
        ->whereHas('Counter_Category', function ($query) use ($user) {
          $query->whereHas('Counters', function ($query1) use ($user) {
            $query1->where('counter_id', $user->Counter->id);
          });
        })->orderBy('position', 'asc')->get();

      $calling = $ticket ? true : false;

      $categories = M_Counter_Category::whereHas('Counters', function ($query1) use ($user) {
        $query1->where('counter_id', $user->Counter->id);
      })->get();

      $selectedCategory = $request->selected_category;

      $data = [
        'tiket' => $ticket,
        'queue' => $queue,
        'calling' => $calling,
        'config' => $config,
        'categories' => $categories,
        'selectedCategory' => $selectedCategory
      ];

      return view('pages.admin.panggil-antrian', $data);
    }

    return redirect()->route('dashboard')->with('error', 'User harus memiliki loket');
  }

  public function updateStatus(Request $request, M_Counter $counter)
  {
    $newStatus = $request->input('switch_loket');
    $counter->update(['status' => $newStatus]);

    return redirect()->route('panggil-antrian');
  }

  public function updateQueue(Request $request)
  {
    $positions = $request->input('positions');

    try {
      foreach ($positions as $position) {
        $ticket = M_Ticket::find($position['id']);
        if ($ticket) {
          // check for ticket with same position
          $samePos = M_Ticket::where('position', $ticket->position)->get();
          if ($samePos) {
            foreach ($samePos as $i => $item) {
              $item->position += $i + 1;
              $item->save();
            }
          }

          $ticket->position = $position['position'];
          $ticket->save();
        }
      }
      return response()->json(['success' => 'Queue updated']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function submitAntrian(Request $request, M_Ticket $ticket)
  {
    $ticket->update([
      'status' => 1,
      'duration' => $request->seconds_res,
      'counter_category_code' => $ticket->Counter_Category->code,
      'ticket_category_name' => $ticket->Ticket_Category->name,
      'counter_name' => $ticket->Counter->name,
      'group_name' => $ticket->Counter->Group->name,
    ]);

    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return redirect()->route('panggil-antrian');
  }

  public function callAntrian(Request $request, M_Ticket $ticket)
  {
    $counter = Auth::user()->Counter->id;
    $kodeTiket = $ticket->Counter_Category->code . $ticket->queue_number;

    if ($ticket) {
      if ($ticket->status !== 3) {
        $minPosition = M_Ticket::where('status', 2)->min('position');
        if ($ticket->position !== $minPosition) {
          $affectedTikets = M_Ticket::where('status', 2)
            ->where('position', '>', $ticket->position)
            ->get();

          foreach ($affectedTikets as $affectedTiket) {
            $affectedTiket->position -= 1;
            $affectedTiket->save();
          }
        }

        $ticket->update([
          'status' => 3,
          'position' => null,
          'counter_id' => $counter
        ]);

        M_Queue::create([
          'code' => $kodeTiket,
          'group_id' => $ticket->Counter->Group->id
        ]);
        return redirect()->route('panggil-antrian', ['selected_category' => $request->selected_category]);
      } else {
        return redirect()->route('panggil-antrian', ['selected_category' => $request->selected_category])->with('error', 'Pengunjung sedang dalam panggilan');
      }
    } else {
      return redirect()->route('panggil-antrian', ['selected_category' => $request->selected_category]);
    }
    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
  }

  public function recallAntrian(M_Ticket $ticket)
  {
    $kodeTiket = $ticket->Counter_Category->code . $ticket->queue_number;

    M_Queue::create([
      'code' => $kodeTiket,
      'group_id' => $ticket->Counter->Group->id
    ]);

    return response()->json(['success' => 'Tiket berhasil dipanggil']);
  }

  public function nextAntrian(Request $request, M_Ticket $ticket)
  {
    $latestTikets = M_Ticket::where('status', '!=', 1)
      ->orderBy('position', 'desc')
      ->first();
    $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

    $duration = $request->seconds_res;

    if ($ticket->duration != 0 && $duration == 0) {
      $ticket->update([
        'status' => 1,
      ]);
    } else if ($ticket->duration == 0 && $duration == 0) {
      $ticket->update([
        'status' => 2,
        'position' => $newPosition,
        'counter_id' => null
      ]);
    } else {
      $ticket->update([
        'status' => 1,
        'duration' => $duration
      ]);
    }

    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return redirect()->route('panggil-antrian', ['selected_category' => $request->selected_category]);
  }

  public function noteAntrian(Request $request, M_Ticket $ticket)
  {
    $ticket->update([
      'note' => $request->input('note-input')
    ]);

    return response()->json(['message' => 'Catatan pengunjung updated successfully']);
  }

  public function identitasAntrian(Request $request, M_Ticket $ticket)
  {
    $request->validate([
      'name' => 'required',
      'email' => 'required|email',
      'phone_number' => 'required',
      'address' => 'required',
      'birth_place' => 'required',
      'birth_date' => 'required',
    ], [
      'name.required' => 'Username wajib diisi',
      'email.required' => 'Email wajib diisi',
      'phone_number.required' => 'No. Telp wajib diisi',
      'address.required' => 'Alamat wajib diisi',
      'birth_place.required' => 'Tempat Lahir wajib diisi',
      'birth_date.required' => 'Tanggal Lahir wajib diisi',
    ]);

    $pengunjung = $ticket->Customer;
    $existingPengunjung = M_Customer::where('email', $request->input('email'))
      ->orWhere('phone_number', $request->input('phone_number'))
      ->first();

    if ($existingPengunjung) {
      $ticket->update([
        'customer_id' => $existingPengunjung->id
      ]);
      $existingPengunjung->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'address' => $request->input('address'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
      ]);
      $pengunjung = $existingPengunjung;
    } else {
      do {
        $randomNoInduk = random_int(100000000, 999999999);
      } while (M_Customer::where('registration_code', $randomNoInduk)->exists());
      $newPengunjung = M_Customer::create([
        'registration_code' => $randomNoInduk,
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'address' => $request->input('address'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
      ]);

      $ticket->update([
        'customer_id' => $newPengunjung->id,
      ]);
      $pengunjung = $newPengunjung;
    }

    return response()->json(['message' => 'Pengunjung updated successfully', 'pengunjung' => $pengunjung]);
  }

  public function store(Request $request)
  {
    $latestTiket = M_Ticket::where('counter_category_id', $request->counter)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->orderBy('queue_number', 'desc')
      ->first();
    $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

    $latestTikets = M_Ticket::where('status', '!=', 1)
      ->orderBy('position', 'desc')
      ->first();
    $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;
    // $dataLoket = DB::table('loket')->where('id', '=', $request->counter)->first();

    // if ($dataLoket->status == 1) {
    $ticket = M_Ticket::create([
      'counter_category_id' => $request->counter,
      'categoryT_id' => 3,
      'queue_number' => $newNoAntrian,
      'position' => $newPosition
    ]);

    $affectedTikets = M_Ticket::where('status', 1)
      ->where('position', '>', $ticket->position)
      ->get();

    foreach ($affectedTikets as $affectedTiket) {
      $affectedTiket->position -= 1;
      $affectedTiket->save();
    }

    $token = hash('sha256', $ticket->id);
    session(['token' => $token]);
    session(['tiketId' => $ticket->id]);

    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return response()->json(['token' => $token]);
  }

  public function config()
  {
    $config = M_Config::first();
    $colors = M_Color::orderBy('updated_at', 'desc')->take(13)->get();
    return view('pages.admin.configuration', compact('config', 'colors'));
  }

  public function configUpdateStatus()
  {
    $config = M_Config::first();

    if ($config->status == 1) {
      $config->update([
        'status' => 2
      ]);
    } else if ($config->status == 2) {
      $config->update([
        'status' => 1
      ]);
    }

    return response()->json(['message' => 'Status config updated successfully']);
  }

  public function partnerUpdateStatus()
  {
    $config = M_Config::first();

    if ($config->partnership == 1) {
      $config->update([
        'partnership' => 2
      ]);
    } else if ($config->partnership == 2) {
      $config->update([
        'partnership' => 1
      ]);
    }

    return response()->json(['message' => 'Partnership status updated successfully']);
  }

  public function configStore(Request $request)
  {
    $request->validate([
      'nama-instansi' => 'required',
      'running-text' => 'required',
      'color' => 'required',
      'color1' => 'required',
      'color2' => 'required',
      'logo1' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
      'logo2' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
      'loading' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
      'partner_api' => 'nullable'
    ], [
      'nama-instansi.required' => 'Nama instansi wajib diisi',
      'running-text.required' => 'Running text wajib diisi',
      'logo1.image' => 'File harus berbentuk image',
      'logo1.mimes' => 'File harus berformat jpeg, png, jpg, gif, webp',
      'logo1.max' => 'File harus berukuran kurang dari 5 MB',
      'logo2.image' => 'File harus berbentuk image',
      'logo2.mimes' => 'File harus berformat jpeg, png, jpg, gif, webp',
      'logo2.max' => 'File harus berukuran kurang dari 5 MB',
      'loading.image' => 'File harus berbentuk image',
      'loading.mimes' => 'File harus berformat jpeg, png, jpg, gif, webp',
      'loading.max' => 'File harus berukuran kurang dari 5 MB',
    ]);

    $existingColor = M_Color::where('hexcode', $request->input('color'))->first();
    $existingColor1 = M_Color::where('hexcode', $request->input('color1'))->first();
    $existingColor2 = M_Color::where('hexcode', $request->input('color2'))->first();

    if ($existingColor) {
      $existingColor->touch();
      $colorId = $existingColor->id;
    } else {
      $newColor = M_Color::create([
        'hexcode' => $request->input('color')
      ]);
      $colorId = $newColor->id;
    }

    if ($existingColor1) {
      $existingColor1->touch();
      $colorId1 = $existingColor1->id;
    } else {
      $newColor1 = M_Color::create([
        'hexcode' => $request->input('color1')
      ]);
      $colorId1 = $newColor1->id;
    }

    if ($existingColor2) {
      $existingColor2->touch();
      $colorId2 = $existingColor2->id;
    } else {
      $newColor2 = M_Color::create([
        'hexcode' => $request->input('color2')
      ]);
      $colorId2 = $newColor2->id;
    }

    $config = M_Config::firstOrNew([]);
    $oldLogo1 = $config->logo1;
    $oldLogo2 = $config->logo2;
    $oldLoading = $config->loading;

    $config->color1_id = $colorId;
    $config->color2_id = $colorId1;
    $config->color3_id = $colorId2;
    $config->instance_name = $request->input('nama-instansi');
    $config->running_text = $request->input('running-text');
    $config->partner_api = $request->input('partner_api');

    if ($request->hasFile('logo1')) {
      $logo1Path = $request->file('logo1')->store('public/images');
      $config->logo1 = str_replace('public/', '', $logo1Path);

      // delete old image
      if ($oldLogo1 && file_exists(storage_path('app/public/' . $oldLogo1))) {
        unlink(storage_path('app/public/' . $oldLogo1));
      }

      // resize image
      $logo1File = $request->file('logo1');
      $extension = $logo1File->getClientOriginalExtension();
      if ($extension !== 'gif') {
        GlideImage::create(storage_path("app/{$logo1Path}"))
          ->modify(['w' => 400, 'h' => 400])
          ->save(storage_path("app/{$logo1Path}"));
      }
    }

    if ($request->hasFile('logo2')) {
      $logo2Path = $request->file('logo2')->store('public/images');
      $config->logo2 = str_replace('public/', '', $logo2Path);

      // delete old image
      if ($oldLogo2 && file_exists(storage_path('app/public/' . $oldLogo2))) {
        unlink(storage_path('app/public/' . $oldLogo2));
      }

      // resize image
      $logo2File = $request->file('logo2');
      $extension = $logo2File->getClientOriginalExtension();
      if ($extension !== 'gif') {
        GlideImage::create(storage_path("app/{$logo2Path}"))
          ->modify(['w' => 400, 'h' => 400])
          ->save(storage_path("app/{$logo2Path}"));
      }
    }

    if ($request->hasFile('loading')) {
      $loadingPath = $request->file('loading')->store('public/images');
      $config->loading = str_replace('public/', '', $loadingPath);

      // delete old image
      if ($oldLoading && file_exists(storage_path('app/public/' . $oldLoading))) {
        unlink(storage_path('app/public/' . $oldLoading));
      }

      // resize image
      $loadingFile = $request->file('loading');
      $extension = $loadingFile->getClientOriginalExtension();
      if ($extension !== 'gif') {
        GlideImage::create(storage_path("app/{$loadingPath}"))
          ->modify(['w' => 1024, 'h' => 1024])
          ->save(storage_path("app/{$loadingPath}"));
      }
    }

    $config->save();

    return redirect()->route('config')->with('success', 'Configuration updated successfully');
  }

  public function configDeleteImage(Request $request)
  {
    $config = M_Config::first();

    $image = $request->image;
    $oldImage = $config->$image;

    // delete old image
    if ($oldImage && file_exists(storage_path('app/public/' . $oldImage))) {
      unlink(storage_path('app/public/' . $oldImage));
    }

    $config->update([
      $image => null
    ]);

    return redirect()->route('config')->with('success', 'Image deleted successfully');
  }

  public function roles(Request $request)
  {
    $roles = M_Role::query();

    // SEARCH
    $data['search'] = $request->input('search');
    $search = $data['search'];

    if (!empty($search)) {
      $roles
        ->where('role.name', 'like', '%' . $search . '%');
    }
    // END SEARCH

    // SORT
    $data['sort'] = $request->input('sort');
    $data['order'] = $request->input('order');

    $sort = $data['sort'];
    $order = $data['order'];

    if (!empty($sort) && !empty($order)) {
      $roles->orderBy($sort, $order);
    } else {
      $roles->orderBy('id', 'asc');
    }
    // END SORT

    // PER PAGE
    $data['perPage'] = $request->input('perPage');

    if (empty($data['perPage']) || !is_numeric($data['perPage'])) {
      $data['perPage'] = 10;
    }
    // END PER PAGE

    $data['roles'] = $roles->paginate($data['perPage']);
    $data['length'] = $data['roles']->total();
    $data['config'] = M_Config::first();
    $data['permissionsTotal'] = M_Permission::count();
    return view('pages.admin.roles.roles', $data);
  }

  public function rolesAdd()
  {
    $data['edit'] = false;
    $data['permissions'] = M_Permission::get();
    $data['config'] = M_Config::first();

    return view('pages.admin.roles.roles-tambah', $data);
  }

  public function rolesEdit(M_Role $role)
  {
    $data['edit'] = true;
    $data['role'] = $role;
    $data['permissions'] = M_Permission::get();
    $data['config'] = M_Config::first();

    return view('pages.admin.roles.roles-tambah', $data);
  }

  public function rolesStore(Request $request)
  {
    $selectedPermissions = json_decode($request->input('permissions'));

    $request->validate([
      'name' => 'required',
      'permissions' => 'required'
    ], [
      'name.required' => 'Nama role wajib diisi',
      'permissions.required' => 'Role wajib memiliki permission',
    ]);

    $role = M_Role::create([
      'name' => $request->input('name')
    ]);
    $role->Permissions()->attach($selectedPermissions);

    return redirect()->route('roles')->with('success', 'Role added successfully');
  }

  public function rolesUpdate(Request $request, M_Role $role)
  {
    $request->validate([
      'name' => 'required',
      'permissions' => 'required'
    ], [
      'name.required' => 'Nama role wajib diisi',
      'permissions.required' => 'Role wajib memiliki permission',
    ]);

    $role->update([
      'name' => $request->input('name')
    ]);

    $role->Permissions()->detach();
    $selectedPermissions = json_decode($request->input('permissions'));
    $role->Permissions()->attach($selectedPermissions);

    return redirect()->route('roles')->with('success', 'Role updated successfully');
  }

  public function rolesDestroy(M_Role $role)
  {
    $users = $role->hasManyUsers;

    foreach ($users as $user) {
      $user->update([
        'role_id' => null,
        'counter_id' => null
      ]);
    }

    $role->delete();

    return redirect()->back()->with('success', 'Role deleted successfully');
  }

  public function rolesDestroySelected(Request $request)
  {
    $selectedRows = json_decode($request->input('selectedRows'));

    foreach ($selectedRows as $rowId) {
      $role = M_Role::findOrFail($rowId);
      $users = $role->hasManyUsers;

      foreach ($users as $user) {
        $user->update([
          'role_id' => null,
          'counter_id' => null
        ]);
      }

      $role->delete();
    }

    return redirect()->back()->with('success', count($selectedRows) . ' row(s) deleted successfully');
  }
}
