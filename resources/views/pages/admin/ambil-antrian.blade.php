@extends('layouts.admin')
@section('title', 'Ambil Antrian')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">ambil-antrian</li>
    </x-breadcrumb>

    <div class="card col overflow-hidden p-4">
      <div class="d-flex">
        <h5 class="fw-semibold">Ambil Antrian</h5>
      </div>

      <div class="m-auto">
        @if (count($categories) === 0)
          Data tidak ditemukan.
        @endif
        <x-ambil-antrian-cards :client="false" :data="$categories" />
      </div>
    </div>
  </section>
@endsection
