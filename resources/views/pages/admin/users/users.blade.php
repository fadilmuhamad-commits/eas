@extends('layouts.admin')
@section('title', 'Users')

@section('content')
  @php
    $policy = 'manage_user';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">users</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex gap-4 mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Users</h5>

          <div class="d-flex gap-3">
            <x-search :sort="$sort" :order="$order" :search="$search" :search-by="$searchBy" :options="[
                ['value' => 'name', 'label' => 'Username'],
                ['value' => 'email', 'label' => 'Email'],
                ['value' => 'role_name', 'label' => 'Role'],
                ['value' => 'loket_name', 'label' => 'Loket'],
            ]" />
            <x-filter-reset />
          </div>
        </div>

        @can($policy)
          <a href="{{ route('tambah-user') }}" class="ms-auto btn btn-primary py-0 d-flex gap-2 align-items-center"
            style="font-size: 14px; height: 31px;">
            <i class="bi bi-plus" style="font-size: 20px"></i>
            Tambah User
          </a>
        @endcan

        <div @cannot($policy) class="ms-auto" @endcannot>
          <x-data-count title="Total:" value="{{ $length }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :heading="[
          ['title' => 'No'],
          ['title' => 'Name', 'sort' => 'name'],
          ['title' => 'Username', 'sort' => 'username'],
          ['title' => 'Email', 'sort' => 'email'],
          ['title' => 'Role', 'sort' => 'role_id'],
      ]" :select="true" :permission="$policy">
        <tbody>
          @if ($length - 1 == 0)
            <tr>
              <td colspan="100">
                Data tidak ditemukan.
              </td>
            </tr>
          @endif

          @php
            $i = ($users->currentPage() - 1) * $users->perPage() + 1;
          @endphp

          @foreach ($users as $row)
            @if ($row->id != 1)
              <tr>
                <td>
                  @if (Auth::user()->id != $row->id)
                    <input name="select-checkbox" data-row-id="{{ $row->id }}" type="checkbox"
                      class="select-checkbox form-check-input border-medium" style="width: 20px; height: 20px;">
                  @endif
                </td>
                <td>{{ $i++ }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->username }}</td>
                <td>{{ $row->email }}</td>
                <td>{{ $row->role_name }} {{ $row->Counter->name ?? '' }}</td>
                @can($policy)
                  <td class="action-sticky">
                    <div class="d-flex gap-2">
                      <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                        data-bs-target="#modal-{{ $row->id }}" {{ Auth::user()->id == $row->id ? 'disabled' : '' }}>
                        <i class="bi bi-trash-fill"></i>
                      </button>
                      <a href="{{ route('user.edit', $row->id) }}" class="btn py-0 px-2 col border-0"
                        style="background-color: #3067F4; color: white;">
                        <i class="bi bi-pencil-fill"></i>
                      </a>
                    </div>
                  </td>
                @endcan
              </tr>
            @endif

            {{-- MODAL --}}
            @can($policy)
              @if (Auth::user()->id != $row->id)
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
                        <form
                          action="{{ route('user.destroy', Auth::user()->id == $row->id || $row->id == 1 ? '' : $row->id) }}"
                          method="POST" id="form-delete" onsubmit="deleteSubmit({{ $row->id }})">
                          @csrf
                          @method('delete')
                          <button type="submit" class="btn btn-danger px-4 fw-bold"
                            id="btn-delete-{{ $row->id }}">Ya</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            @endcan
          @endforeach
        </tbody>
      </x-table>

      <x-per-page :per-page="$perPage">
        {{ $users->appends(['search' => $search, 'search_by' => $searchBy, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>
  </section>

  <script>
    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
