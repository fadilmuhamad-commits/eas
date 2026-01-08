@extends('layouts.admin')
@section('title', 'Riwayat Kunjungan')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">riwayat-kunjungan</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Riwayat Kunjungan</h5>

          {{-- FILTER --}}
          <div class="d-flex gap-3" style="flex-wrap: wrap">
            {{-- Select Loket --}}
            <x-select-loket :loket="$counters" :selected-loket="$selectedCounter" />
            <x-select-category :categories="$categories" :selected-category="$selectedCounterCategory" />

            {{-- Datepicker --}}
            <div id="riwayatKunjungan-datepicker" class="datepicker btn-group d-flex align-items-center position-relative"
              style="height: 31px;">
              <input name="date-input" type="text" class="btn btn-outline-medium text-start pe-0"
                style="height: inherit; width: 7.25rem; font-size: 14px;" placeholder="dd mmm yyyy" data-input>

              @if ($selectedDate)
                <a class="fs-5 btn btn-danger d-flex align-items-center justify-content-center"
                  style="height: inherit; aspect-ratio: 1 / 1;" title="clear" data-clear>
                  <i class="bi bi-x"></i>
              @endif
              </a>
            </div>

            {{-- Search --}}
            <x-search :sort="$sort" :order="$order" :search="$search" :search-by="$searchBy" :options="[
                ['value' => 'name', 'label' => 'Nama'],
                ['value' => 'email', 'label' => 'Email'],
                ['value' => 'phone_number', 'label' => 'No. Telp'],
            ]" />

            {{-- Refresh --}}
            <x-filter-reset />
          </div>
        </div>

        <div class="ms-auto ">
          <x-data-count title="Total:" value="{{ $length }}" />
        </div>

      </div>

      <x-table :sort="$sort" :order="$order" :heading="[
          ['title' => 'No'],
          ['title' => 'Nama', 'sort' => 'name'],
          ['title' => 'Email', 'sort' => 'email'],
          ['title' => 'No. Telp', 'sort' => 'phone_number'],
          ['title' => 'Alamat', 'sort' => 'address'],
          ['title' => 'TTL', 'sort' => 'birth_date'],
          ['title' => 'Kategori Tiket', 'sort' => 'ticket_category_name'],
          ['title' => 'No. Booking', 'sort' => 'booking_code'],
          ['title' => 'Group', 'sort' => 'group_name'],
          ['title' => 'Loket', 'sort' => 'counter_name'],
          ['title' => 'No. Antrian', 'sort' => 'queue_number'],
          ['title' => 'Durasi', 'sort' => 'duration'],
          ['title' => 'Catatan', 'sort' => 'note'],
          ['title' => 'Selesai Pada', 'sort' => 'updated_at'],
      ]">
        <tbody>
          @if ($length == 0)
            <tr>
              <td colspan="100">
                Data tidak ditemukan.
              </td>
            </tr>
          @endif

          @php
            $i = ($tickets->currentPage() - 1) * $tickets->perPage() + 1;
          @endphp

          @foreach ($tickets as $row)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td>{{ $row->email }}</td>
              <td>{{ $row->phone_number }}</td>
              <td>{{ $row->address }}</td>
              <td>
                {{ $row->birth_date && $row->birth_date ? $row->birth_date . ', ' . \Carbon\Carbon::parse($row->birth_date)->translatedFormat('d M Y') : '' }}
              </td>
              <td>{{ $row->ticket_category_name }}</td>
              <td>{{ $row->booking_code }}</td>
              <td>{{ $row->group_name }}</td>
              <td>{{ $row->counter_name }}</td>
              <td>{{ $row->counter_category_code . '-' . $row->queue_number }}</td>
              <td>{{ $row->duration ? gmdate('H:i:s', $row->duration) : '' }}</td>
              <td style="cursor: pointer" data-tooltip="tooltip" data-bs-placement="left"
                data-bs-title="Click to see the detail" data-bs-toggle="modal"
                data-bs-target="#modal-note-{{ $row->id }}">
                {{ strlen($row->note) <= 15 ? substr($row->note, 0, 15) : substr($row->note, 0, 15) . '...' }}
              </td>
              <td>{{ $row->updated_at }}</td>
            </tr>

            {{-- MODAL-NOTE --}}
            <div class="modal fade" tabindex="-1" id="modal-note-{{ $row->id }}">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header justify-content-start">Catatan untuk :
                    <b
                      class="text-tertiary ms-1">{{ $row->name ? $row->name : $row->Counter_Category->code . '-' . $row->queue_number }}</b>
                  </div>
                  <div class="modal-body">
                    <textarea class="form-control mb-2 p-2 w-100 h-100 rounded-2" cols="30" rows="10" disabled>{{ $row->note }}</textarea>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </tbody>
      </x-table>

      <x-per-page :per-page="$perPage">
        {{ $tickets->appends(['selected_counter' => $selectedCounter, 'selected_date' => $selectedDate, 'search' => $search, 'search_by' => $searchBy, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>
  </section>

  <script>
    flatpickr('#riwayatKunjungan-datepicker', {
      wrap: true,
      defaultDate: '{{ $selectedDate }}',
      dateFormat: "d M Y",
      locale: 'id',
      disableMobile: "true",
      onChange: function(selectedDates, dateStr, instance) {
        updateUrl();
      },
    })
  </script>
@endsection
