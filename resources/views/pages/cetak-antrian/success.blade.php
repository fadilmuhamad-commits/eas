@extends('layouts.bar')
@section('title', 'Success')
@section('col')

  <script>
    function redirectCetak() {
      window.location.href = "{{ route('cetak') }}";
    }

    setTimeout(redirectCetak, 7000);
  </script>

  <div>
    <div class="card" id="modal" style="width: 500px; height: 200px; background-color: {{ $counterCategory->color }} ">
      <div class="card-body d-flex align-items-center justify-content-center">
        <h5 class="card-title fw-bold text-white" style="font-size: 5em">
          {{ $counterCategory->code . '-' . $ticket->queue_number }}
        </h5>
      </div>
    </div>
    <p class="text-center mt-4">Kartu antrian Anda sudah dapat diambil</p>
  </div>
@endsection
