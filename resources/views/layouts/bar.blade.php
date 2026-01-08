@extends('index')
@section('title', 'Show')
@section('layout')

  <main class="d-flex flex-column align-items-center overflow-x-hidden " style="height: 100vh">
    {{-- Upper Bar --}}
    <div
      class="container-fluid bg-primary text-white d-flex py-3 px-4 justify-content-between align-items-center fixed-top"
      style="z-index: 997;">
      <div class="fs-4 fw-semibold text-white"
        style="max-width: 38vw; overflow: hidden; display: -webkit-box;
        -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;">
        {{ $config->instance_name }}
      </div>

      @if (
          (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1) ||
              (file_exists(public_path('storage/' . $config->logo2)) && $config->logo2))
        <div
          class="position-absolute rounded-bottom-4 top-0 start-50 translate-middle-x bg-white px-4 d-flex gap-4 align-items-center justify-content-center"
          style="height: 6.5rem; filter: drop-shadow(2px 2px 10px rgba(0,0,0,0.1));">
          @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
            <img src="{{ asset('storage/' . $config->logo1) }}" loading="lazy" height="64px" class="object-fit-contain"
              alt="Logo 1" loading="lazy">
          @endif
          @if (file_exists(public_path('storage/' . $config->logo2)) && $config->logo2)
            <img src="{{ asset('storage/' . $config->logo2) }}" loading="lazy" height="64px" class="object-fit-contain"
              alt="Logo 2" loading="lazy">
          @endif
        </div>
      @endif

      <div class="d-flex flex-column text-center text-white">
        <span class="fs-3 time">00:00:00</span>
        <span class="fs-6 date">01 Januari 1970</span>
      </div>

      {{-- @if (file_exists(public_path('storage/' . $logo2)) && $config->logo2)
        <img src="{{ asset('storage/' . $logo2) }}" height="56px" style="filter: drop-shadow(2px 2px 5px #1c1c1c);;"
          class="object-fit-contain" alt="Logo 2">
      @endif --}}
    </div>

    <div id="bar-container" class="col d-flex justify-content-center align-items-center w-100"
      style="background: url('/images/greyBg.png'); background-size: cover; background-attachment: fixed; padding-block: 104px 48px;">
      @yield('col')
    </div>

    {{-- Bottom Bar --}}
    <marquee class="py-2 bg-primary text-white fixed-bottom">{{ $config->running_text }}</marquee>
  </main>

  <script>
    function showTime() {
      const date = new Date();
      let timeString = date.toLocaleTimeString('id-ID');
      let dateString = date.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
      timeString = timeString.replace(/\./g, ':');

      document.querySelector('.time').innerHTML = timeString
      document.querySelector('.date').innerHTML = dateString
    }

    setInterval(showTime, 1000);
  </script>
@endsection
