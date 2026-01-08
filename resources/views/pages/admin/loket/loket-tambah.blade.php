@extends('layouts.admin')
@section('title', 'Tambah Loket')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">
        <a class="text-body" href="{{ route('loket') }}">loket</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $edit ? 'loket-edit' : 'loket-tambah' }}</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">{{ $edit ? 'Edit Loket' : 'Tambah Loket' }}</h5>

      <form class="m-auto" style="width: 60%"
        action="{{ $edit ? route('loket.update', $counter->id) : route('loket.store') }}" method="POST" id="form-edit">
        @csrf

        @if ($edit)
          @method('put')
        @endif

        <div class="d-flex gap-3 mb-4">
          <div class="col-4">
            {{-- Form-Nama --}}
            <div class="mb-3">
              <label for="nama-loket" class="form-label  fw-semibold">Nama Loket</label>
              <input type="text" value="{{ $edit ? old('nama-loket', $counter->name) : old('nama-loket') }}"
                class="form-control py-2" style="font-size: 14px" id="nama-loket" name="nama-loket"
                placeholder="Masukkan nama loket">
              <x-form-alert field="nama-loket" />
            </div>

            {{-- Form Warna --}}
            <div>
              <label for="color" class="form-label  fw-semibold">Warna Loket</label>
              <input type="color" class="w-100" id="color" name="color" style="height: 112px"
                value="{{ $edit ? old('color', $counter->Color->hexcode) : old('color') }}">
              <div class="d-flex gap-2 mt-2">
                @foreach ($colors as $color)
                  <div onclick="pickColor('#color', '{{ $color->hexcode }}')" data-tooltip="tooltip"
                    data-bs-placement="bottom" data-bs-title="{{ $color->hexcode }}"
                    style="cursor: pointer; width: 24px; height: 24px; background-color: {{ $color->hexcode }};"></div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="col d-flex gap-3">
            {{-- Form-Category --}}
            <div class="col">
              <label class="form-label  fw-semibold">Kategori</label>
              <div class="d-flex flex-column overflow-auto border rounded-1 p-0" style="font-size: 14px; height: 196px;">
                @foreach ($counter_categories as $item)
                  <div>
                    <input type="checkbox" class="btn-check category-checkbox" id="cat-{{ $item->id }}"
                      value="{{ $item->id }}"
                      {{ $edit && $counter->Categories->contains('id', $item->id) ? 'checked' : '' }}>
                    <label class="btn btn-outline-success border-0 rounded-0 text-start w-100" style="font-size: 14px"
                      for="cat-{{ $item->id }}">{{ $item->name }}</label>
                  </div>
                @endforeach
              </div>
              <x-form-alert field="categories" />
            </div>

            {{-- Form-Group --}}
            <div class="col d-flex flex-column">
              <label class="form-label  fw-semibold">Group</label>
              <div role="group" class="d-flex flex-column overflow-auto border rounded-1 p-0 col"
                style="font-size: 14px; max-height: 196px;">
                @foreach ($group as $item)
                  <div>
                    <input type="radio" class="btn-check" name="radio-group" id="group-{{ $item->id }}"
                      value="{{ $item->id }}" {{ $edit && $item->id == $counter->group_id ? 'checked' : '' }}>
                    <label class="btn btn-outline-success border-0 rounded-0 text-start w-100" style="font-size: 14px"
                      for="group-{{ $item->id }}">{{ $item->name }}</label>
                  </div>
                @endforeach
              </div>

              <x-form-alert field="radio-group" />
            </div>
          </div>
        </div>

        {{-- Form-Selected Category --}}
        <input type="hidden" name="categories" id="categories">

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

    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-tambah").addEventListener("submit", function() {
        document.getElementById("btn-tambah").disabled = true;
      });
    });

    function updateSelectedCat() {
      let selectedCat = [];

      $('input[type="checkbox"].category-checkbox').each(function() {
        if ($(this).prop('checked')) {
          selectedCat.push($(this).val());
        }
      });

      if (selectedCat.length > 0) {
        $('#categories').val(JSON.stringify(selectedCat));
      } else {
        $('#categories').val(null);
      }
    }

    $('input[type="checkbox"].category-checkbox').on('change', function() {
      updateSelectedCat();
    });

    $(document).ready(function() {
      updateSelectedCat();
    });

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-edit").addEventListener("submit", function() {
        document.getElementById("btn-edit").disabled = true;
      });
    });
  </script>
@endsection
