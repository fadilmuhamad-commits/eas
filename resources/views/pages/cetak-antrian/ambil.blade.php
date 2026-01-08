@extends('layouts.bar')
@section('title', 'Cetak')

@section('col')
  <style>
    #btn-tidak {
      padding: 2% 6%;
      background-color: #832424;
      font-size: 24px;
      width: 75%
    }

    #btn-memiliki {
      padding: 2% 12%;
      font-size: 24px;
    }

    @media(max-width: 768px) {
      #btn-tidak {
        font-size: 18px;
      }

      #btn-memiliki {
        font-size: 18px;
      }

      #btn-back {
        font-size: 15px;
        top: -25%;
        left: 0;
      }
    }

    @media(max-width: 590px) {

      #btn-tidak {
        font-size: 14px;
      }

      #btn-memiliki {
        font-size: 14px;
      }

      #btn-back {
        font-size: 13px;
        top: -25%;
        left: 0;
      }
    }

    @media(max-width: 465px) {
      #btn-tidak {
        font-size: 14px;
      }

      #btn-memiliki {
        font-size: 14px;
      }

      #form-tidak {
        justify-content: center;
      }

      #form-memiliki {
        justify-content: center;
      }
    }

    @media(max-width: 300px) {
      #btn-tidak {
        font-size: 14px;
      }

      #btn-memiliki {
        font-size: 14px;
      }

      #form-tidak {
        justify-content: center;
      }

      #form-memiliki {
        justify-content: center;
      }
    }
  </style>
  @php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
  @endphp

  <div class="position-relative w-100 h-100 d-flex flex-column px-4">
    <div class="fs-2 mt-4 fw-bold mb-lg-0 mb-4 mx-auto">Ambil Nomor Antrian</div>

    @if (count($categories) > 1 ||
            (count($categories) == 1 && !empty($counters) && count($counters->Counters) != 0 && $counterStatusId == 1))
      <div class="d-flex flex-column flex-lg-row col">
        @if (count($categories) == 1)
          <div class="col d-flex flex-column gap-3 align-items-center justify-content-center">
            <h4 class="fw-bold fs-4">Pilih metode cetak</h4>

            @if ($config->status == 1)
              <!-- Jika status adalah 1 -->
              <form action="{{ route('cetak.store', $counters->id) }}" method="POST" style="width: 80%" id="form-tidak">
                @csrf
                <button class="btn text-white fw-bold w-100 py-3" id="btn-tidak">CETAK LANGSUNG</button>
              </form>
            @elseif($config->status == 2)
              <!-- Jika status adalah 2 -->
              <button class="btn text-white fw-bold py-3" id="btn-tidak" data-bs-toggle="modal" style="width: 80%"
                data-bs-target="#modal-input-identitas">
                CETAK LANGSUNG
              </button>
            @endif

            @if ($config->partnership == 1)
              {{-- mode non partnership --}}
              <form action="{{ route('tanya-booking', $counters->id) }}" method="POST" style="width: 80%"
                id="form-memiliki">
                @csrf
                <button class="btn btn-success fw-bold w-100" id="btn-memiliki">
                  CETAK MENGGUNAKAN NOMOR REGISTRASI
                </button>
              </form>
            @else
              {{-- mode partnership --}}
              <div style="width: 80%">
                <button class="btn btn-success fw-bold w-100" id="btn-memiliki" data-bs-toggle="modal"
                  data-bs-target="#modal-partner">
                  CETAK MENGGUNAKAN NOMOR REGISTRASI / ANGGOTA
                </button>
              </div>
            @endif
          </div>
        @else
          <div class="col d-flex align-items-center justify-content-center">
            <button type="button" class="btn btn-primary fs-2 fw-semibold" style="width: 320px; height: 180px;"
              data-bs-toggle="modal" data-bs-target="#modal">
              Kategori Loket
            </button>
          </div>
        @endif

        <div class="my-4" style="border: solid 1px rgba(0, 0, 0, 0.3)"></div>

        <div class="col my-auto">
          <div class="text-center d-flex flex-column gap-3">
            <span class="fs-5 fw-bold">Daftarkan identitasmu disini</span>
            <div class="mx-auto">
              {!! QrCode::size(130)->encoding('UTF-8')->generate(route('booking')) !!}
            </div>
          </div>
          <div class="text-center mt-4">
            <h1 class="fs-3 text-black fw-bold">Masukkan Kode Booking</h1>
            <form action="{{ route('cetak.nomor-booking') }}" method="POST"
              class="w-100 mt-4 d-flex flex-column align-items-center" id="form-booking">
              @csrf
              <input oninput="this.value = this.value.toUpperCase()" type="text"
                class="form-control border-black py-4 text-center w-75" style="font-size: 14px;" name="booking_code"
                placeholder="Kode Booking" maxlength="6" autocomplete="off">
              <button class="btn btn-primary mt-4 fw-bold w-75" style="padding: 10px 10px" id="btn-booking">CETAK</button>
            </form>
          </div>
        </div>
      </div>
    @elseif (count($categories) == 1 && $counterStatusId == 2)
      <div
        class="p-4 rounded-2 bg-danger text-white fs-4 fw-semibold m-auto d-flex gap-4 align-items-center justify-content-center">
        <i class="bi bi-exclamation-circle fs-1"></i>
        Sedang tidak melayani.
      </div>
    @else
      <div
        class="p-4 rounded-2 bg-danger text-white fs-4 fw-semibold m-auto d-flex gap-4 align-items-center justify-content-center">
        <i class="bi bi-exclamation-circle fs-1"></i>
        Tidak ada loket / kategori loket yang ditemukan.
      </div>
    @endif
  </div>

  {{-- QR MODAL --}}
  {{-- <div class="modal fade" tabindex="-1" id="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-3">
        <div class="modal-body text-center">
          <img src="{{ asset('storage/' . $logo1) }}" width="30%" alt="{{ $nama_instansi }}">
          <div class="py-3 mt-3">
            {!! QrCode::size(250)->generate(route('booking')) !!}
          </div>
          <h3 class="fw-bold">SCAN UNTUK MENDAPATKAN<br>NOMOR BOOKING</h3>
        </div>
      </div>
    </div>
  </div> --}}

  {{-- MODAL INPUT IDENTITAS --}}
  @if ($config->status == 2 && !empty($counters) && count($counters->Counters) != 0 && $counterStatusId == 1)
    <div class="modal fade" tabindex="-1" id="modal-input-identitas">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content py-2 px-5">
          <div class="modal-body px-0">
            @csrf
            <div class="fs-3 fw-bold text-black text-center">
              Input Identitas
              <hr>
            </div>
            <div class="mb-3">
              <div class="d-flex flex-column align-items-center">
                <form action="{{ route('cetak.store', $counters->id) }}" method="POST" id="form-unregis">
                  @csrf
                  <button style="width: 208px; border: 1px solid #832424; color: #832424;"
                    class="btn btn-sm text-center p-3 fw-semibold" id="btn-unregis">
                    Cetak (Tanpa identitas)
                  </button>
                </form>
              </div>
              <hr>
              <form class="w-100 mt-2" action="{{ route('cetak.register', $counters->id) }}" method="POST"
                id="form-regis">
                @csrf
                <label for="nama-lengkap" class="form-label text-black fw-semibold">Nama Lengkap</label>
                <input type="text" autocomplete="name" class="form-control py-2" style="font-size: 14px"
                  id="nama-lengkap" name="name" placeholder="Masukkan nama lengkap">
                @error('name')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
              <div class="col">
                <label for="email" class="form-label text-black fw-semibold">Email</label>
                <input type="email" autocomplete="email" class="form-control py-2" style="font-size: 14px"
                  id="email" name="email" placeholder="Masukkan email">
                @error('email')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
              <div class="col">
                <label for="nomor-telepon" class="form-label text-black fw-semibold">Nomor Telepon</label>
                <input type="tel" pattern="[^a-zA-Z]*" autocomplete="tel" class="form-control py-2"
                  style="font-size: 14px" id="nomor-telepon" name="phone_number" placeholder="Masukkan nomor">
                @error('phone_number')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
              <div class="col">
                <label for="birth_place" class="form-label text-black fw-semibold">Tempat Lahir</label>
                <input type="text" class="form-control py-2" style="font-size: 14px" id="birth_place"
                  name="birth_place" placeholder="Masukkan tempat lahir">
                @error('birth_place')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
              <div class="col">
                <label for="birth_date" class="form-label text-black fw-semibold">Tanggal
                  Lahir</label>
                <input type="date" autocomplete="bday" class="form-control py-2" style="font-size: 14px"
                  id="birth_date" name="birth_date" max="9999-12-31">
                @error('birth_date')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <div class="mb-4">
              <label for="address" class="form-label text-black fw-semibold">Alamat</label>
              <input type="text" autocomplete="street-address" class="form-control py-2" style="font-size: 14px"
                id="address" name="address" placeholder="Masukkan alamat">
              @error('address')
                <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
              @enderror
            </div>

            <button data-bs-toggle="modal" data-bs-target="#myModal" type="submit"
              class="btn btn-primary w-100 fw-bold mb-3" id="btn-regis">Cetak</button>
            </form>
          </div>
          <button type="button" class="btn-close position-absolute" style="top: 24px; right: 24px;"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  @endif

  {{-- MODAL PARTNER --}}
  @if ($config->partnership == 2 && !empty($counters) && count($counters->Counters) != 0 && $counterStatusId == 1)
    <div class="modal fade" id="modal-partner" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="text-center pt-4 fs-2 fw-bold">Pilih Metode</div>
          <hr>
          <div class="modal-body d-flex justify-content-between gap-3">
            <form action="{{ route('tanya-booking', ['counter_category' => $counters->id, 'type' => 'default']) }}"
              method="POST" class="d-flex" id="form-memiliki">
              @csrf
              <button class="btn btn-primary p-5 fw-semibold fs-4">NOMOR REGISTRASI</button>
            </form>
            <form action="{{ route('tanya-booking', ['counter_category' => $counters->id, 'type' => 'partner']) }}"
              method="POST" class="d-flex" id="form-memiliki">
              @csrf
              <button class="btn bg-primary p-5 fw-semibold fs-4 text-white">
                NOMOR ANGGOTA
              </button>
            </form>
          </div>
          <button type="button" class="btn-close position-absolute" style="top: 24px; right: 24px;"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  @endif

  {{-- KATEGORI MODAL --}}
  @if (count($categories) > 1)
    <div class="modal fade" tabindex="-1" id="modal">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content" style="height: 99vh;">
          <div class="modal-body py-4">
            <x-ambil-antrian-cards :client="true" :data="$categories" />
            <button type="button" class="btn-close position-absolute" style="top: 24px; right: 24px;"
              data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      @if ($config->status == 1 && count($categories) == 1)
        document.getElementById("form-tidak").addEventListener("submit", function() {
          document.getElementById("btn-tidak").disabled = true;
        });
      @endif

      document.getElementById("form-booking").addEventListener("submit", function() {
        document.getElementById("btn-booking").disabled = true;
      });

      @if ($config->status == 2)
        document.getElementById("form-unregis").addEventListener("submit", function() {
          document.getElementById("btn-unregis").disabled = true;
        });

        document.getElementById("form-regis").addEventListener("submit", function() {
          document.getElementById("btn-regis").disabled = true;
        });
      @endif

    });
  </script>
@endsection
