<!-- success.blade.php -->
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

    #kode-booking {
      font-family: 'Roboto Mono', sans-serif
    }

    @media(max-width: 375px) {
      #kode-booking {
        font-size: 2rem
      }

      #no-antrian {
        font-size: 2rem
      }
    }
  </style>

  <section class="d-flex justify-content-center align-items-center" id="booking-layout">
    <div class="bg-white rounded mx-3 my-4" id="booking-container" style="padding: 32px 4%">
      <div class="mb-3 text-center">
        @if (file_exists(public_path('storage/' . $logo1)) && $config->logo1)
          <img src="{{ asset('storage/' . $logo1) }}" class="object-fit-contain mb-3" style="height: 80px"
            alt="Logo {{ $instanceName }}">
        @endif
        <div class="text-center">
          <h2 class="fw-bold text-black">Data Diri Anda</h2>
          <div class="border-black rounded-1 px-4 py-2 mt-4 mx-auto" style="border: 1px solid; width: fit-content;">
            <span class="fw-semibold text-black">
              Nomor Registrasi:
            </span><br>
            <span class="fw-bold fs-3 text-primary"
              style="word-break: break-all">{{ $pengunjung->registration_code }}</span>
          </div>
          <table class="d-flex justify-content-center text-start text-black mt-3 border border-black rounded"
            style="background-color: #eaeaea; font-size: 12px;">
            <tr>
              <td class="px-sm-3 px-2 py-2">Nama Lengkap</td>
              <td class="px-sm-3 px-1">:</td>
              <td class="fw-bold px-sm-3 px-2 py-2">{{ $pengunjung->name }}</td>
            </tr>
            <tr>
              <td class="px-sm-3 px-2 py-2">Email</td>
              <td class="px-sm-3 px-1">:</td>
              <td class="fw-bold px-sm-3 px-2 py-2">{{ $pengunjung->email }}</td>
            </tr>
            <tr>
              <td class="px-sm-3 px-2 py-2">Nomor Telepon</td>
              <td class="px-sm-3 px-1">:</td>
              <td class="fw-bold px-sm-3 px-2 py-2">{{ $pengunjung->phone_number }}</td>
            </tr>
            <tr>
              <td class="px-sm-3 px-2 py-2">Tempat, Tanggal Lahir</td>
              <td class="px-sm-3 px-1">:</td>
              <td class="fw-bold px-sm-3 px-2 py-2">{{ $pengunjung->birth_place }},
                {{ \Carbon\Carbon::parse($pengunjung->birth_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
              <td class="px-sm-3 px-2 py-2">Alamat</td>
              <td class="px-sm-3 px-1">:</td>
              <td class="fw-bold px-sm-3 px-2 py-2">{{ $pengunjung->address }}</td>
            </tr>
          </table>
          <div class="mt-4 mb-3 fw-bold text-primary" style="font-size: 32px">
            {{ strtoupper($tiket->Counter_Category->name) }}
          </div>
          <div class="d-flex flex-wrap">
            <div class="col border-end border-1 border-primary">
              <h5 class="text-black" style="font-size: 1rem">Kode Booking:</h5>
              <h1 class="text-primary fw-bold" id="kode-booking">
                {{ $tiket->booking_code }}
            </div>
            <div class="col">
              <h5 class="text-black" style="font-size: 1rem">Nomor Antrian:</h5>
              <h1 class="text-primary fw-bold" id="no-booking" id="no-antrian">
                {{ $tiket->Counter_Category->code . '-' . $tiket->queue_number }}
            </div>
          </div>
          <p class="text-black my-3 fw-semibold">Masukkan Kode Booking ke Mesin <br> untuk Aktivasi
            Nomor Antrian
          </p>
          </h1>
          <span style="font-size: 14px" class="mt-3 text-black">Dibuat pada:
            <b>{{ \Carbon\Carbon::parse($tiket->created_at)->translatedFormat('d F Y') }}</b> |
            <b>{{ \Carbon\Carbon::parse($tiket->created_at)->translatedFormat('H:i') }} WIB</b></span> <br>
          <span style="font-size: 14px" class="text-black">Berlaku hingga:
            <b>{{ \Carbon\Carbon::parse($tiket->created_at)->addDay()->translatedFormat('d F Y') }}</b> |
            <b>00:00 WIB</b></span>
        </div>
      </div>
  </section>
@endsection
