<!-- mail.blade.php -->
@extends('index')
@section('title', 'Booking')

@section('layout')

  <section style="display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;">
    <div style="
        margin: 20px auto;
        padding: 32px 4%;
        text-align: center;">
      <div style="text-align: center">
        <hr>
        <h1 style="font-size: 40px; color: #1c1c1c">{{ $data['instanceName'] }}</h1>
        <h2 style="font-size: 32px; color: #1c1c1c">Data Diri Anda</h2>
        <div id="border-no-induk"
          style="border: 1px solid #1c1c1c;
                border-radius: 1px;
                padding: 2% 4%;
                margin-top: 4%;
                margin-left: auto;
                margin-right: auto;
                border: 1px solid;
                width: fit-content;">
          <span style="font-weight: 600; color: #1c1c1c">
            Nomor Registrasi:
          </span><br>
          <span
            style="word-break: break-all; font-size: 1.5rem; font-weight: bold; color: #1c1c1c">{{ $data['pengunjung']->registration_code }}</span>
        </div>
        <table
          style="background-color: #eaeaea;
                font-size: 12px;
                display: flex;
                justify-content: center;
                text-align: start;
                color: #1c1c1c;
                margin-top: 24px;
                border: 1px solid #1c1c1c;
                border-radius: 3px;
                padding-inline: 10px;">
          <tr>
            <td style="padding-bottom: 8px">Nama Lengkap</td>
            <td style="padding-bottom: 8px">:</td>
            <td style="padding-bottom: 8px">{{ $data['pengunjung']->name }}</td>
          </tr>
          <tr>
            <td style="padding-bottom: 8px">Email</td>
            <td style="padding-bottom: 8px">:</td>
            <td style="padding-bottom: 8px">{{ $data['pengunjung']->email }}</td>
          </tr>
          <tr>
            <td style="padding-bottom: 8px">Nomor Telepon</td>
            <td style="padding-bottom: 8px">:</td>
            <td style="padding-bottom: 8px">{{ $data['pengunjung']->phone_number }}</td>
          </tr>
          <tr>
            <td style="padding-bottom: 8px">Tempat, Tanggal Lahir</td>
            <td style="padding-bottom: 8px">:</td>
            <td style="padding-bottom: 8px">{{ $data['pengunjung']->birth_place }},
              {{ \Carbon\Carbon::parse($data['pengunjung']->birth_date)->translatedFormat('d F Y') }}
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 8px">Alamat</td>
            <td style="padding-bottom: 8px">:</td>
            <td style="padding-bottom: 8px">{{ $data['pengunjung']->address }}</td>
          </tr>
        </table>
        <div
          style="font-size: 24px; color: #1c1c1c; margin-top: 8px; font-weight: bold; margin-top: 24px; margin-bottom: 24px">
          {{ strtoupper($data['tiket']->Counter_Category->name) }}
        </div>
        <div style="display: flex; gap: 4px; justify-content: center; margin-left: -10%">
          <div style="flex: 1 1 0%; border-right: 1px solid #1c1c1c; text-align: center; padding: 5%; width: 100%">
            <h5 style="font-size: 1rem; margin-top: 24px; color: #1c1c1c">Kode<br>Booking:</h5>
            <h1 style="font-size: 2rem; color: #1c1c1c;font-family: 'Roboto Mono', sans-serif;">
              {{ $data['tiket']->booking_code }}</h1>
          </div>
          <div style="flex: 1 1 0%; text-align: center; padding: 5%; width: 100%">
            <h5 style="font-size: 1rem; margin-top: 24px; color: #1c1c1c">Nomor<br>Antrian:</h5>
            <h1 style="font-size: 2rem; color: #1c1c1c;font-family: 'Roboto Mono', sans-serif">
              {{ $data['tiket']->Counter_Category->code . '-' . $data['tiket']->queue_number }}</h1>
          </div>
        </div>
        <p style="color: #1c1c1c; margin: 1rem 1rem; font-weight: 600">
          Masukkan Kode Booking ke
          Mesin <br> untuk Aktivasi
          Nomor Antrian
        </p>
        <span style="font-size: 14px; margin-top: 24px">Dibuat pada:
          <b>{{ \Carbon\Carbon::parse($data['tiket']->created_at)->translatedFormat('d F Y') }}</b> |
          <b>{{ \Carbon\Carbon::parse($data['tiket']->created_at)->translatedFormat('H:i') }} WIB</b></span>
        <br>
        <span style="font-size: 14px">Berlaku hingga:
          <b>{{ \Carbon\Carbon::parse($data['tiket']->created_at)->addDay()->translatedFormat('d F Y') }}</b>
          |
          <b>00:00 WIB</b></span>
        <hr>
      </div>
    </div>
  </section>
@endsection
