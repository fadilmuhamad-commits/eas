@extends('layouts.admin')
@section('title', 'Group')

@section('content')
  @php
    $policy = 'manage_group';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">group</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex gap-4 mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Group</h5>

          <div class="d-flex gap-3">
            <x-search :sort="$sort" :order="$order" :search="$search" />
            <x-filter-reset />
          </div>
        </div>

        @can($policy)
          <button type="button" data-bs-toggle="modal" data-bs-target="#tambah-modal"
            class="ms-auto btn btn-primary py-0 d-flex gap-2 align-items-center" style="font-size: 14px; height: 31px;">
            <i class="bi bi-plus" style="font-size: 20px"></i>
            Tambah Group
          </button>
        @endcan

        <div @cannot($policy) class="ms-auto" @endcannot>
          <x-data-count title="Total:" value="{{ $length }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :heading="[['title' => 'No'], ['title' => 'Nama', 'sort' => 'name'], ['title' => 'Loket']]" :select="true" :permission="$policy">
        <tbody>
          @if ($length == 0)
            <tr>
              <td colspan="100">
                Data tidak ditemukan.
              </td>
            </tr>
          @endif

          @php
            $i = ($group->currentPage() - 1) * $group->perPage() + 1;
          @endphp

          @foreach ($group as $row)
            @php
              $counters = [];
              foreach ($row->hasManyCounters as $counter) {
                  array_push($counters, $counter->name);
              }
            @endphp

            <tr>
              <td>
                <input name="select-checkbox" data-row-id="{{ $row->id }}" type="checkbox"
                  class="select-checkbox form-check-input border-medium" style="width: 20px; height: 20px;">
              </td>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td class="overflow-hidden" style="max-width: 16rem; text-overflow: ellipsis;">
                @foreach ($counters as $index => $counter)
                  {{ $counter }}{{ $index == count($counters) - 1 ? '' : ',' }}
                @endforeach
              </td>
              @can($policy)
                <td class="action-sticky">
                  <div class="d-flex gap-2">
                    <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                      data-bs-target="#modal-{{ $row->id }}">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                    <a class="btn py-0 px-2 col" style="background-color: #3067F4; color: white;" data-bs-toggle="modal"
                      data-bs-target="#edit-group-modal" onclick="editGroup({{ $row->id }}, '{{ $row->name }}')">
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
                    <div class="modal-body px-0 text-center">
                      <p>Apakah anda yakin ingin menghapus<br>
                        <b class="text-tertiary" style="font-size: 33px">{{ $row->name }}?</b>
                      </p>
                    </div>
                    <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                      <button type="button" class="btn btn-outline-secondary fw-bold"
                        data-bs-dismiss="modal">Tidak</button>
                      <form action="{{ route('group.destroy', $row->id) }}" method="POST" id="form-delete"
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
        {{ $group->appends(['search' => $search, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>

    {{-- TAMBAH GROUP MODAL --}}
    @can($policy)
      <div class="modal fade" tabindex="-1" id="tambah-modal">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-2">
            <div class="modal-body">
              <h3 class="fs-3 text-center  fw-bold mb-3">Tambah Group</h3>
              <form action="{{ route('group.store') }}" method="POST" id="form-tambah" class="d-flex flex-column">
                @csrf
                <input type="text" class="form-control py-2" style="font-size: 14px" id="group" name="group"
                  placeholder="Masukkan nama grup">
                <x-form-alert field="group" />
                <div class="mt-3 ms-auto">
                  <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary fw-semibold" id="btn-tambah">Tambah</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      {{-- EDIT MODAL --}}
      <div class="modal fade" tabindex="-1" id="edit-group-modal">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-2">
            <div class="modal-body">
              <h3 class="fs-3 text-center  fw-bold mb-3">Edit Group</h3>
              <form method="POST" id="form-edit-group" class="d-flex flex-column">
                @csrf
                @method('put')

                <input type="text" class="form-control py-2" style="font-size: 14px" id="group" name="group"
                  placeholder="Masukkan nama grup">
                <x-form-alert field="group" />
                <div class="mt-3 ms-auto">
                  <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary fw-semibold" id="btn-tambah">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endcan
  </section>

  <script>
    function editGroup(id, name) {
      $('#edit-group-modal .modal-dialog .modal-content .modal-body #form-edit-group #group').val(name);
      $('#form-edit-group').attr('action', "{{ route('group.update', 9999) }}".replace(9999, id));
    }

    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
