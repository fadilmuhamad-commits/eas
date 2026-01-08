<?php

namespace App\Http\Controllers;

use App\Models\M_Counter_Category;
use App\Models\M_Config;
use App\Models\M_Group;
use App\Models\M_Counter;
use App\Models\M_Customer;
use App\Models\M_Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class C_Client extends Controller
{
  public function generatePdf(Request $request)
  {
    $token = session('token');
    $tiketId = session('tiketId');
    $token = $request->token;
    if ($token !== session('token') || !$tiketId) {
      return response()->json(['message' => 'Forbidden']);
    }

    $tiket = M_Ticket::findOrFail($tiketId);
    $counterCategory = M_Counter_Category::where('counter_categories.id', $tiket->counter_category_id)->first();
    $config = M_Config::first();
    $date = Carbon::parse(date('d F Y'))->translatedFormat('d F Y');
    $time = Carbon::parse(date('H:i'))->translatedFormat('H:i');
    $pengunjung = M_Customer::where('id', $tiket->customer_id)->first();

    $data = [
      'categoryL' => $counterCategory,
      'tiket' =>  $tiket,
      'config' => $config,
      'date' => $date,
      'time' => $time,
      'pengunjung' => $pengunjung ?? null
    ];

    $pdf = Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif'])->loadView('pdf.tiket', $data);

    $fileName = date('dmy') . '_' . $counterCategory->code . '-' . $tiket->queue_number . '.pdf';
    $filePath = 'public/pdf/' . $fileName;
    Storage::put($filePath, $pdf->output());

    return response()->json(['url' => Storage::url($filePath)], Response::HTTP_OK);
  }

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
    $data['logo1'] = M_Config::value('logo1');
    $data['logo2'] = M_Config::value('logo2');
    $data['nama_instansi'] = M_Config::value('instance_name');
    $data['config'] = M_Config::first();
    $data['audio'] = [
      '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
      'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    ];

    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return view('pages.show-antrian.show', $data);
  }

  public function cetak()
  {
    $data['categories'] = M_Counter_Category::leftJoin('colors', 'counter_categories.color_id', '=', 'colors.id')
      ->select('counter_categories.*', 'colors.hexcode as color')
      ->get();
    $data['runningText'] = M_Config::value('running_text');
    $data['logo1'] = M_Config::value('logo1');
    $data['logo2'] = M_Config::value('logo2');
    $data['nama_instansi'] = M_Config::value('instance_name');
    $data['config'] = M_Config::first();
    $data['counters'] = M_Counter_Category::first();

    if ($data['counters']) {
      if ($data['counters']->Counters->isNotEmpty()) {
        $data['counterStatusId'] = $data['counters']->Counters->first()->status;
      } else {
        $data['counterStatusId'] = null;
      }
    } else {
      $data['counterStatusId'] = null;
    }

    // try {
    //   Broadcast(new E_CetakWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return view('pages.cetak-antrian.ambil', $data);
  }

  public function tanyaOpsi(M_Counter_Category $counter_category)
  {
    $data['counter_category'] =  M_Counter_Category::where('id', '=', $counter_category->id)->first();
    $data['config'] = M_Config::first();

    // if ($data['loket']->status === 1) {
    return view('pages.cetak-antrian.pilih-booking', $data);
    // } else {
    // return redirect()->route('cetak')->with('error', 'Loket sedang tidak beroperasi.');
    // }
  }

  public function tanyaBooking(Request $request, M_Counter_Category $counter_category)
  {
    $data['counter_category'] = DB::table('counter_categories')
      ->where('id', '=', $counter_category->id)
      ->first();
    $data['config'] = M_Config::first();
    $data['counterLength'] = M_Counter_Category::get()->count();
    $data['type'] = $request->type;

    // if ($data['loket']->status === 1) {
    return view('pages.cetak-antrian.masukkan-nomor-booking', $data);
    // } else {
    //   return redirect()->route('cetak')->with('error', 'Loket sedang tidak beroperasi.');
    // }
  }

  public function wait(Request $request)
  {
    $tiketId = session('cetak_tiket_id');
    $token = $request->token;
    if ($token !== session('cetak_token') || !$tiketId) {
      return redirect()->route('cetak')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    // TOKEN
    session(['token' => $token]);
    session(['tiketId' => $tiketId]);

    $data['token'] = $token;
    $data['ticket'] = M_Ticket::findOrFail($tiketId);
    $data['config'] = M_Config::first();

    return view('pages.cetak-antrian.wait', $data);
  }

  public function success(Request $request)
  {
    $token = session('token');
    $tiketId = session('tiketId');
    $token = $request->token;
    if ($token !== session('token') || !$tiketId) {
      return redirect()->route('cetak')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    $data['ticket'] = M_Ticket::findOrFail($tiketId);
    $data['counterCategory'] = DB::table('counter_categories')
      ->leftJoin('colors', 'counter_categories.color_id', '=', 'colors.id')
      ->select('counter_categories.*', 'colors.hexcode as color')
      ->where('counter_categories.id', '=', $data['ticket']->counter_category_id)
      ->first();
    $data['config'] = M_Config::first();

    return view('pages.cetak-antrian.success', $data);
  }

  public function booking()
  {
    $data['instanceName'] = M_Config::value('instance_name');
    $data['logo1'] = M_Config::value('logo1');
    $data['config'] = M_Config::first();
    $data['loket'] = M_Counter_Category::all();
    $data['config'] = M_Config::first();
    $data['category'] = M_Counter_Category::first();

    return view('pages.booking-antrian.input', $data);
  }

  public function submitBooking(Request $request)
  {
    $request->validate([
      'name' => 'required',
      'email' => 'required|email',
      'phone_number' => 'required',
      'birth_place' => 'required',
      'birth_date' => 'required',
      'address' => 'required',
      'counters' => 'required'
    ], [
      'name.required' => 'Nama wajib diisi',
      'email.required' => 'Email wajib diisi',
      'phone_number.required' => 'No. Telepon wajib diisi',
      'birth_place.required' => 'Tempat lahir wajib diisi',
      'birth_date.required' => 'Tanggal lahir wajib diisi',
      'address.required' => 'Alamat wajib diisi',
      'counters.required' => 'Kategori wajib diisi',
    ]);

    $existingPengunjung = M_Customer::where('email', $request->input('email'))
      ->orWhere('phone_number', $request->input('phone_number'))
      ->first();
    if ($existingPengunjung) {
      $pengunjung = $existingPengunjung;
      $pengunjung->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
        'address' => $request->input('address')
      ]);
    } else {
      do {
        $randomNoInduk = random_int(100000000, 999999999);
      } while (M_Customer::where('registration_code', $randomNoInduk)->exists());
      $pengunjung = M_Customer::create([
        'registration_code' => $randomNoInduk,
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
        'address' => $request->input('address')
      ]);
    }
    $existingTiket = M_Ticket::where('customer_id', $pengunjung->id)
      ->where('booking_code', '<>', null)
      ->where('status', '!=', 1)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->first();

    if ($existingTiket) {
      return redirect()->route('booking')->with('error', 'Anda sudah membuat tiket hari ini, coba lagi nanti.');
    }

    $randomCode = strtoupper(Str::random(6));

    $latestTiket = M_Ticket::where('counter_category_id', $request->counters)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->orderBy('queue_number', 'desc')
      ->first();
    $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

    $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
      ->where('status', '!=', 1)
      ->orderBy('position', 'desc')
      ->first();
    $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

    $tiket = M_Ticket::create([
      'booking_code' => $randomCode,
      'customer_id' => $pengunjung->id,
      'counter_category_id' => $request->input('counters'),
      'ticket_category_id' => 2,
      'queue_number' => $newNoAntrian,
      'status' => 4,
      'position' => $newPosition
    ]);

    $token = hash('sha256', $tiket->id);
    session(['booking_token' => $token]);
    session(['booking_tiket_id' => $tiket->id]);

    $affectedTikets = M_Ticket::where('status', 1)
      ->where('position', '>', $tiket->position)
      ->get();

    foreach ($affectedTikets as $affectedTiket) {
      $affectedTiket->position -= 1;
      $affectedTiket->save();
    }

    $data['tiket'] = M_Ticket::findOrFail($tiket->id);
    $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
    $data['logo1'] = M_Config::value('logo1');
    $data['instanceName'] = M_Config::value('instance_name');

    // Mail::to($data['pengunjung']->email)->send(new RegisterMail($data));
    return redirect()->route('booking.success', ['token' => $token]);
  }


  public function bookingSuccess(Request $request)
  {
    $tiketId = session('booking_tiket_id');
    $token = $request->token;

    if ($token !== session('booking_token') || !$tiketId) {
      return redirect()->route('booking')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    $data['tiket'] = M_Ticket::findOrFail($tiketId);
    $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
    $data['logo1'] = M_Config::value('logo1');
    $data['instanceName'] = M_Config::value('instance_name');
    $data['config'] = M_Config::first();

    return view('pages.booking-antrian.success', $data);
  }

  public function tiket(M_Counter_Category $counter_category)
  {
    $latestTiket = M_Ticket::where('counter_category_id', $counter_category->id)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->orderBy('queue_number', 'desc')
      ->first();
    $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

    $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
      ->where('status', '!=', 1)
      ->orderBy('position', 'desc')
      ->first();
    $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;
    // $dataLoket = DB::table('loket')->where('id', '=', $counter_category)->first();

    // if ($dataLoket->status == 1) {
    $tiket = M_Ticket::create([
      'counter_category_id' => $counter_category->id,
      'ticket_category_id' => 1,
      'queue_number' => $newNoAntrian,
      'position' => $newPosition
    ]);

    // TOKEN
    $token = hash('sha256', $tiket->id);
    session(['cetak_token' => $token]);
    session(['cetak_tiket_id' => $tiket->id]);

    $affectedTikets = M_Ticket::where('status', 1)
      ->where('position', '>', $tiket->position)
      ->get();

    foreach ($affectedTikets as $affectedTiket) {
      $affectedTiket->position -= 1;
      $affectedTiket->save();
    }

    // try {
    //   Broadcast(new E_ShowWebsocket);
    // } catch (\Exception $e) {
    //   Log::error('Pusher broadcast error: ' . $e->getMessage());
    // }
    return redirect()->route('wait', ['token' => $token]);
    // } else {
    //   return redirect()->route('cetak')->with('error', 'Loket sedang tidak beroperasi.');
    // }
  }

  public function tiketForm(Request $request, M_Counter_Category $counter_category)
  {
    $request->validate([
      'name' => 'required',
      'email' => 'required|email',
      'phone_number' => 'required',
      'birth_place' => 'required',
      'birth_date' => 'required',
      'address' => 'required'
    ], [
      'name.required' => 'Nama wajib diisi',
      'email.required' => 'Email wajib diisi',
      'phone_number.required' => 'No. Telepon wajib diisi',
      'birth_place.required' => 'Tempat lahir wajib diisi',
      'birth_date.required' => 'Tanggal lahir wajib diisi',
      'address.required' => 'Alamat wajib diisi'
    ]);

    $existingPengunjung = M_Customer::where('email', $request->input('email'))
      ->orWhere('phone_number', $request->input('phone_number'))
      ->first();
    if ($existingPengunjung) {
      $pengunjung = $existingPengunjung;
      $pengunjung->update([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
        'address' => $request->input('address')
      ]);
    } else {
      do {
        $randomNoInduk = random_int(100000000, 999999999);
      } while (M_Customer::where('registration_code', $randomNoInduk)->exists());
      $pengunjung = M_Customer::create([
        'registration_code' => $randomNoInduk,
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'phone_number' => $request->input('phone_number'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
        'address' => $request->input('address')
      ]);
    }

    $latestTiket = M_Ticket::where('counter_category_id', $counter_category->id)
      ->whereDate('created_at', '=', date('Y-m-d'))
      ->orderBy('queue_number', 'desc')
      ->first();
    $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

    $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
      ->where('status', '!=', 1)
      ->orderBy('position', 'desc')
      ->first();
    $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

    $tiket = M_Ticket::create([
      'customer_id' => $pengunjung->id,
      'counter_category_id' => $counter_category->id,
      'ticket_category_id' => 1,
      'queue_number' => $newNoAntrian,
      'position' => $newPosition
    ]);

    // TOKEN
    $token = hash('sha256', $tiket->id);
    session(['cetak_token' => $token]);
    session(['cetak_tiket_id' => $tiket->id]);

    $affectedTikets = M_Ticket::where('status', 1)
      ->where('position', '>', $tiket->position)
      ->get();

    foreach ($affectedTikets as $affectedTiket) {
      $affectedTiket->position -= 1;
      $affectedTiket->save();
    }

    $data['tiket'] = M_Ticket::findOrFail($tiket->id);
    $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
    $data['logo1'] = M_Config::value('logo1');
    $data['instanceName'] = M_Config::value('instance_name');

    // Mail::to($data['pengunjung']->email)->send(new KiosMail($data));
    return redirect()->route('wait', ['token' => $token]);
  }

  public function cetakByNomorBooking(Request $request)
  {
    $nomorBooking = $request->input('booking_code');

    $tiket = M_Ticket::where('booking_code', $nomorBooking)->first();

    if ($tiket && $nomorBooking == $tiket->booking_code && $nomorBooking !== null && date('Y-m-d') == date('Y-m-d', strtotime($tiket->created_at))) {
      // if ($tiket->Loket->status === 1 && $tiket->status === 4) {
      if ($tiket->status === 4) {
        $tiket->timestamps = false;
        $tiket->update([
          'status' => 2
        ]);
        $tiket->timestamps = true;

        // TOKEN
        $token = hash('sha256', $tiket->id);
        session(['cetak_token' => $token]);
        session(['cetak_tiket_id' => $tiket->id]);

        // try {
        //   Broadcast(new E_ShowWebsocket);
        // } catch (\Exception $e) {
        //   Log::error('Pusher broadcast error: ' . $e->getMessage());
        // }

        return redirect()->route('wait', ['token' => $token]);
        // } else if ($tiket->Loket->status === 1 && $tiket->status !== 4) {
      } else if ($tiket->status !== 4) {
        return redirect()->back()->with('error', 'Nomor Booking sudah diaktivasi.');
        // } else {
        //   return redirect()->route('cetak')->with('error', 'Loket sedang tidak beroperasi.');
      }
    } else {
      return redirect()->back()->with('error', 'Nomor Booking tidak valid.');
    }
  }

  public function cetakWithNomorRegis(Request $request, M_Counter_Category $counter_category)
  {
    $nomorRegis = $request->input('no_regis');
    $pengunjung = M_Customer::where('registration_code', $nomorRegis)->first();

    if ($pengunjung && $nomorRegis !== null) {
      $latestTiket = M_Ticket::where('counter_category_id', $counter_category->id)
        ->whereDate('created_at', '=', date('Y-m-d'))
        ->orderBy('queue_number', 'desc')
        ->first();
      $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

      $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
        ->where('status', '!=', 1)
        ->orderBy('position', 'desc')
        ->first();
      $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;
      // $dataLoket = DB::table('loket')->where('id', '=', $counter_category)->first();

      // if ($dataLoket->status == 1) {
      $tiket = M_Ticket::create([
        'counter_category_id' => $counter_category->id,
        'ticket_category_id' => 1,
        'queue_number' => $newNoAntrian,
        'customer_id' => $pengunjung->id,
        'position' => $newPosition
      ]);

      // TOKEN
      $token = hash('sha256', $tiket->id);
      session(['cetak_token' => $token]);
      session(['cetak_tiket_id' => $tiket->id]);

      $affectedTikets = M_Ticket::where('status', 1)
        ->where('position', '>', $tiket->position)
        ->get();

      foreach ($affectedTikets as $affectedTiket) {
        $affectedTiket->position -= 1;
        $affectedTiket->save();
      }

      // try {
      //   Broadcast(new E_ShowWebsocket);
      // } catch (\Exception $e) {
      //   Log::error('Pusher broadcast error: ' . $e->getMessage());
      // }

      return redirect()->route('wait', ['token' => $token]);
      // } else {
      //   return redirect()->route('cetak')->with('error', 'Loket sedang tidak beroperasi.');
      // }
    } else {
      return redirect()->back()->with('error', 'Nomor Registrasi tidak valid.');
    }
  }

  public function cetakWithNomorAnggota(Request $request, M_Counter_Category $counter_category)
  {
    $userData = json_decode($request->userData);
    $pengunjung = M_Customer::where('registration_code', $userData->registration_code)->first();

    if ($pengunjung) {
      $latestTiket = M_Ticket::where('counter_category_id', $counter_category->id)
        ->whereDate('created_at', '=', date('Y-m-d'))
        ->orderBy('queue_number', 'desc')
        ->first();
      $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

      $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
        ->where('status', '!=', 1)
        ->orderBy('position', 'desc')
        ->first();
      $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

      $tiket = M_Ticket::create([
        'counter_category_id' => $counter_category->id,
        'ticket_category_id' => 1,
        'queue_number' => $newNoAntrian,
        'customer_id' => $pengunjung->id,
        'position' => $newPosition
      ]);

      // TOKEN
      $token = hash('sha256', $tiket->id);
      session(['cetak_token' => $token]);
      session(['cetak_tiket_id' => $tiket->id]);

      $affectedTikets = M_Ticket::where('status', 1)
        ->where('position', '>', $tiket->position)
        ->get();

      foreach ($affectedTikets as $affectedTiket) {
        $affectedTiket->position -= 1;
        $affectedTiket->save();
      }

      return response()->json(['token' => $token], Response::HTTP_OK);
    } else {
      $newPengunjung = M_Customer::create([
        'registration_code' => $userData->registration_code,
        'name' => $userData->name,
        'email' => $userData->email,
        'address' => $userData->address,
        'phone_number' => $userData->phone_number,
        'birth_date' => $userData->birth_date,
        'birth_place' => $userData->birth_place,
        'type' => 'partner'
      ]);

      $latestTiket = M_Ticket::where('counter_category_id', $counter_category->id)
        ->whereDate('created_at', '=', date('Y-m-d'))
        ->orderBy('queue_number', 'desc')
        ->first();
      $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;

      $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
        ->where('status', '!=', 1)
        ->orderBy('position', 'desc')
        ->first();
      $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

      $tiket = M_Ticket::create([
        'counter_category_id' => $counter_category->id,
        'ticket_category_id' => 1,
        'queue_number' => $newNoAntrian,
        'customer_id' => $newPengunjung->id,
        'position' => $newPosition
      ]);

      // TOKEN
      $token = hash('sha256', $tiket->id);
      session(['cetak_token' => $token]);
      session(['cetak_tiket_id' => $tiket->id]);

      $affectedTikets = M_Ticket::where('status', 1)
        ->where('position', '>', $tiket->position)
        ->get();

      foreach ($affectedTikets as $affectedTiket) {
        $affectedTiket->position -= 1;
        $affectedTiket->save();
      }

      return response()->json(['token' => $token], Response::HTTP_OK);
    }
  }

  public function ambilBookingByRegis(Request $request)
  {
    $request->validate([
      'registration_code' => 'required',
      'birth_date' => 'required',
      'counters' => 'required'
    ], [
      'registration_code.required' => 'Nomor induk wajib diisi',
      'birth_date.required' => 'Tanggal lahir wajib diisi',
      'counters.required' => 'Kategori wajib diisi'
    ]);

    $noInduk = $request->input('registration_code');
    $tglLahir = $request->input('birth_date');

    if ($noInduk && $tglLahir) {
      $pengunjung = M_Customer::where('registration_code', $noInduk)->first();

      if ($pengunjung && $pengunjung->type == 'default') {
        Session::flash('registration_code', $request->input('registration_code'));

        $tanggalLhr = $pengunjung->birth_date ?? 0;

        if ($tglLahir == $tanggalLhr) {
          $existingTicket = M_Ticket::where('customer_id', $pengunjung->id)
            ->where('status', '!=', 1)
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->first();

          if ($existingTicket) {
            return redirect()->back()->with('error', 'Anda sudah membuat tiket hari ini, coba lagi nanti.');
          }

          $randomCode = strtoupper(Str::random(6));
          $latestTiket = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))->orderBy('queue_number', 'desc')->first();
          $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;
          $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
            ->where('status', '!=', 1)
            ->orderBy('position', 'desc')
            ->first();
          $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

          $tiket = M_Ticket::create([
            'booking_code' => $randomCode,
            'customer_id' => $pengunjung->id,
            'counter_category_id' => $request->input('counters'),
            'ticket_category_id' => 2,
            'queue_number' => $newNoAntrian,
            'status' => 4,
            'position' => $newPosition
          ]);

          $token = hash('sha256', $tiket->id);
          session(['booking_token' => $token]);
          session(['booking_tiket_id' => $tiket->id]);

          $affectedTikets = M_Ticket::where('status', 1)
            ->where('position', '>', $tiket->position)
            ->get();

          foreach ($affectedTikets as $affectedTiket) {
            $affectedTiket->position -= 1;
            $affectedTiket->save();
          }

          $data['tiket'] = M_Ticket::findOrFail($tiket->id);
          $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
          $data['logo1'] = M_Config::value('logo1');
          $data['instanceName'] = M_Config::value('instance_name');

          // Mail::to($data['pengunjung']->email)->send(new RegisterMail($data));
          return redirect()->route('booking.success', ['token' => $token]);
        } else {
          return redirect()->back()->with('error', 'Tanggal lahir salah');
        }
      } else {
        return redirect()->back()->with('error', 'Pengunjung dengan nomor regis ' . $noInduk . ' tidak ditemukan.');
      }
    }
  }

  public function ambilBookingByAnggota(Request $request)
  {
    $userData = json_decode($request->userData);
    $pengunjung = M_Customer::where('registration_code', $userData->registration_code)->first();

    if ($pengunjung) {
      $existingTicket = M_Ticket::where('customer_id', $pengunjung->id)
        ->where('status', '!=', 1)
        ->whereDate('created_at', '=', date('Y-m-d'))
        ->first();

      if ($existingTicket) {
        return response()->json(['booked_today' => true], Response::HTTP_OK);
      }

      $randomCode = strtoupper(Str::random(6));
      $latestTiket = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))->orderBy('queue_number', 'desc')->first();
      $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;
      $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
        ->where('status', '!=', 1)
        ->orderBy('position', 'desc')
        ->first();
      $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

      $tiket = M_Ticket::create([
        'booking_code' => $randomCode,
        'customer_id' => $pengunjung->id,
        'counter_category_id' => $userData->counter_category_id,
        'ticket_category_id' => 2,
        'queue_number' => $newNoAntrian,
        'status' => 4,
        'position' => $newPosition
      ]);

      $token = hash('sha256', $tiket->id);
      session(['booking_token' => $token]);
      session(['booking_tiket_id' => $tiket->id]);

      $affectedTikets = M_Ticket::where('status', 1)
        ->where('position', '>', $tiket->position)
        ->get();

      foreach ($affectedTikets as $affectedTiket) {
        $affectedTiket->position -= 1;
        $affectedTiket->save();
      }

      $data['tiket'] = M_Ticket::findOrFail($tiket->id);
      $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
      $data['logo1'] = M_Config::value('logo1');
      $data['instanceName'] = M_Config::value('instance_name');

      // Mail::to($data['pengunjung']->email)->send(new RegisterMail($data));
      return response()->json(['token' => $token], Response::HTTP_OK);
    } else {
      $newPengunjung = M_Customer::create([
        'registration_code' => $userData->registration_code,
        'name' => $userData->name,
        'email' => $userData->email,
        'address' => $userData->address,
        'phone_number' => $userData->phone_number,
        'birth_date' => $userData->birth_date,
        'birth_place' => $userData->birth_place,
        'type' => 'partner'
      ]);

      $randomCode = strtoupper(Str::random(6));
      $latestTiket = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))->orderBy('queue_number', 'desc')->first();
      $newNoAntrian = $latestTiket ? $latestTiket->queue_number + 1 : 1;
      $latestTikets = M_Ticket::whereDate('created_at', '=', date('Y-m-d'))
        ->where('status', '!=', 1)
        ->orderBy('position', 'desc')
        ->first();
      $newPosition = $latestTikets ? $latestTikets->position + 1 : 1;

      $tiket = M_Ticket::create([
        'booking_code' => $randomCode,
        'customer_id' => $newPengunjung->id,
        'counter_category_id' => $userData->counter_category_id,
        'ticket_category_id' => 2,
        'queue_number' => $newNoAntrian,
        'status' => 4,
        'position' => $newPosition
      ]);

      $token = hash('sha256', $tiket->id);
      session(['booking_token' => $token]);
      session(['booking_tiket_id' => $tiket->id]);

      $affectedTikets = M_Ticket::where('status', 1)
        ->where('position', '>', $tiket->position)
        ->get();

      foreach ($affectedTikets as $affectedTiket) {
        $affectedTiket->position -= 1;
        $affectedTiket->save();
      }

      $data['tiket'] = M_Ticket::findOrFail($tiket->id);
      $data['pengunjung'] = M_Customer::findOrFail($data['tiket']->customer_id);
      $data['logo1'] = M_Config::value('logo1');
      $data['instanceName'] = M_Config::value('instance_name');

      // Mail::to($data['pengunjung']->email)->send(new RegisterMail($data));
      return response()->json(['token' => $token], Response::HTTP_OK);
    }
  }

  public function tanyaInduk(Request $request)
  {
    $data['instanceName'] = M_Config::value('instance_name');
    $data['logo1'] = M_Config::value('logo1');
    $data['counters'] = M_Counter_Category::get();
    $data['config'] = M_Config::first();
    $data['type'] = $request->type;

    return view('pages.booking-antrian.input-nomor-induk', $data);
  }

  public function checkMember(Request $request)
  {
    $config = M_Config::first();
    $url = str_replace('{slug}', $request->kode, $config->partner_api);
    $response = Http::get($url);

    return response()->json($response->object());
  }
}
