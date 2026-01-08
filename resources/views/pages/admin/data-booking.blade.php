@extends('layouts.admin')
@section('title', 'Data Booking')

@section('content')
  @php
    $policy = 'manage_booking';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">booking</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Data Booking</h5>

          {{-- FILTER --}}
          <div class="d-flex gap-3" style="flex-wrap: wrap">
            <x-select-category :categories="$categories" :selected-category="$selectedCounterCategory" />

            <x-select-type :selected-type="$selectedType" />

            <div id="antrian-datepicker" class="datepicker btn-group d-flex align-items-center position-relative"
              style="height: 31px;">
              <input name="date-input" type="text" class="btn btn-outline-medium text-start pe-0"
                style="height: inherit; width: 7.25rem; font-size: 14px;" placeholder="dd mmm yyyy" data-input>

              @if ($selectedDate)
                <a class="fs-5 btn btn-danger d-flex align-items-center justify-content-center"
                  style="height: inherit; aspect-ratio: 1 / 1;" title="clear" data-clear>
                  <i class="bi bi-x"></i>
                </a>
              @endif
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
          <x-data-count title="Tiket Booking:" value="{{ $length }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :heading="[
          ['title' => 'No'],
          ['title' => 'Nama', 'sort' => 'name'],
          ['title' => 'No. Registrasi / Anggota', 'sort' => 'registration_code'],
          ['title' => 'Tipe Pengunjung', 'sort' => 'type'],
          ['title' => 'No. Antrian', 'sort' => 'queue_number'],
          ['title' => 'No. Booking', 'sort' => 'booking_code'],
          ['title' => 'Email', 'sort' => 'email'],
          ['title' => 'No. Telp', 'sort' => 'phone_number'],
          ['title' => 'Alamat', 'sort' => 'address'],
          ['title' => 'TTL', 'sort' => 'birth_date'],
          ['title' => 'Dibuat Pada', 'sort' => 'created_at'],
      ]" :select="true" :permission="$policy">
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
            <tr class="{{ $row->status == 3 ? 'fw-bold' : '' }}">
              <td>
                <input name="select-checkbox" data-row-id="{{ $row->id }}" type="checkbox"
                  class="select-checkbox form-check-input border-medium" style="width: 20px; height: 20px;">
              </td>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td>{{ $row->registration_code }}</td>
              <td>{{ $row->type }}</td>
              <td>{{ ($row->Counter_Category->code ?? '') . '-' . $row->queue_number }}</td>
              <td>{{ $row->booking_code }}</td>
              <td>{{ $row->email }}</td>
              <td>{{ $row->phone_number }}</td>
              <td>{{ $row->address }}</td>
              <td>
                {{ $row->birth_place && $row->birth_date ? $row->birth_place . ', ' . \Carbon\Carbon::parse($row->birth_date)->translatedFormat('d M Y') : '' }}
              </td>
              <td>{{ $row->created_at }}</td>
              @can($policy)
                <td class="action-sticky {{ $row->status == 3 ? 'bg-primary bg-opacity-25' : '' }}">
                  <div class="d-flex gap-2">
                    <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                      data-bs-target="#modal-delete-{{ $row->id }}">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </div>
                </td>
              @endcan
            </tr>

            {{-- MODAL-DELETE --}}
            @can($policy)
              <div class="modal fade" tabindex="-1" id="modal-delete-{{ $row->id }}">
                <div class="modal-dialog modal-dialog-centered" style="width: fit-content">
                  <div class="modal-content py-3 px-5">
                    <div class="modal-body px-0 text-center">
                      <p>Apakah anda yakin ingin menghapus<br>
                        <b class="text-tertiary"
                          style="font-size: 33px">{{ $row->name ? $row->name : $row->ticket_category_id . '-' . $row->queue_number }}?</b>
                      </p>
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                      <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">Tidak</button>
                      <form action="{{ route('tiket.destroy', $row->id) }}" method="POST" id="form-delete"
                        onsubmit="deleteSubmit({{ $row->id }})">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger px-4 fw-bold"
                          id="btn-delete-{{ $row->id }}">Ya</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            @endcan
          @endforeach
        </tbody>
      </x-table>

      <x-per-page :per-page="$perPage">
        {{ $tickets->appends(['selected_date' => $selectedDate, 'selected_category' => $selectedCounterCategory, 'search' => $search, 'search_by' => $searchBy, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage, 'customer_type' => $selectedType])->links() }}
      </x-per-page>
    </div>
  </section>

  <script>
    flatpickr('#antrian-datepicker', {
      wrap: true,
      defaultDate: '{{ $selectedDate }}',
      dateFormat: "d M Y",
      locale: 'id',
      disableMobile: "true",
      onChange: function(selectedDates, dateStr, instance) {
        updateUrl();
      },
    })

    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
