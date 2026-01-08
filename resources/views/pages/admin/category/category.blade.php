@extends('layouts.admin')
@section('title', 'Category')

@section('content')
  @php
    $policy = 'manage_category';
  @endphp

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">kategori</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex gap-4 mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Kategori</h5>

          <div class="d-flex gap-3">
            <nav class="nav nav-pills flex-nowrap border rounded-4 overflow-hidden" style="font-size: 14px">
              <a style="height: 31px"
                class="nav-link d-flex align-items-center rounded-0 {{ $type == 'loket' ? 'active' : '' }}"
                href="{{ route(\Request::route()->getName(), array_merge(request()->query(), ['type' => 'loket'])) }}">Loket</a>
              <a style="height: 31px"
                class="nav-link d-flex align-items-center rounded-0 {{ $type == 'tiket' ? 'active' : '' }}"
                href="{{ route(\Request::route()->getName(), array_merge(request()->query(), ['type' => 'tiket'])) }}">Tiket</a>
            </nav>
            <x-search :sort="$sort" :order="$order" :search="$search" :search-by="$searchBy" :type="$type"
              :options="[['value' => 'name', 'label' => 'Nama'], ['value' => 'code', 'label' => 'Kode']]" />
            <x-filter-reset />
          </div>
        </div>

        @can($policy)
          <a href="{{ route('category-tambah', ['type' => $type]) }}"
            class="ms-auto btn btn-primary py-0 d-flex gap-2 align-items-center" style="font-size: 14px; height: 31px;">
            <i class="bi bi-plus" style="font-size: 20px"></i>
            Tambah Kategori
          </a>
        @endcan

        <div @cannot($policy) class="ms-auto" @endcannot>
          <x-data-count title="Total:" value="{{ $length }}" />
        </div>
      </div>

      <x-table :sort="$sort" :order="$order" :type="$type" :heading="$type == 'loket'
          ? [
              ['title' => 'No'],
              ['title' => 'Nama', 'sort' => 'name'],
              ['title' => 'Kode', 'sort' => 'code'],
              ['title' => 'Warna'],
          ]
          : [['title' => 'No'], ['title' => 'Nama', 'sort' => 'name'], ['title' => 'Kode', 'sort' => 'code']]" :select="true"
        :permission="$policy">
        <tbody>
          @if ($length == 0)
            <tr>
              <td colspan="100">
                Data tidak ditemukan.
              </td>
            </tr>
          @endif

          @php
            $i = ($category->currentPage() - 1) * $category->perPage() + 1;
          @endphp

          @foreach ($category as $row)
            <tr>
              <td>
                @if (($type == 'tiket' && ($row->id != 1 && $row->id != 2 && $row->id != 3)) || $type == 'loket')
                  <input name="select-checkbox" data-row-id="{{ $row->id }}" type="checkbox"
                    class="select-checkbox form-check-input border-medium" style="width: 20px; height: 20px;">
                @endif
              </td>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td>{{ $row->code }}</td>
              @if ($type == 'loket')
                <td class="text-white"
                  style="background-color: {{ $row->Color->hexcode }}; text-shadow: 1px 1px 1px black;">
                  {{ $row->Color->hexcode }}
                </td>
              @endif

              @can($policy)
                <td class="action-sticky">
                  <div class="d-flex gap-2">
                    <button class="btn btn-danger py-0 px-2 col rounded-1" data-bs-toggle="modal"
                      data-bs-target="#modal-{{ $row->id }}"
                      {{ ($type == 'tiket' && ($row->id != 1 && $row->id != 2 && $row->id != 3)) || $type == 'loket' ? '' : 'disabled' }}>
                      <i class="bi bi-trash-fill"></i>
                    </button>
                    <a href="{{ route('category-edit', ['counter_category' => $row->id, 'type' => $type]) }}"
                      class="btn py-0 px-2 col" style="background-color: #3067F4; color: white;">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                  </div>
                </td>
              @endcan
            </tr>

            {{-- MODAL --}}
            @can($policy)
              @if (($type == 'tiket' && ($row->id != 1 && $row->id != 2 && $row->id != 3)) || $type == 'loket')
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
                          action="{{ $type == 'loket' ? route('categoryL.destroy', $row->id) : route('categoryT.destroy', $row->id) }}"
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
        {{ $category->appends(['type' => $type, 'search' => $search, 'search_by' => $searchBy, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage])->links() }}
      </x-per-page>
    </div>
  </section>

  <script>
    function deleteSubmit(id) {
      document.getElementById(`btn-delete-${id}`).disabled = true;
    }
  </script>
@endsection
