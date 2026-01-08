@extends('layouts.admin')
@section('title', 'Roles')

@section('content')
  @php
    $policy = 'manage_role';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">roles</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex gap-4 mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Roles</h5>

          <div class="d-flex gap-3">
            <x-search :sort="$sort" :order="$order" :search="$search" />
            <x-filter-reset />
          </div>
        </div>

        @can($policy)
          <a href="{{ route('roles.add') }}" class="ms-auto btn btn-primary py-0 d-flex gap-2 align-items-center"
            style="font-size: 14px; height: 31px;">
            <i class="bi bi-plus" style="font-size: 20px"></i>
            Tambah Role
          </a>
        @endcan

        <div @cannot($policy) class="ms-auto" @endcannot>
          <x-data-count title="Total:" value="{{ $length - 2 }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :heading="[['title' => 'No'], ['title' => 'Name', 'sort' => 'name'], ['title' => 'Permissions']]" :select="true" :permission="$policy">
        <tbody>
          @if ($length - 2 == 0)
            <tr>
              <td colspan="100">
                Data tidak ditemukan.
              </td>
            </tr>
          @endif

          @php
            $i = ($roles->currentPage() - 1) * $roles->perPage() + 1;
          @endphp

          @foreach ($roles as $row)
            @if ($row->id !== 1 && $row->id !== 2)
              @php
                $permissions = [];
                foreach ($row->Permissions as $permission) {
                    array_push($permissions, $permission->name);
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
                  @if (count($permissions) == $permissionsTotal)
                    <span class="fw-bold">All Permissions</span>
                  @else
                    @foreach ($permissions as $index => $permission)
                      {{ $permission }}{{ $index == count($permissions) - 1 ? '' : ',' }}
                    @endforeach
                  @endif
                </td>

                @can($policy)
                  <td class="action-sticky">
                    <div class="d-flex gap-2">
                      <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                        data-bs-target="#modal-{{ $row->id }}">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                      <a href="{{ route('roles.edit', $row->id) }}" class="btn py-0 px-2 col"
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
                      <div class="modal-body px-0 text-center">
                        <p>Apakah anda yakin ingin menghapus<br>
                          <b class="text-tertiary" style="font-size: 33px">{{ $row->name }}?</b>
                        </p>
                      </div>
                      <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold"
                          data-bs-dismiss="modal">Tidak</button>
                        <form action="{{ route('roles.destroy', $row->id) }}" method="POST" id="form-delete"
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
            @endif
          @endforeach
        </tbody>
      </x-table>

      <x-per-page :per-page="$perPage">
        {{ $roles->appends(['search' => $search, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>
  </section>

  <script>
    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
