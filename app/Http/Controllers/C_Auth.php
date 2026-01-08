<?php

namespace App\Http\Controllers;

use App\Models\M_Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class C_Auth extends Controller
{
  public function index()
  {
    if (auth()->check()) {
      return redirect()->route('dashboard');
    }

    $data['config'] = M_Config::first();

    return view('pages.auth.login', $data);
  }

  public function login(Request $request)
  {
    $request->validate([
      'usernameL' => 'required',
      'passwordL' => 'required'
    ], [
      'usernameL.required' => 'Username atau email wajib diisi',
      'passwordL.required' => 'Password wajib diisi'
    ]);

    $loginField = filter_var($request->usernameL, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $infologin = [
      $loginField => $request->usernameL,
      'password' => $request->passwordL
    ];

    if (Auth::attempt($infologin)) {
      // GENERATE UI COLOR
      // if (Auth::user()->Loket) {
      //   $primaryColor = Auth::user()->Loket->Color->hexcode;
      //   $scss = view('theme', ['primaryColor' => $primaryColor])->render();
      //   $existingScss = Storage::exists('public/css/theme.scss') ? Storage::get('public/css/theme.scss') : null;

      //   if ($scss !== $existingScss) {
      //     Storage::put('public/css/theme.scss', $scss);
      //     // Cache::put('dynamic_scss', true, now()->addMinutes(120));
      //   }
      // }


      return redirect()->route('dashboard');
    } else {
      return redirect()->route('login')->with('error', 'Username dan Password yang dimasukan tidak valid');
    }
  }

  // public function register(Request $request)
  // {
  //   $request->validate([
  //     'usernameR' => 'required|min:5|unique:users,name',
  //     'email' => 'required|email|unique:users',
  //     'passwordR' => [
  //       'required',
  //       'min:8',
  //       'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d])[\s\S]{8,}$/'
  //     ],
  //     'confirm-password' => 'required|same:passwordR'
  //   ], [
  //     'usernameR.required' => 'Username wajib diisi',
  //     'usernameR.min' => 'Username minimal berjumlah 5 karakter',
  //     'usernameR.unique' => 'Username sudah digunakan',
  //     'email.required' => 'Email wajib diisi',
  //     'email.unique' => 'Email sudah digunakan',
  //     'passwordR.required' => 'Password wajib diisi',
  //     'passwordR.min' => 'Password minimal berjumlah 8 karakter',
  //     'passwordR.regex' => 'Password harus memiliki minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol',
  //     'confirm-password.required' => 'Confirm Password wajib diisi',
  //     'confirm-password.same' => 'Confirm Password harus sama dengan Password'
  //   ]);

  //   $data = [
  //     'name' => $request->usernameR,
  //     'email' => $request->email,
  //     'password' => Hash::make($request->passwordR)
  //   ];
  //   M_User::create($data);

  //   $loginField = filter_var($request->usernameR, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

  //   $infologin = [
  //     $loginField => $request->usernameR,
  //     'password' => $request->passwordR
  //   ];

  //   if (Auth::attempt($infologin)) {
  //     return redirect()->route('dashboard');
  //   } else {
  //     return redirect()->route('login')->with('error', 'Username dan Password yang dimasukan tidak valid');
  //   }
  // }

  public function logout()
  {
    Auth::logout();
    return redirect()->route('login');
  }
}
