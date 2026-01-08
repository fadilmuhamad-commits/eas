@extends('layouts.admin')
@section('title', 'Edit Data Pengunjung')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">
        <a class="text-body" href="{{ route('antrian') }}">antrian</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">edit-antrian</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <h5 class="fw-semibold">Edit Data Antrian</h5>

      <form class="m-auto d-flex gap-2" style="width: 60%" action="{{ route('antrian.update', $customer->id) }}"
        method="POST" id="form-edit">
        @csrf
        @method('put')

        <div class="col">
          {{-- Form-Nama --}}
          <div class="mb-3">
            <label for="nama-pengunjung" class="form-label  fw-semibold">Nama</label>
            <input type="text" value="{{ old('nama-pengunjung', $customer->name) }}" class="form-control py-2"
              style="font-size: 14px" id="nama-pengunjung" name="nama-pengunjung" placeholder="Masukkan nama">
            <x-form-alert field="nama-pengunjung" />
          </div>

          {{-- Form-Email --}}
          <div class="mb-3">
            <label for="email-pengunjung" class="form-label  fw-semibold">Email</label>
            <input type="text" value="{{ old('email-pengunjung', $customer->email) }}" class="form-control py-2"
              style="font-size: 14px" id="email-pengunjung" name="email-pengunjung" placeholder="Masukkan email">
            <x-form-alert field="email-pengunjung" />
          </div>

          {{-- Form-No-Telp-Alamat --}}
          <div class="d-flex gap-2 mb-3">
            <div class="col">
              <label for="alamat-pengunjung" class="form-label  fw-semibold">Alamat</label>
              <input type="text" value="{{ old('alamat-pengunjung', $customer->address) }}" class="form-control py-2"
                style="font-size: 14px" id="alamat-pengunjung" name="alamat-pengunjung" placeholder="Masukkan alamat">
              <x-form-alert field="alamat-pengunjung" />
            </div>

            <div class="col">
              <label for="telp-pengunjung" class="form-label  fw-semibold">No. Telp</label>
              <input type="tel" pattern="[^a-zA-Z]*" value="{{ old('telp-pengunjung', $customer->phone_number) }}"
                class="form-control py-2" style="font-size: 14px" id="telp-pengunjung" name="telp-pengunjung"
                placeholder="Masukkan no.telp">
              <x-form-alert field="telp-pengunjung" />
            </div>
          </div>

          {{-- Form-TTL --}}
          <div class="d-flex gap-2 mb-3">
            <div class="col">
              <label for="tempat-lahir-pengunjung" class="form-label  fw-semibold">Tempat
                Lahir</label>
              <input type="text" value="{{ old('tempat-lahir-pengunjung', $customer->birth_place) }}"
                class="form-control py-2" style="font-size: 14px" id="tempat-lahir-pengunjung"
                name="tempat-lahir-pengunjung" placeholder="Masukkan tempat lahir">
              <x-form-alert field="tempat-lahir-pengunjung" />
            </div>

            <div class="col">
              <label for="tanggal-lahir-pengunjung" class="form-label  fw-semibold">Tanggal
                Lahir</label>
              <input type="date" value="{{ old('tanggal-lahir-pengunjung', $customer->birth_date) }}"
                class="form-control py-2" style="font-size: 14px" id="tanggal-lahir-pengunjung"
                name="tanggal-lahir-pengunjung" max="9999-12-31">
              <x-form-alert field="tanggal-lahir-pengunjung" />
            </div>
          </div>

          {{-- Form-Submit --}}
          <button type="submit" class="btn btn-primary w-100 fw-semibold" id="btn-edit">Simpan</button>
        </div>
      </form>
    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-edit").addEventListener("submit", function() {
        document.getElementById("btn-edit").disabled = true;
      });
    });
  </script>
@endsection
