@extends('layouts.admin')
@section('title', 'Data Pengunjung')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">pengunjung</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex mb-4">
        <div class="d-flex flex-column gap-1">
          <h5 class="fw-semibold">Data Pengunjung</h5>

          {{-- FILTER --}}
          <div class="d-flex gap-3" style="flex-wrap: wrap">
            {{-- Type Select --}}
            <x-select-type :selected-type="$selectedType" />

            {{-- Search --}}
            <x-search :sort="$sort" :order="$order" :search="$search" :search-by="$searchBy" :options="[
                ['value' => 'name', 'label' => 'Nama'],
                ['value' => 'registration_code', 'label' => 'No. Registrasi'],
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
          ['title' => 'No. Registrasi / Anggota', 'sort' => 'registration_code'],
          ['title' => 'Tipe', 'sort' => 'type'],
          ['title' => 'Email', 'sort' => 'email'],
          ['title' => 'No. Telp', 'sort' => 'phone_number'],
          ['title' => 'Alamat', 'sort' => 'address'],
          ['title' => 'TTL', 'sort' => 'birth_date'],
          ['title' => 'Jumlah Berkunjung', 'sort' => 'tickets_total'],
          ['title' => 'Dibuat Pada', 'sort' => 'created_at'],
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
            $i = ($customers->currentPage() - 1) * $customers->perPage() + 1;
          @endphp

          @foreach ($customers as $row)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $row->name }}</td>
              <td>{{ $row->registration_code }}</td>
              <td>{{ $row->type }}</td>
              <td>{{ $row->email }}</td>
              <td>{{ $row->phone_number }}</td>
              <td>{{ $row->address }}</td>
              <td>
                {{ $row->birth_place && $row->birth_date ? $row->birth_place . ', ' . \Carbon\Carbon::parse($row->birth_date)->translatedFormat('d M Y') : '' }}
              </td>
              <td>{{ $row->tickets_total }}</td>
              <td>{{ $row->created_at }}</td>
            </tr>
          @endforeach
        </tbody>
      </x-table>

      <x-per-page :per-page="$perPage">
        {{ $customers->appends(['search' => $search, 'search_by' => $searchBy, 'sort' => $sort, 'order' => $order, 'perPage' => $perPage, 'customer_type' => $selectedType])->links() }}
      </x-per-page>
    </div>
  </section>
@endsection
