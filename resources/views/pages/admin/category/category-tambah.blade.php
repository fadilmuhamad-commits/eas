@extends('layouts.admin')
@section('title', 'Tambah Category')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">
        <a class="text-body" href="{{ route('category') }}">kategori</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $edit ? 'kategori-edit' : 'kategori-tambah' }}</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">{{ $edit ? 'Edit Kategori' : 'Tambah Kategori' }}</h5>

      <form class="m-auto" style="width: 40%"
        action="{{ $type == 'loket'
            ? ($edit
                ? route('categoryL.update', $category->id)
                : route('categoryL.store'))
            : ($edit
                ? route('categoryT.update', $category->id)
                : route('categoryT.store')) }}"
        method="POST" id="form-edit">
        @csrf

        @if ($edit)
          @method('put')
        @endif
        {{-- Form-Nama --}}
        <div class="mb-4">
          <label for="nama-category" class="form-label  fw-semibold">Nama Kategori</label>
          <input type="text" value="{{ $edit ? old('nama-category', $category->name) : old('nama-category') }}"
            class="form-control py-2" style="font-size: 14px" id="nama-category" name="nama-category"
            placeholder="Masukkan nama kategori">
          <x-form-alert field="nama-category" />
        </div>

        <div class="d-flex form-group {{ $type == 'loket' ? 'mb-5' : 'mb-4' }} gap-3">
          {{-- Form-Kode --}}
          <div class="col">
            <label for="kode-category" class="form-label  fw-semibold">Kode Kategori</label>
            <input type="text" value="{{ $edit ? old('kode-category', $category->code) : old('kode-category') }}"
              class="form-control py-2" style="font-size: 14px" id="kode-category" name="kode-category"
              placeholder="Masukkan kode kategori">
            <x-form-alert field="kode-category" />
          </div>

          {{-- Form Warna --}}
          @if ($type == 'loket')
            <div class="col-5">
              <label for="color" class="form-label  fw-semibold">Warna Kategori</label>
              <input type="color" class="w-100 h-50" id="color" name="color"
                value="{{ $edit ? old('color', $category->Color->hexcode) : old('color') }}">
              <div class="d-flex gap-2 mt-2">
                @foreach ($colors as $color)
                  <div onclick="pickColor('#color', '{{ $color->hexcode }}')" data-tooltip="tooltip"
                    data-bs-placement="bottom" data-bs-title="{{ $color->hexcode }}"
                    style="cursor: pointer; width: 24px; height: 24px; background-color: {{ $color->hexcode }};"></div>
                @endforeach
              </div>
            </div>
          @endif
        </div>

        {{-- Form-Submit --}}
        <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3"
          id="btn-edit">{{ $edit ? 'Simpan' : 'Tambah' }}</button>
      </form>
    </div>
  </section>

  <script>
    function pickColor(id, hex) {
      $(id).val(hex);
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-edit").addEventListener("submit", function() {
        document.getElementById("btn-edit").disabled = true;
      });
    });
  </script>
@endsection
