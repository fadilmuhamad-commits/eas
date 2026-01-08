@extends('layouts.admin')
@section('title', 'Edit Account')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">edit-account</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">Edit Account</h5>

      <form action="{{ route('account.update', $user->id) }}" method="POST" class="m-auto" style="width: 50%" id="form-edit">
        @csrf
        @method('put')

        {{-- Form-Nama --}}
        <div class="mb-4">
          <label for="name" class="form-label  fw-semibold">Name</label>
          <input type="text" value="{{ old('name', $user->name) }}" class="form-control py-2" style="font-size: 14px"
            id="name" name="name" placeholder="Masukkan nama">
          <x-form-alert field="name" />
        </div>

        {{-- Form-Username --}}
        <div class="mb-4">
          <label for="username" class="form-label  fw-semibold">Username</label>
          <input type="text" value="{{ old('username', $user->username) }}" class="form-control py-2"
            style="font-size: 14px" id="username" name="username" placeholder="Masukkan username">
          <x-form-alert field="username" />
        </div>

        {{-- Form-Email --}}
        <div class="mb-4">
          <label for="email" class="form-label  fw-semibold">Email</label>
          <input type="email" value="{{ old('email', $user->email) }}" class="form-control py-2" style="font-size: 14px"
            id="email" name="email" placeholder="Masukkan email">
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
        <button type="submit" class="btn btn-primary w-100 fw-semibold" id="btn-edit">Simpan</button>
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

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-edit").addEventListener("submit", function() {
        document.getElementById("btn-edit").disabled = true;
      });
    });
  </script>
@endsection
