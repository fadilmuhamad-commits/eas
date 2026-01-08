@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
  <style>
    #main {
      padding-top: 2.5vh !important;
    }

    #dashboard-datepicker::after {
      content: '\F282';
      font-family: 'bootstrap-icons';
      font-size: 12px;
      font-weight: bold;
      position: absolute;
      right: -16px;
      pointer-events: none;
    }

    #dashboard-datepicker input {
      border: none;
    }
  </style>

  <section class="h-100 d-flex flex-column gap-4">
    {{-- Card 1 --}}
    <div class="d-flex flex-column gap-2">
      {{-- Datepicker --}}
      <div id="dashboard-datepicker" class="d-flex align-items-center position-relative" style="height: 31px; width: 7rem;">
        <input name="date-input" type="text" class="btn px-0 fw-bold"
          style="height: inherit; width: inherit; font-size: 14px;" data-input>
      </div>

      {{-- Stats --}}
      <div class="d-flex flex-lg-row flex-column flex-wrap gap-4">
        {{-- Total Pelanggan Booking --}}
        <div class="card pt-2 pb-3 rounded-4 col text-center">
          <h1 class="fw-bold mt-1 display-1">{{ count($booking) }}</h1>
          <div class="d-flex justify-content-center align-items-center gap-3">
            <i class="bi bi-file-person-fill text-secondary fs-4"></i>
            <div>Total Pelanggan Booking</div>
          </div>
        </div>

        {{-- Total Pengunjung --}}
        <div class="card pt-2 pb-3 rounded-4 col text-center">
          <h1 class="fw-bold mt-1 display-1">{{ count($pengunjung) }}</h1>
          <div class="d-flex justify-content-center align-items-center gap-3">
            <i class="bi bi-ticket-detailed-fill text-secondary fs-4"></i>
            <div>Total Tiket Tercetak</div>
          </div>
        </div>

        <div class="card pt-2 pb-3 rounded-4 col text-center">
          <h1 class="fw-bold mt-1 display-1">{{ count($pelanggan) }}</h1>
          <div class="d-flex justify-content-center align-items-center gap-3">
            <i class="bi bi-person-fill-check text-secondary fs-4"></i>
            <div>Total Pelanggan Terlayani</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card 2 --}}
    <div class="col">
      <div class="card rounded-4 h-100 d-flex flex-row flex-wrap p-3 position-relative">

        {{-- Select Year --}}
        <div class="d-flex align-items-center position-absolute start-50 translate-middle-x" style="top: 16px">
          <span style="font-size: 14px;">Tahun</span>
          <select id="year-select" onchange="updateUrl()" class="form-select border-0 py-0 ps-1 fw-bold"
            style="font-size: 14px; padding-right: 30px">
            @for ($i = $config->created_at->year; $i <= now()->year; $i++)
              <option value="{{ $i }}" @selected($selectedYear ? $i == $selectedYear : $i == now()->year)>{{ $i }}</option>
            @endfor
          </select>
        </div>

        {{-- Chart-Data Kunjungan --}}
        <div class="d-flex align-items-center justify-content-center col mt-lg-0 mt-4">
          <div class="position-relative" style="width: 480px">
            <canvas id="data-kunjungan"></canvas>

            @if (empty(array_filter($monthlyCounts)))
              <div class="position-absolute start-50 top-50 translate-middle text-medium"
                style="white-space: nowrap; font-size: 14px; pointer-events: none;">Data tidak ditemukan.</div>
            @endif
          </div>
        </div>

        {{-- Chart-Data Category --}}
        <div class="d-flex align-items-center justify-content-center col">
          <div class="position-relative" style="width: 272px">
            <canvas id="data-category"></canvas>

            @if ($categoryL->isEmpty())
              <div class="position-absolute start-50 top-50 translate-middle text-medium"
                style="white-space: nowrap; font-size: 14px; pointer-events: none;">Data tidak ditemukan.
              </div>
            @endif
          </div>

          {{-- Select Month --}}
          <div class="d-flex align-items-center">
            <span style="font-size: 14px; margin-top: 2px;">Bulan</span>
            <select id="month-select" onchange="updateUrl()" class="form-select border-0 py-0 ps-1 fw-bold"
              style="font-size: 14px;">
              @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" @selected($selectedMonth ? $i == $selectedMonth : $i == now()->month)>
                  {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
              @endfor
            </select>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    // let config = @json($config);

    // function ajaxGet() {
    //   $.ajax({
    //     url: 'https://demo.smartcoop.id/v1.5/api/coop/dashboard/anggota/472007007/cek',
    //     type: 'GET',
    //     headers: {
    //       'Content-Type': 'application/json',
    //       'Access-Control-Allow-Origin': '*'
    //     },
    //     success: function(res) {
    //       console.log(res);
    //     },
    //     error: function(xhr, status, error) {
    //       console.error(error);
    //       // console.log(config.partner_api.replace('{slug}', 472007007));
    //     }
    //   })
    // }
    // ajaxGet();

    let urlParams = new URLSearchParams(window.location.search);
    let today = urlParams.get('date');

    flatpickr('#dashboard-datepicker', {
      wrap: true,
      dateFormat: 'd F Y',
      defaultDate: today ? today : new Date(),
      disableMobile: "true",
      locale: 'id',
      onChange: function(selectedDates, dateStr, instance) {
        updateUrl();
      },
    })

    function updateUrl() {
      let selectedDate = document.querySelector('#dashboard-datepicker input').value;
      let selectedYear = document.querySelector('#year-select').value;
      let selectedMonth = document.querySelector('#month-select').value;

      let baseUrl = "{{ route('dashboard') }}";
      let urlParams = [];

      if (selectedDate) {
        urlParams.push('date=' + selectedDate);
      }

      if (selectedYear) {
        urlParams.push('year=' + selectedYear);
      }

      if (selectedMonth) {
        urlParams.push('month=' + selectedMonth);
      }

      window.location.href = baseUrl + (urlParams.length > 0 ? '?' + urlParams.join('&') : '');
    }

    // DASHBOARD CHART
    let monthlyCounts = {!! json_encode(array_values($monthlyCounts)) !!};
    let category = {!! json_encode($categoryL) !!};

    let categoryName = [];
    let categoryData = [];

    category.map((e) => {
      categoryName.push(e.name);
      categoryData.push(e.tiket_count);
    })

    document.addEventListener('DOMContentLoaded', function() {
      const ctxKunjungan = document.getElementById('data-kunjungan').getContext('2d');
      const kunjunganChart = new Chart(ctxKunjungan, {
        type: 'bar',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov",
            "Des"
          ],
          datasets: [{
            label: 'Jumlah Kunjungan',
            backgroundColor: [
              "#FF2865", "#FF639F", "#FFB26B", "#FFF29C", "#FEFF89", "#FF9F68",
              "#F85959", "#7C203A", "#CD7856", "#E4663A", "#FFE35E", "#FBFF7C"
            ],
            data: monthlyCounts,
          }]
        },
        options: {
          plugins: {
            legend: {
              display: false,
            },
            title: {
              display: true,
              text: 'Data Kunjungan Terbanyak',
              font: {
                family: 'Inter',
                weight: 500,
              },
            },
          },
        },
      });

      const ctxCategory = document.getElementById('data-category').getContext('2d');
      const categoryChart = new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
          labels: categoryName,
          datasets: [{
            label: "Jumlah Pelayanan",
            data: categoryData,
          }, ],
        },
        options: {
          plugins: {
            legend: {
              position: "bottom",
            },
            title: {
              display: true,
              text: "Data Kategori dengan Pelayanan Terbanyak",
              font: {
                family: "Inter",
                weight: 500,
              },
            },
          },
        },
      });
    });
  </script>
@endsection
