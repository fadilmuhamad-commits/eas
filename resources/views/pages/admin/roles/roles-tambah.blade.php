@extends('layouts.admin')
@section('title', 'Tambah Role')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">
        <a class="text-body" href="{{ route('roles') }}">roles</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $edit ? 'edit-role' : 'tambah-role' }}</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">{{ $edit ? 'Edit Role' : 'Tambah Role' }}</h5>

      <form class="m-auto" action="{{ $edit ? route('roles.update', $role->id) : route('roles.store') }}" method="POST"
        id="form-role">
        @csrf

        @if ($edit)
          @method('put')
        @endif

        {{-- Form-Nama --}}
        <div class="mb-4">
          <label for="name" class="form-label  fw-semibold">Nama Role</label>
          <input type="text" value="{{ $edit ? old('name', $role->name) : old('name') }}" class="form-control py-2"
            style="font-size: 14px" id="name" name="name" placeholder="Masukkan nama role">
          <x-form-alert field="name" />
        </div>

        {{-- Form-Permissions --}}
        <div class="mb-4">
          <label for="permissions" class="form-label  fw-semibold">Permissions</label>
          <div class="d-grid w-100 form-control py-2 gap-1" style="grid-template-columns: repeat(4, 1fr)">
            @foreach ($permissions as $permission)
              <div style="font-size: 14px">
                <input type="checkbox" class="btn-check permission-checkbox" id="permission-{{ $permission->id }}"
                  value="{{ $permission->id }}"
                  {{ $edit && $role->Permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                <label class="btn btn-outline-success rounded-0 text-start w-100" style="font-size: 14px"
                  for="permission-{{ $permission->id }}" data-tooltip="tooltip" data-bs-placement="top"
                  data-bs-title="{{ $permission->description }}">{{ $permission->name }}</label>
              </div>
            @endforeach
          </div>
          <x-form-alert field="permissions" />
        </div>

        {{-- Form-Selected Permissions --}}
        <input type="hidden" name="permissions" id="permissions">

        {{-- Form-Submit --}}
        <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3"
          id="btn-submit">{{ $edit ? 'Simpan' : 'Tambah' }}</button>
      </form>
    </div>
  </section>

  <script>
    function updateSelectedPermission() {
      let selectedPermission = [];

      $('input[type="checkbox"].permission-checkbox').each(function() {
        if ($(this).prop('checked')) {
          selectedPermission.push($(this).val());
        }
      });

      if (selectedPermission.length > 0) {
        $('#permissions').val(JSON.stringify(selectedPermission));
      } else {
        $('#permissions').val(null);
      }
    }

    $('input[type="checkbox"].permission-checkbox').on('change', function() {
      updateSelectedPermission();
    });

    $(document).ready(function() {
      updateSelectedPermission();
    });

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-role").addEventListener("submit", function() {
        document.getElementById("btn-submit").disabled = true;
      });
    });
  </script>
@endsection
