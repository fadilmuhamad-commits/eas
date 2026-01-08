<?php

use App\Http\Controllers\C_Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

// CATEGORYL
Route::get('categoryL', [C_Api::class, 'categoryLs']);
Route::get('categoryL/{id}', [C_Api::class, 'categoryL']);
Route::post('categoryL', [C_Api::class, 'categoryLStore']);
Route::put('categoryL/{id}', [C_Api::class, 'categoryLUpdate']);
Route::delete('categoryL/{id}', [C_Api::class, 'categoryLDestroy']);

// CATEGORYT
Route::get('categoryT', [C_Api::class, 'categoryTs']);
Route::get('categoryT/{id}', [C_Api::class, 'categoryT']);
Route::post('categoryT', [C_Api::class, 'categoryTStore']);
Route::put('categoryT/{id}', [C_Api::class, 'categoryTUpdate']);
Route::delete('categoryT/{id}', [C_Api::class, 'categoryTDestroy']);

// COLOR
Route::get('color', [C_Api::class, 'colors']);
Route::get('color/{id}', [C_Api::class, 'color']);
Route::post('color', [C_Api::class, 'colorStore']);
Route::put('color/{id}', [C_Api::class, 'colorUpdate']);
Route::delete('color/{id}', [C_Api::class, 'colorDestroy']);

// CONFIG
Route::get('config', [C_Api::class, 'configs']);
Route::put('config/{id}', [C_Api::class, 'configUpdate']);

// GROUP
Route::get('group', [C_Api::class, 'groups']);
Route::get('group/{id}', [C_Api::class, 'group']);
Route::post('group', [C_Api::class, 'groupStore']);
Route::put('group/{id}', [C_Api::class, 'groupUpdate']);
Route::delete('group/{id}', [C_Api::class, 'groupDestroy']);

// LOKET
Route::get('loket', [C_Api::class, 'lokets']);
Route::get('loket/{id}', [C_Api::class, 'loket']);
Route::post('loket', [C_Api::class, 'loketStore']);
Route::put('loket/{id}', [C_Api::class, 'loketUpdate']);
Route::delete('loket/{id}', [C_Api::class, 'loketDestroy']);

// PENGUNJUNG
Route::get('pengunjung', [C_Api::class, 'pengunjungs']);
Route::get('pengunjung/{id}', [C_Api::class, 'pengunjung']);
Route::get('pengunjung/{id}/regis', [C_Api::class, 'pengunjungRegis']);
Route::post('pengunjung', [C_Api::class, 'pengunjungStore']);
Route::put('pengunjung/{id}', [C_Api::class, 'pengunjungUpdate']);
Route::delete('pengunjung/{id}', [C_Api::class, 'pengunjungDestroy']);

// ROLE
Route::get('role', [C_Api::class, 'roles']);
Route::get('role/{id}', [C_Api::class, 'role']);
Route::post('role', [C_Api::class, 'roleStore']);
Route::put('role/{id}', [C_Api::class, 'roleUpdate']);
Route::delete('role/{id}', [C_Api::class, 'roleDestroy']);

// STATUS
Route::get('status', [C_Api::class, 'statuses']);
Route::get('status/{id}', [C_Api::class, 'status']);

// TIKET
Route::get('tiket', [C_Api::class, 'tikets']);
Route::get('tiket/{id}', [C_Api::class, 'tiket']);
Route::post('tiket', [C_Api::class, 'tiketStore']);
Route::put('tiket/{id}', [C_Api::class, 'tiketUpdate']);
Route::delete('tiket/{id}', [C_Api::class, 'tiketDestroy']);

// USER
Route::get('user', [C_Api::class, 'users']);
Route::get('user/{id}', [C_Api::class, 'user']);
Route::post('user', [C_Api::class, 'userStore']);
Route::put('user/{id}', [C_Api::class, 'userUpdate']);
Route::delete('user/{id}', [C_Api::class, 'userDestroy']);
