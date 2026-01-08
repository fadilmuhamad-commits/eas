@extends('layouts.admin')
@section('title', 'Loket')

@section('content')
  @php
    $policy = 'manage_counter';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">loket</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex gap-4 mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Loket</h5>

          <div class="d-flex gap-3">
            <x-search :sort="$sort" :order="$order" :search="$search" />
            <x-filter-reset />
          </div>
        </div>

        @can($policy)
          <a href="{{ route('loket-tambah') }}" class="ms-auto btn btn-primary py-0 d-flex gap-2 align-items-center"
            style="font-size: 14px; height: 31px;">
            <i class="bi bi-plus" style="font-size: 20px"></i>
            Tambah Loket
          </a>
        @endcan

        <div @cannot($policy) class="ms-auto" @endcannot>
          <x-data-count title="Total:" value="{{ $length }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :heading="[
          ['title' => 'No'],
          ['title' => 'Nama Loket', 'sort' => 'name'],
          ['title' => 'Kategori Loket'],
          ['title' => 'Group', 'sort' => 'groups.id'],
          ['title' => 'Warna Loket'],
          ['title' => 'Status', 'sort' => 'status'],
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
            $i = ($counters->currentPage() - 1) * $counters->perPage() + 1;
          @endphp

          @foreach ($counters as $row)
            <tr>
              <td>
                <input name="select-checkbox" data-row-id="{{ $row->id }}" type="checkbox"
                  class="select-checkbox form-check-input border-medium" style="width: 20px; height: 20px;">
              </td>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td>
                @foreach ($row->Categories as $index => $Ticket_Category)
                  {{ $Ticket_Category->name }}{{ $index == count($row->Categories) - 1 ? '' : ',' }}
                @endforeach
              </td>
              <td>{{ $row->Group->name ?? '' }}</td>
              <td class="text-white"
                style="background-color: {{ $row->Color->hexcode }}; text-shadow: 1px 1px 1px black;">
                {{ $row->Color->hexcode }}
              </td>
              <td style="width: 120px;">
                <div class="form-check form-switch text-primary">
                  <form id="switch-form-{{ $row->id }}" method="post">
                    @csrf
                    @method('put')

                    <input onchange="loketSwitch({{ $row->id }})" class="form-check-input mx-auto mt-0"
                      type="checkbox" name="switch_loket" role="switch" style="height: 24px; width: 40px;" value="1"
                      @if ($row->status === 1) checked @endif @cannot($policy) disabled @endcannot />
                    <input type="hidden" name="switch_loket" value="2" id="hidden-switch" />

                  </form>
                </div>
              </td>
              @can($policy)
                <td class="action-sticky">
                  <div class="d-flex gap-2">
                    <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                      data-bs-target="#modal-{{ $row->id }}">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                    <a href="{{ route('loket-edit', $row->id) }}" class="btn py-0 px-2 col rounded-1"
                      style="background-color: #3067F4; color: white;">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                  </div>
                </td>
              @endcan
            </tr>

            {{-- MODAL --}}
            @can($policy)
              <div class="modal fade" tabindex="-1" id="modal-{{ $row->id }}">
                <div class="modal-dialog modal-dialog-centered" style="width: fit-content">
                  <div class="modal-content py-3 px-5">
                    <div class="modal-body text-center px-0">
                      <p>Apakah anda yakin ingin menghapus <br>
                        <b class="text-tertiary" style="font-size: 33px">{{ $row->name }}?</b>
                      </p>
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                      <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">Tidak</button>
                      <form action="{{ route('loket.destroy', $row->id) }}" method="POST" id="form-delete"
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
        {{ $counters->appends(['search' => $search, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>
  </section>

  <div id="loket-toast" class="toast align-items-center border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    @can($policy)
      function loketSwitch(id) {
        const switchForm = document.getElementById('switch-form-' + id);
        const checkbox = switchForm.querySelector('[name="switch_loket"]');
        const hiddenSwitch = switchForm.querySelector('#hidden-switch');

        hiddenSwitch.value = checkbox.checked ? 1 : 2;

        let formData = $('#switch-form-' + id).serialize();

        $.ajax({
          type: 'PUT',
          url: `{{ route('loket.switch', 999) }}`.replace('999', id),
          data: formData,
          success: function(response) {
            $('#loket-toast .d-flex .toast-body').text(response.message);
            $('#loket-toast').addClass('text-bg-success');
            $('#loket-toast').toast('show');
          },
          error: function(error) {
            console.error(error);
          }
        });
      }
    @endcan


    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
