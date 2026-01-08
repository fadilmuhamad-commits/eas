@extends('layouts.bar')
@section('title', 'Pilih Opsi')

@section('col')

  <style>
    #btn-pilih {
      width: 100%;
      text-align: center;
      margin-top: 50px;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    #container {
      padding: 5% 5%;
      margin: 3%;
      position: relative;
    }

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

    #btn-back {
      font-size: 20px;
      position: absolute;
      top: 4%;
      left: 2%;
    }

    @media(max-width: 768px) {
      #container {
        padding: 7% 8%;
      }

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
      #btn-pilih {
        flex-wrap: wrap;
      }

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

      #btn-back {
        font-size: 10px;
        top: -18%;
        left: 0;
      }
    }

    @media(max-width: 300px) {
      #btn-pilih {
        flex-wrap: wrap;
      }

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

      #btn-back {
        font-size: 8px;
        top: -14%;
        left: 0;
      }
    }
  </style>

  <div class="bg-white shadow rounded d-flex flex-column align-items-center justify-content-center" id="container">
    <a href="{{ route('cetak') }}">
      <button type="submit" class="btn bg-danger p-3 rounded text-white fw-bold fst-italic" id="btn-back">
        <i class="bi bi-arrow-left-circle-fill"></i>
        CANCEL
      </button>
    </a>

    <h1 class="fw-bold text-center text-black">{{ $counter_category->name }}</h1>
    <h1 class="text-center text-black fs-2">Apakah Anda pernah mendaftar disini?</h1>

    <div id="btn-pilih" class="d-flex justify-content-center flex-row">
      @if ($config->status == 1)
        <!-- Jika status adalah 1 -->
        <form action="{{ route('cetak.store', $counter_category->id) }}" method="POST" class="d-flex" id="form-tidak">
          @csrf
          <button class="btn text-white fw-bold" id="btn-tidak">TIDAK, SAYA TIDAK MEMILIKI NOMOR
            REGISTRASI</button>
        </form>
      @elseif($config->status == 2)
        <!-- Jika status adalah 2 -->
        <div class="d-flex">
          <button class="btn text-white fw-bold" id="btn-tidak" data-bs-toggle="modal"
            data-bs-target="#modal-input-identitas">
            TIDAK, SAYA TIDAK MEMILIKI NOMOR REGISTRASI
          </button>
        </div>
      @endif

      @if ($config->partnership == 1)
        {{-- mode non partnership --}}
        <form action="{{ route('tanya-booking', $counter_category->id) }}" method="POST" class="d-flex"
          id="form-memiliki">
          @csrf
          <button class="btn btn-success fw-bold" id="btn-memiliki">
            YA, SAYA MEMILIKI NOMOR REGISTRASI
          </button>
        </form>
      @else
        {{-- mode partnership --}}
        <div class="d-flex">
          <button class="btn btn-success fw-bold" id="btn-memiliki" data-bs-toggle="modal"
            data-bs-target="#modal-partner">
            YA, SAYA MEMILIKI NOMOR REGISTRASI / ANGGOTA
          </button>
        </div>
      @endif
    </div>
  </div>

  {{-- MODAL INPUT IDENTITAS --}}
  @if ($config->status == 2)
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
                <form action="{{ route('cetak.store', $counter_category->id) }}" method="POST" id="form-unregis">
                  @csrf
                  <button style="width: 208px; border: 1px solid #832424; color: #832424;"
                    class="btn btn-sm text-center p-3 fw-semibold" id="btn-unregis">Cetak (Tanpa
                    identitas)</button>
                </form>
              </div>
              <hr>
              <form class="w-100 mt-2" action="{{ route('cetak.register', $counter_category->id) }}" method="POST"
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
  @if ($config->partnership == 2)
    <div class="modal fade" id="modal-partner" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="text-center pt-4 fs-2 fw-bold">Pilih Metode</div>
          <hr>
          <div class="modal-body d-flex justify-content-between gap-3">
            <form
              action="{{ route('tanya-booking', ['counter_category' => $counter_category->id, 'type' => 'default']) }}"
              method="POST" class="d-flex" id="form-memiliki">
              @csrf
              <button class="btn btn-primary p-5 fw-semibold fs-4">NOMOR REGISTRASI</button>
            </form>
            <form
              action="{{ route('tanya-booking', ['counter_category' => $counter_category->id, 'type' => 'partner']) }}"
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

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      @if ($config->status == 1)
        document.getElementById("form-tidak").addEventListener("submit", function() {
          document.getElementById("btn-tidak").disabled = true;
        });
      @endif

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
