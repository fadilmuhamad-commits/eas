@extends('index')
@section('title', 'Booking')

@section('layout')
  <style>
    body {
      min-height: 100vh;
      /* background-image: url('/images/greyBg.png'); */
      /* background-size: cover; */

      background: radial-gradient(circle,
          rgba({{ implode(',', hexToRgb($config->Color1->hexcode)) }}, 0.5) 0%,
          rgb({{ implode(',', hexToRgb($config->Color1->hexcode)) }}) 100%);
    }

    #booking-layout {
      min-height: 100vh;
    }

    #booking-container {
      width: 540px;
      padding: 32px 56px;
    }

    @media (max-width: 640px) {
      #booking-container {
        width: 100%;
        padding: 24px 24px;
      }
    }
  </style>

  <div class="d-flex justify-content-center align-items-center" id="booking-layout">
    <div class="bg-white rounded mx-3 my-4" id="booking-container">
      <form class="w-100" action="{{ route('booking.store') }}" method="POST" id="form-booking">
        @csrf
        @if (file_exists(public_path('storage/' . $logo1)) && $config->logo1)
          <div class="text-center">
            <img src="{{ asset('storage/' . $logo1) }}" class="object-fit-contain" style="height: 80px"
              alt="Logo {{ $instanceName }}">
          </div>
        @endif
        <div class="fs-2 fw-semibold text-black text-center mt-2">
          Booking Antrian <br>
          <span class="fw-bold">{{ $instanceName }}</span>
        </div>
        <div class="mb-3">
          <hr class="my-3">
          <div class="d-flex flex-column align-items-center">
            <span class="text-black mb-2 text-center">Sudah Pernah Berkunjung?</span>
            @if ($config->partnership == 1)
              {{-- mode non partnership --}}
              <a href="{{ route('nomor-induk') }}" style="width: 208px"
                class="btn btn-primary btn-sm text-center fw-bold">
                Booking Menggunakan<br>Nomor Registrasi
              </a>
            @else
              {{-- mode partnership --}}
              <button type="button" data-bs-toggle="modal" data-bs-target="#modal-partner" style="width: 208px"
                class="btn btn-primary btn-sm text-center fw-bold">
                Booking Menggunakan<br>Nomor Registrasi / Anggota
              </button>
            @endif
          </div>
          <hr class="my-3">
          <label for="name" class="form-label text-black fw-semibold">Nama Lengkap</label>
          <input type="text" autocomplete="name" class="form-control py-2" style="font-size: 14px" id="name"
            name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}">
          @error('name')
            <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
          @enderror
        </div>
        <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
          <div class="col">
            <label for="email" class="form-label text-black fw-semibold">Email</label>
            <input type="email" autocomplete="email" class="form-control py-2" style="font-size: 14px" id="email"
              name="email" placeholder="Masukkan email" value="{{ old('email') }}">
            @error('email')
              <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
            @enderror
          </div>
          <div class="col">
            <label for="phone_number" class="form-label text-black fw-semibold">Nomor Telepon</label>
            <input type="tel" pattern="[^a-zA-Z]*" autocomplete="tel" class="form-control py-2"
              style="font-size: 14px" id="phone_number" name="phone_number" placeholder="Masukkan nomor"
              value="{{ old('phone_number') }}">
            @error('phone_number')
              <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
          <div class="col">
            <label for="birth_place" class="form-label text-black fw-semibold">Tempat Lahir</label>
            <input type="text" class="form-control py-2" style="font-size: 14px" id="birth_place" name="birth_place"
              placeholder="Masukkan tempat lahir" value="{{ old('birth_place') }}">
            @error('birth_place')
              <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
            @enderror
          </div>
          <div class="col">
            <label for="birth_date" class="form-label text-black fw-semibold">Tanggal Lahir</label>
            <input type="date" autocomplete="bday" class="form-control py-2" style="font-size: 14px" id="birth_date"
              name="birth_date" max="9999-12-31" value="{{ old('birth_date') }}">
            @error('birth_date')
              <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="address" class="form-label text-black fw-semibold">Alamat</label>
          <input type="text" autocomplete="street-address" class="form-control py-2" style="font-size: 14px"
            id="address" name="address" placeholder="Masukkan alamat" value="{{ old('address') }}">
          @error('address')
            <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
          @enderror
        </div>

        <div style="margin-bottom: 50px">
          <label for="counters" class="form-label text-black fw-semibold">Kategori</label>
          <select id="counters" name="counters" class="form-select py-2" style="font-size: 14px;">
            <option value="">--Pilih Kategori--</option>
            @foreach ($loket as $item)
              {{-- @if ($hasActiveLoket) --}}
              <option value="{{ $item->id }}">{{ $item->name }}</option>
              {{-- @else --}}
              {{-- <option value="">{{ $item->name }} <span class="text-danger">TIDAK AKTIF</span> --}}
              {{-- </option> --}}
              {{-- @endif --}}
            @endforeach
          </select>
          @error('counters')
            <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
          @enderror
        </div>

        <button data-bs-toggle="modal" data-bs-target="#myModal" type="submit"
          class="btn btn-primary w-100 fw-semibold mb-3" id="btn-booking">Booking</button>
      </form>
    </div>
  </div>

  {{-- MODAL PARTNER --}}
  @if ($config->partnership == 2)
    <div class="modal fade" id="modal-partner" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="text-center pt-4 fs-4 fw-bold">Pilih Metode</div>
          <hr>
          <div class="modal-body d-flex flex-wrap justify-content-between gap-3">
            <a href="{{ route('nomor-induk', ['type' => 'default']) }}"
              class="btn btn-primary col p-4 fw-semibold fs-5 text-white">
              NOMOR REGISTRASI
            </a>
            <a href="{{ route('nomor-induk', ['type' => 'partner']) }}"
              class="btn bg-primary col p-4 fw-semibold fs-5 text-white">
              NOMOR ANGGOTA
            </a>
          </div>
          <button type="button" class="btn-close position-absolute" style="top: 24px; right: 24px;"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  @endif

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-booking").addEventListener("submit", function() {
        document.getElementById("btn-booking").disabled = true;
      });
    });
  </script>

  {{-- MODAL --}}
  {{-- <div class="modal px-4" tabindex="-1" id="myModal">
    <div class="modal-dialog modal-dialog-centered">

      <div class="modal-content p-3">
        <div class="modal-header" style="margin: -15px">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p class="fs-5 fw-bold" style="color: #575757">Kode Booking Anda: </p>
          <p class="fw-bold text-black fs-1">{{ $tiket> }}</p>
        </div>
      </div>
    </div>
  </div> --}}
@endsection
