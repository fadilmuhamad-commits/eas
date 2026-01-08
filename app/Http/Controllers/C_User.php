<?php

namespace App\Http\Controllers;

use App\Models\M_Config;
use App\Models\M_Role;
use App\Models\M_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class C_User extends Controller
{
  public function users(Request $request)
  {
    $query = M_User::query();

    $users = $query
      ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
      ->leftJoin('counters', 'users.counter_id', '=', 'counters.id')
      ->select('users.*', 'roles.name as role_name', 'counters.name as counter_name');

    // SEARCH
    $data['search'] = $request->input('search');
    $data['searchBy'] = $request->input('search_by');

    $search = $data['search'];
    $searchBy = $data['searchBy'];

    if (!empty($search) && empty($searchBy)) {
      $users
        ->where('users.name', 'like', '%' . $search . '%')
        ->orWhere('users.email', 'like', '%' . $search . '%')
        ->orWhere('roles.name', 'like', '%' . $search . '%')
        ->orWhere('counters.name', 'like', '%' . $search . '%');
    } else if (!empty($search) && !empty($searchBy) && $searchBy != 'role_name' && $searchBy != 'counter_name') {
      $users
        ->where('users.' . $searchBy, 'like', '%' . $search . '%');
    } else if (!empty($search) && !empty($searchBy) && $searchBy == 'role_name') {
      $users
        ->where('roles.name', 'like', '%' . $search . '%');
    } else if (!empty($search) && !empty($searchBy) && $searchBy == 'counter_name') {
      $users
        ->where('counters.name', 'like', '%' . $search . '%');
    }
    // END SEARCH

    // SORT
    $data['sort'] = $request->input('sort');
    $data['order'] = $request->input('order');

    $sort = $data['sort'];
    $order = $data['order'];

    if (!empty($sort) && !empty($order)) {
      $users->orderBy($sort, $order);
    } else {
      $users->orderBy('users.id', 'asc');
    }
    // END SORT

    // PER PAGE
    $data['perPage'] = $request->input('perPage');

    if (empty($data['perPage']) || !is_numeric($data['perPage'])) {
      $data['perPage'] = 10;
    }
    // END PER PAGE

    $data['users'] = $users->paginate($data['perPage']);
    $data['length'] = $data['users']->total();
    $config = M_Config::first();
    $data['config'] = $config;
    return view('pages.admin.users.users', $data);
  }

  public function tambahUser()
  {
    $data['edit'] = false;
    $data['roles'] = DB::table('roles')->get();
    $data['counters'] = DB::table('counters')->get();
    $data['config'] = M_Config::first();

    return view('pages.admin.users.users-tambah', $data);
  }

  public function editUser(M_User $user)
  {
    if ($user->id !== 1 || ($user->id == 1 && Auth::user()->id === 1)) {
      $data['edit'] = true;
      $data['roles'] = M_Role::all();
      $data['counters'] = DB::table('counters')->get();
      $data['user'] = M_User::where('users.id', '=', $user->id)
        ->first();
      $data['config'] = M_Config::first();
    } else {
      abort(403, 'Unauthorized action.');
    }

    return view('pages.admin.users.users-tambah', $data);
  }

  public function destroy(M_User $user)
  {
    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully');
  }

  public function destroySelected(Request $request)
  {
    $selectedRows = json_decode($request->input('selectedRows'));

    foreach ($selectedRows as $rowId) {
      $user = M_User::findOrFail($rowId);

      $user->delete();
    }

    return redirect()->back()->with('success', count($selectedRows) . ' row(s) deleted successfully');
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required',
      'username' => 'required|unique:users,username',
      'role' => 'required',
      'email' => 'required|email|unique:users,email',
      'password' => [
        'required',
        'min:8',
        // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d])[\s\S]{8,}$/'
      ],
    ], [
      'name.required' => 'Nama wajib diisi',
      'username.required' => 'Username wajib diisi',
      'username.unique' => 'Username sudah digunakan',
      'email.required' => 'Email wajib diisi',
      'email.unique' => 'Email sudah digunakan',
      'password.required' => 'Password wajib diisi',
      'password.min' => 'Password minimal berjumlah 8 karakter',
      // 'password.regex' => 'Password harus memiliki minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol',
      'role.required' => 'Role wajib dipilih',
    ]);

    M_User::create([
      'name' => $request->input('name'),
      'username' => $request->input('username'),
      'email' => $request->input('email'),
      'password' => bcrypt($request->input('password')),
      'role_id' => $request->input('role'),
      'counter_id' => $request->input('counter-user') ? $request->input('counter-user') : null,
    ]);

    return redirect()->route('users')->with('success', 'User added successfully');
  }

  public function update(Request $request, M_User $user)
  {
    $request->validate([
      'name' => 'required',
      'username' => [
        'required',
        Rule::unique('users', 'username')->ignore($user->id),
      ],
      'role' => 'required',
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($user->id),
      ],
      'password' => [
        'nullable',
        'min:8',
        // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d])[\s\S]{8,}$/'
      ],
    ], [
      'name.required' => 'Nama wajib diisi',
      'username.required' => 'Username wajib diisi',
      'username.unique' => 'Username sudah digunakan',
      'email.required' => 'Email wajib diisi',
      'email.unique' => 'Email sudah digunakan',
      'password.required' => 'Password wajib diisi',
      'password.min' => 'Password minimal berjumlah 8 karakter',
      // 'password.regex' => 'Password harus memiliki minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol',
      'role.required' => 'Role wajib dipilih',
    ]);

    $input = [
      'name' => $request->input('name'),
      'username' => $request->input('username'),
      'role_id' => $request->input('role'),
      'counter_id' =>  $request->input('counter-user') ? $request->input('counter-user') : null,
      'email' => $request->input('email'),
    ];

    if ($request->filled('password')) {
      $input['password'] = bcrypt($request->input('password'));
    }

    $user->update($input);

    return redirect()->route('users')->with('success', 'User updated successfully');
  }

  public function editAccount()
  {
    $user = Auth::user();

    $data['user'] = DB::table('users')
      ->where('users.id', '=', $user->id)
      ->first();

    $config = M_Config::first();
    $data['config'] = $config;

    return view('pages.admin.edit-account', $data);
  }

  public function updateAccount(Request $request, M_User $user)
  {
    $request->validate([
      'name' => 'required',
      'username' => [
        'required',
        Rule::unique('users', 'username')->ignore($user->id),
      ],
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($user->id),
      ],
      'password' => [
        'nullable',
        'min:8',
        // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d])[\s\S]{8,}$/'
      ],
    ], [
      'name.required' => 'Nama wajib diisi',
      'username.required' => 'Username wajib diisi',
      'username.unique' => 'Username sudah digunakan',
      'email.required' => 'Email wajib diisi',
      'email.unique' => 'Email sudah digunakan',
      'password.required' => 'Password wajib diisi',
      'password.min' => 'Password minimal berjumlah 8 karakter',
      // 'password.regex' => 'Password harus memiliki minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 simbol',
    ]);

    $data = [
      'name' => $request->input('name'),
      'username' => $request->input('username'),
      'email' => $request->input('email'),
    ];

    if ($request->filled('password')) {
      $data['password'] = bcrypt($request->input('password'));
    }

    $user->update($data);

    return redirect()->route('dashboard')->with('success', 'Account updated successfully');
  }
}
