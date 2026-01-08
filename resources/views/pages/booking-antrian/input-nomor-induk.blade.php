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

    #btn-booking {
      padding-inline: 96px;
    }

    #booking-container {
      padding: 32px 56px;
    }

    @media (max-width: 640px) {
      #booking-container {
        padding: 24px 24px;
      }
    }

    @media(max-width: 330px) {
      #btn-booking {
        padding-inline: 0;
      }
    }
  </style>

  <div class="d-flex justify-content-center align-items-center position-relative" id="booking-layout">
    <div class="bg-white rounded mx-3 my-2 d-flex justify-content-center align-items-center" id="booking-container">

      <form action="{{ $type == 'partner' ? '' : route('booking.induk') }}" method="POST"
        class="d-flex flex-column align-items-center" id="form-booking">
        @csrf
        <div class="mb-5 d-flex flex-column">
          @if (file_exists(public_path('storage/' . $logo1)) && $config->logo1)
            <img src="{{ asset('storage/' . $logo1) }}" class="object-fit-contain" style="height: 80px"
              alt="{{ $instanceName }}">
          @endif
          <h2 class="fw-bold text-black text-center py-4">Masukkan Nomor
            {{ $type == 'partner' ? 'Anggota' : 'Registrasi' }}</h2>

          <div>
            <label for="registration_code" class="form-label text-black fw-semibold">Nomor
              {{ $type == 'partner' ? 'Anggota' : 'Registrasi' }}</label>
            <input type="text" class="form-control py-2 text-center" style="font-size: 14px;" id="registration_code"
              name="registration_code" placeholder="123456789" maxlength="{{ $type == 'partner' ? '' : 9 }}"
              autocomplete="off"
              oninput="{{ $type == 'partner' ? '' : "this.value = this.value.replace(/[^0-9]/g, '')" }}"
              value="{{ Session::get('registration_code') }}">
            @error('registration_code')
              <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
            @enderror
          </div>

          <div class="mt-4">
            <label for="birth_date" class="form-label text-black fw-semibold">Tanggal Lahir</label>
            <input type="date" class="form-control py-2 text-center" style="font-size: 14px;" id="birth_date"
              name="birth_date" max="9999-12-31">
            @error('birth_date')
              <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
            @enderror
          </div>

          <div class="mt-4">
            <label for="counters" class="form-label text-black fw-semibold">Kategori</label>
            <select id="counters" name="counters" class="form-select py-2" style="font-size: 14px;">
              <option value="">--Pilih Kategori--</option>
              @foreach ($counters as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
            @error('counters')
              <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="d-flex flex-sm-row flex-col-reverse column-gap-3 flex-wrap-reverse row-gap-2 w-100">
          <a href="{{ route('booking') }}" class="btn col bg-danger text-white d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left-circle-fill"></i>
            <span class="fw-semibold mx-auto">Kembali</span>
          </a>
          <button type="submit" class="btn btn-primary col fw-semibold position-relative" id="btn-booking">
            Booking

            <div id="booking-spinner"
              class="position-absolute top-0 start-0 h-100 w-100 bg-black bg-opacity-50 d-flex justify-content-center align-items-center">
              <div class="spinner-border text-secondary" style="width: 2rem; height: 2rem" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="partner-toast" class="toast text-bg-danger align-items-center border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    function showBookingSpinner() {
      $('#booking-spinner').removeClass('d-none');
      $('#booking-spinner').addClass('d-flex');
    }

    function hideBookingSpinner() {
      $('#booking-spinner').addClass('d-none');
      $('#booking-spinner').removeClass('d-flex');
    }
    hideBookingSpinner();

    function showToast(message) {
      $('#partner-toast .d-flex .toast-body').text(message);
      $('#partner-toast').toast('show');

      $('#btn-booking').prop('disabled', false);
      hideBookingSpinner();
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-booking").addEventListener("submit", function() {
        document.getElementById("btn-booking").disabled = true;
      });
    });

    @if ($type == 'partner')
      $('#form-booking').submit((e) => {
        e.preventDefault();
        findMember();
      })

      function findMember() {
        showBookingSpinner();
        let partnerAPI = @json($config->partner_api);
        let inputValue = $('#registration_code').val() ? $('#registration_code').val() : 'return404';
        let dateValue = $('#birth_date').val() ? $('#birth_date').val() : '1970-01-01';
        let counterCategoryValue = $('#counters').val() ? $('#counters').val() : null;

        if (partnerAPI) {
          $.ajax({
            url: '{{ route('check-member', ['kode' => 9999]) }}'.replace(9999, inputValue),
            type: 'GET',
            success: function(res) {
              if (res.status == true || (res.status == 200 && res.data != null)) {
                if (counterCategoryValue != null) {
                  if (res.data.tanggal_lahir == dateValue) {
                    let data = {
                      'registration_code': res.data.kode,
                      'name': res.data.nama,
                      'email': res.data.email,
                      'address': res.data.alamat,
                      'phone_number': res.data.no_telepon,
                      'birth_date': res.data.tanggal_lahir,
                      'birth_place': res.data.tempat_lahir,
                      'counter_category_id': counterCategoryValue
                    }

                    submitForm(data);
                  } else {
                    showToast('Tanggal lahir salah');
                  }
                } else {
                  showToast('Kategori wajib diisi');
                }
              } else {
                showToast('Nomor Anggota tidak valid');
              }
            },
            error: function(xhr, status, error) {
              console.error(error);
              $('#btn-booking').prop('disabled', false);
              hideBookingSpinner();
            }
          })
        } else {
          showToast('API Not Found');
        }
      }

      function submitForm(data) {
        $.ajax({
          url: '{{ route('booking.anggota', ['userData' => 9999]) }}'.replace(9999, JSON.stringify(data)),
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(res) {
            if (res.booked_today == true) {
              showToast('Anda sudah membuat tiket hari ini, coba lagi nanti.');
            } else {
              window.location.href = "{{ route('booking.success', ['token' => 9999]) }}".replace(9999, res.token);
            }
          },
          error: function(xhr, status, error) {
            console.error(error);
            $('#btn-booking').prop('disabled', false);
            hideBookingSpinner();
          }
        })
      }
    @endif
  </script>
@endsection
