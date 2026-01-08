@extends('layouts.admin')
@section('title', 'Tambah User')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">
        <a class="text-body" href="{{ route('users') }}">users</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $edit ? 'edit-user' : 'tambah-user' }}</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">{{ $edit ? 'Edit User' : 'Tambah User' }}</h5>

      <form action="{{ $edit ? route('user.update', $user->id) : route('users.store') }}" method="POST"
        class="m-auto position-relative" style="width: 35%" id="form-edit">
        @csrf
        @if ($edit)
          @method('put')
        @endif

        {{-- Dropdowns --}}
        <div class="d-flex flex-column gap-4 position-absolute" style="right: -180px; width: 160px;">
          {{-- Dropdown Role --}}
          <div class="col">
            <label for="role" class="form-label  fw-semibold">Role</label>
            <select name="role" id="role" class="form-select py-2" style="font-size: 14px;">
              @foreach ($roles as $role)
                @if ($role->id != 1)
                  <option value="{{ $role->id }}" @selected($edit ? $role->id == $user->role_id : false)>{{ $role->name }}</option>
                @endif
              @endforeach
            </select>
            <x-form-alert field="role" />
          </div>

          {{-- Dropdown Counter --}}
          <div class="col" id="counter-select"
            style="display: {{ $edit && ($user->id != 1 || $user->id != 2) ? 'block' : 'none' }}">
            <label for="counter-user" class="form-label  fw-semibold">Loket</label>
            <select name="counter-user" id="counter-user" class="form-select py-2" style="font-size: 14px;">
              <option value="">--Pilih Loket--</option>
              @foreach ($counters as $counter)
                <option value="{{ $counter->id }}" @selected($edit ? $counter->id == $user->counter_id : false)>{{ $counter->name }}</option>
              @endforeach
            </select>
            <x-form-alert field="counter-user" />
          </div>
        </div>

        {{-- Form-Nama --}}
        <div class="mb-4">
          <label for="name" class="form-label  fw-semibold">Name</label>
          <input type="text" value="{{ $edit ? old('name', $user->name) : old('name') }}" class="form-control py-2"
            style="font-size: 14px" id="name" name="name" placeholder="Masukkan nama">
          <x-form-alert field="name" />
        </div>

        {{-- Form-Username --}}
        <div class="mb-4">
          <label for="username" class="form-label  fw-semibold">Username</label>
          <input type="text" value="{{ $edit ? old('username', $user->username) : old('username') }}"
            class="form-control py-2" style="font-size: 14px" id="username" name="username"
            placeholder="Masukkan username">
          <x-form-alert field="username" />
        </div>

        {{-- Form-Email --}}
        <div class="mb-4">
          <label for="email" class="form-label  fw-semibold">Email</label>
          <input type="email" value="{{ $edit ? old('email', $user->email) : old('email') }}" class="form-control py-2"
            style="font-size: 14px" id="email" name="email" placeholder="Masukkan email">
          <x-form-alert field="email" />
        </div>

        {{-- Form-Password --}}
        <div class="mb-4">
          <label for="password" class="form-label  fw-semibold">Password</label>
          <div class="d-flex align-items-center ">
            <input type="password" class="form-control py-2" style="font-size: 14px" name="password" id="password"
              placeholder="Masukkan password">
            <i class="bi bi-eye-slash fs-5  " style="margin-left: -32px; cursor: pointer;" id="toggle-password"></i>
          </div>
          <x-form-alert field="password" />
        </div>

        {{-- Form-Submit --}}
        <button type="submit" class="btn btn-primary w-100 fw-semibold"
          id="btn-edit">{{ $edit ? 'Simpan' : 'Tambah' }}</button>
      </form>
    </div>
  </section>

  <script>
    const toggleTambahPassword = document.querySelector('#toggle-password');
    const tambahPasswordUser = document.querySelector('#password');

    toggleTambahPassword.addEventListener('click', function() {
      const type = tambahPasswordUser
        .getAttribute('type') === 'password' ?
        'text' : 'password';
      tambahPasswordUser.setAttribute('type', type);
      this.classList.toggle('bi-eye');
    })

    document.addEventListener('DOMContentLoaded', function() {
      const roleUser = document.getElementById('role');
      const counterSelect = document.getElementById('counter-select');
      const counterUser = document.querySelector('select[name="counter-user"]');

      roleUser.addEventListener('change', function() {
        const selectedRoleId = parseInt(this.value);
        if (selectedRoleId !== 1 && selectedRoleId !== 2) {
          counterSelect.style.display = 'block';
          counterUser.value = '{{ $edit ? $user->counter_id : '' }}';
        } else {
          counterSelect.style.display = 'none';
          counterUser.value = null;
        }
      });
    })

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-edit").addEventListener("submit", function() {
        document.getElementById("btn-edit").disabled = true;
      });
    });
  </script>
@endsection
