@extends('layouts.bar')
@section('title', 'Masukkan No. Registrasi')

@section('col')
  <style>
    #container {
      padding: 3% 15% !important;
      margin: 3%;
      position: relative;
    }

    #modal-nomor-booking {
      font-size: 33px
    }

    #btn-back {
      position: absolute;
      top: 4%;
      left: 2%;
    }

    #text-kembali {
      font-size: 20px
    }

    @media(max-width: 1200px) {
      #container {
        padding: 7% 5%;
      }

      #btn-back {
        position: absolute;
        top: -18%;
        left: 0;
      }

      #text-kembali {
        font-size: 15px
      }
    }

    @media(max-width: 768px) {
      #container {
        padding: 7% 5%;
      }

      #btn-back {
        position: absolute;
        top: -18%;
        left: 0;
        font-size: 12px;
      }
    }

    @media(max-width: 340px) {
      #container {
        padding: 20px 20px !important;
        margin: 3%;
        position: relative;
      }

      #btn-back {
        position: absolute;
        top: -16%;
        left: 0;
      }
    }
  </style>

  <div id="container" class="bg-white shadow rounded py-5 d-flex flex-column align-items-center justify-content-center">
    <form action="{{ $counterLength == 1 ? route('cetak') : route('tanya-opsi', $counter_category->id) }}"
      method="{{ $counterLength == 1 ? 'GET' : 'POST' }}">
      @if ($counterLength != 1)
        @csrf
      @endif
      <button type="submit" id="btn-back" class="btn bg-danger p-3 rounded">
        <i class="bi bi-arrow-left-circle-fill text-white fw-bold" id="text-kembali"> KEMBALI</i>
      </button>
    </form>
    <h1 class="fw-bold text-center text-black">{{ $counter_category->name }}</h1>
    <h1 class="fs-3 text-center text-black">Masukkan Nomor {{ $type == 'partner' ? 'Anggota' : 'Registrasi' }}</h1>
    <form action="{{ $type == 'partner' ? '' : route('cetak.nomor-regis', $counter_category->id) }}" method="POST"
      class="w-100 text-center" id="form-cetak">
      @csrf
      <div style="padding: 0 10%">
        <input oninput="this.value = this.value.toUpperCase()" type="text" class="form-control py-4 mt-3 text-center"
          style="font-size: 14px; border-color: #1c1c1c" name="no_regis" id="no_regis"
          placeholder="Nomor {{ $type == 'partner' ? 'Anggota' : 'Registrasi' }}"
          maxlength="{{ $type == 'partner' ? '' : 9 }}" autocomplete="off">
      </div>
      <button class="btn btn-primary mt-5 fw-bold w-75 position-relative" style="padding: 10px 10px" id="btn-cetak">
        CETAK

        <div id="cetak-spinner"
          class="position-absolute top-0 start-0 h-100 w-100 bg-black bg-opacity-50 d-flex justify-content-center align-items-center">
          <div class="spinner-border text-secondary" style="width: 2rem; height: 2rem" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </button>
    </form>
  </div>

  <div id="partner-toast" class="toast align-items-center border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    function showCetakSpinner() {
      $('#cetak-spinner').removeClass('d-none');
      $('#cetak-spinner').addClass('d-flex');
    }

    function hideCetakSpinner() {
      $('#cetak-spinner').addClass('d-none');
      $('#cetak-spinner').removeClass('d-flex');
    }
    hideCetakSpinner();

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-cetak").addEventListener("submit", function() {
        document.getElementById("btn-cetak").disabled = true;
      });
    });

    // partner form
    @if ($type == 'partner')
      $('#form-cetak').submit((e) => {
        e.preventDefault();
        findMember();
      })

      function findMember() {
        showCetakSpinner();
        let partnerAPI = @json($config->partner_api);
        let inputValue = $('#no_regis').val() ? $('#no_regis').val() : 'return404';

        if (partnerAPI) {
          $.ajax({
            url: '{{ route('check-member', ['kode' => 9999]) }}'.replace(9999, inputValue),
            type: 'GET',
            success: function(res) {
              if (res.status == true || (res.status == 200 && res.data != null)) {
                let data = {
                  'registration_code': res.data.kode,
                  'name': res.data.nama,
                  'email': res.data.email,
                  'address': res.data.alamat,
                  'phone_number': res.data.no_telepon,
                  'birth_date': res.data.tanggal_lahir,
                  'birth_place': res.data.tempat_lahir,
                }

                submitForm(data);
              } else {
                $('#partner-toast .d-flex .toast-body').text('Nomor Anggota tidak valid');
                $('#partner-toast').addClass('text-bg-danger');
                $('#partner-toast').toast('show');
                $('#btn-cetak').prop('disabled', false);
                hideCetakSpinner();
              }
            },
            error: function(xhr, status, error) {
              console.error(error);
              $('#btn-cetak').prop('disabled', false);
              hideCetakSpinner();
            }
          })
        } else {
          $('#partner-toast .d-flex .toast-body').text('API Not Found');
          $('#partner-toast').addClass('text-bg-danger');
          $('#partner-toast').toast('show');
          $('#btn-cetak').prop('disabled', false);
          hideCetakSpinner();
        }
      }

      function submitForm(data) {
        $.ajax({
          url: '{{ route('cetak.nomor-anggota', ['counter_category' => $counter_category->id, 'userData' => 9999]) }}'
            .replace(
              9999, JSON.stringify(data)),
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(res) {
            window.location.href = "{{ route('wait', ['token' => 9999]) }}".replace(9999, res.token);
          },
          error: function(xhr, status, error) {
            console.error(error);
            $('#btn-cetak').prop('disabled', false);
            hideCetakSpinner();
          }
        })
      }
    @endif
  </script>

  {{-- MODAL
  <div class="modal fade" tabindex="-1" id="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content p-3">
        <div class="modal-body text-center">
          <p class="text-center">Apakah Nomor Booking Anda<br><b class="text-primary fw-bold text-center"
              id="modal-nomor-booking">2109381209?</b>
          </p>
        </div>
        <div class="modal-footer border-0 d-flex justify-content-center pt-0">
          <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">Ya</button>
        </div>
      </div>
    </div>
  </div> --}}
@endsection
