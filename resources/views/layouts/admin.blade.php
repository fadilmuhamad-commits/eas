@extends('index')

@section('layout')
  <style>
    .main-collapse {
      width: 95.8% !important;
      margin-left: 56px;
    }

    #content-container {
      margin-top: 48px;
    }

    .offcanvas {
      padding-top: calc(60px + 3vh);
    }

    .w-40 {
      width: 40%;
    }

    @media (max-width: 560px) {
      #admin-title {
        max-width: 50vw;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .navbar-brand {
        width: 100% !important;
      }

      #content-container {
        margin-top: 96px;
      }

      .offcanvas {
        padding-top: calc(112px + 3vh);
      }
    }
  </style>

  <header class="navbar fixed-top py-0 d-flex bg-body" style="z-index: 51; height: 52px;">
    <a class="navbar-brand bg-secondary d-flex align-items-center py-0 col-2 me-0 position-relative"
      href="{{ route('dashboard') }}" style="gap: 2px; height: inherit; min-width: 112px;">
      @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
        {{-- <img src="{{ asset('storage/' . $config->logo1) }}" alt="Logo {{ $config->instance_name }}"
          class="object-fit-contain position-relative z-1"
          style="height: 40px; filter: drop-shadow(2px 2px 5px #1c1c1c);" /> --}}
        {{-- <div class="col"></div> --}}
        <div id="navbar-logo" class="bg-white h-100 d-flex align-items-center justify-content-center w-40">
          <img src="{{ asset('storage/' . $config->logo1) }}" alt="Logo {{ $config->instance_name }}"
            class="object-fit-contain" style="height: 40px;" />
        </div>
        <span id="navbar-header" class="fs-6 fw-bold text-white mx-auto">ANTRIAN</span>
      @else
        <span id="navbar-header" class="fs-5 fw-bold text-white mx-auto">ANTRIAN</span>
      @endif
    </a>

    <div class="col px-3 d-flex gap-3 align-items-center justify-content-end h-100 py-2"
      style="box-shadow: 0px 1px 5px rgba(0,0,0,0.2);">
      <a class="btn btn-sm px-2" id="sidebarToggle" data-bs-toggle="offcanvas" href="#sidebar" role="button"
        aria-controls="sidebar">
        <i class="bi bi-list fs-4 "></i>
      </a>
      <span id="admin-title" class="fw-semibold me-auto" style="font-size: 18px">
        Admin Panel
        {{ strlen($config->instance_name) <= 56 ? substr($config->instance_name, 0, 56) : substr($config->instance_name, 0, 56) . '...' }}
      </span>

      @if (auth()->user()->Counter)
        <div class="dropdown">
          <button type="button" id="colorSwitch" class="btn btn-sm py-0 px-1" type="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="bi bi-palette" style="font-size: 24px"></i>
          </button>

          <div class="dropdown-menu dropdown-menu-end">
            <div class="px-3 text-medium user-select-none" style="font-size: 14px">
              Color theme
            </div>
            <hr class="my-2">
            @php
              $colorTheme = isset($_COOKIE['colorTheme']) ? $_COOKIE['colorTheme'] : 'default';
            @endphp

            <div><button class="dropdown-item {{ $colorTheme == 'default' ? 'active' : '' }}"
                onclick="setColor('colorTheme', 'default', 365)">Default</button></div>
            <div><button class="dropdown-item {{ $colorTheme == 'custom' ? 'active text-white' : '' }}"
                onclick="setColor('colorTheme', 'custom', 365)">Loket</button></div>
          </div>
        </div>
      @endif

      <button type="button" id="darkSwitch" class="btn btn-sm py-0 px-1">
        <i class="bi bi-moon" id="darkIcon" style="font-size: 24px"></i>
      </button>

      <div class="dropdown">
        <button type="button" class="btn btn-sm py-0 px-1 d-flex align-items-center gap-2" data-bs-toggle="dropdown"
          data-bs-auto-close="outside" aria-expanded="false">
          {{ strlen(auth()->user()->name) <= 24 ? substr(auth()->user()->name, 0, 24) : substr(auth()->user()->name, 0, 24) . '...' }}
          <i class="bi bi-person-circle" style="font-size: 28px"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-end overflow-hidden pb-0" style="width: 320px;">
          <div class="px-3">
            <div class="text-medium" style="font-size: 12px;">User:</div>
            <div class="fs-5 fw-semibold" style="word-break: break-all;">{{ auth()->user()->name }}</div>

            <div class="text-medium mt-2 " style="font-size: 12px;">Role:</div>
            <div class="fs-5 fw-semibold" style="word-break: break-all;">
              {!! auth()->user()->role->name ?? '<span class="text-medium fw-normal">none</span>' !!}
              {{ auth()->user()->Counter ? auth()->user()->Counter->name : '' }}
            </div>
          </div>

          <hr class="mb-0">

          <div class="d-flex">
            <a class="col btn btn-primary border-0 rounded-0" href="{{ route('account.edit') }}">Account</a>
            <a class="col-4 btn btn-danger border-0 rounded-0" href="{{ route('logout') }}">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div id="content-container" class="d-flex">
    <div class="col-lg-2 offcanvas-container">
      <div class="offcanvas offcanvas-start show bg-primary border-0 overflow-y-auto" style="width: inherit; z-index: 50;"
        data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="sidebar">

        <nav class="nav flex-column gap-2 fw-semibold px-1 pb-3" style="font-size: 14px;">
          <div class="fw-medium px-3 user-select-none nav-title"
            data-route="dashboard,antrian,antrian-edit,pengunjung,riwayat-kunjungan,ambil-antrian"
            style="font-size: 14px;">
            GENERAL
          </div>
          <hr class="nav-line mx-3 mt-0 mb-1 text-white">

          <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
            href="{{ route('dashboard') }}" data-route="dashboard" data-bs-title="Dashboard">
            <i class="bi bi-house-door" style="font-size: 16px;"></i>
            <div class="nav-text">Dashboard</div>
          </a>

          @can('get_ticket')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('ambil-antrian') }}" data-route="ambil-antrian" data-bs-title="Ambil Antrian">
              <i class="bi bi-list-check" style="font-size: 16px;"></i>
              <div class="nav-text">Ambil Antrian</div>
            </a>
          @endcan

          @can('view_queue')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('antrian') }}" data-route="antrian,antrian-edit" data-bs-title="Data Antrian">
              <i class="bi bi-person-lines-fill" style="font-size: 16px;"></i>
              <div class="nav-text">Data Antrian</div>
            </a>
          @endcan

          @can('view_booking')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('data-booking') }}" data-route="data-booking" data-bs-title="Data Booking">
              <i class="bi bi-ticket-detailed" style="font-size: 16px;"></i>
              <div class="nav-text">Data Booking</div>
            </a>
          @endcan

          @can('view_customer')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('pengunjung') }}" data-route="pengunjung" data-bs-title="Data Pengunjung">
              <i class="bi bi-person" style="font-size: 16px;"></i>
              <div class="nav-text">Data Pengunjung</div>
            </a>
          @endcan

          @can('view_history')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('riwayat-kunjungan') }}" data-route="riwayat-kunjungan" data-bs-title="Riwayat Kunjungan">
              <i class="bi bi-clock-history" style="font-size: 16px;"></i>
              <div class="nav-text">Riwayat Kunjungan</div>
            </a>
          @endcan

          @if (auth()->user()->Role)
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center" href="#screen-modal"
              data-route="screen" data-bs-title="Screen" data-bs-toggle="modal">
              <i class="bi bi-window-fullscreen"></i>
              <div class="nav-text">Screen</div>
            </a>
          @endif

          @if (auth()->user()->Counter)
            @can('call_queue')
              <div class="fw-medium px-3 user-select-none nav-title mt-4" data-route="panggil-antrian"
                style="font-size: 14px;">CUSTOMER SERVICE</div>

              <hr class="nav-line mx-3 mt-0 mb-1 text-white">

              <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
                href="{{ route('panggil-antrian') }}" data-route="panggil-antrian" data-bs-title="Panggil Antrian">
                <i class="bi bi-volume-up" style="font-size: 16px;"></i>
                <div class="nav-text">Panggil Antrian</div>
              </a>
            @endcan
          @endif

          @canany('view_group|view_counter|view_category|view_user|view_role|manage_config')
            <div class="fw-medium px-3 user-select-none nav-title mt-4"
              data-route="group,loket,loket-edit,loket-tambah,category,category-tambah,category-edit,users,tambah-user,user.edit,roles,roles.add,roles.edit,config"
              style="font-size: 14px;">ADMIN
            </div>
            <hr class="nav-line mx-3 mt-0 mb-1 text-white">
          @endcanany

          @can('view_group')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('group') }}" data-route="group" data-bs-title="Group">
              <i class="bi bi-collection" style="font-size: 16px;"></i>
              <div class="nav-text">Group</div>
            </a>
          @endcan

          @can('view_counter')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('loket') }}" data-route="loket,loket-edit,loket-tambah" data-bs-title="Loket">
              <i class="bi bi-card-list" style="font-size: 16px;"></i>
              <div class="nav-text">Loket</div>
            </a>
          @endcan

          @can('view_category')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('category') }}" data-route="category,category-tambah,category-edit"
              data-bs-title="Kategori">
              <i class="bi bi-tags" style="font-size: 16px;"></i>
              <div class="nav-text">Kategori</div>
            </a>
          @endcan

          @can('view_user')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('users') }}" data-route="users,tambah-user,user.edit" data-bs-title="Users">
              <i class="bi bi-people" style="font-size: 16px;"></i>
              <div class="nav-text">Users</div>
            </a>
          @endcan

          @can('view_role')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('roles') }}" data-route="roles,roles.add,roles.edit" data-bs-title="Roles">
              <i class="bi bi-person-gear" style="font-size: 16px;"></i>
              <div class="nav-text">Roles</div>
            </a>
          @endcan

          @can('manage_config')
            <a style="height: 32px;" class="nav-link px-2 mx-2 d-flex gap-2 align-items-center"
              href="{{ route('config') }}" data-route="config" data-bs-title="Configuration">
              <i class="bi bi-sliders2"></i>
              <div class="nav-text">Configuration</div>
            </a>
          @endcan
        </nav>
      </div>
    </div>

    <main id="main" class="px-4 col-lg-10 col"
      style="min-height: calc(100vh - 48px); max-width: 100%; padding-top: 3.5vh; padding-bottom: 2vh;">
      @yield('content')
    </main>
  </div>

  {{-- SCREEN MODAL --}}
  <div class="modal fade" tabindex="-1" id="screen-modal">
    <div class="modal-dialog modal-dialog-centered" style="width: fit-content">
      <div class="modal-content py-4 px-5">
        <div class="modal-body d-flex gap-4 p-0">
          <a href="{{ route('cetak') }}"
            class="btn btn-outline-secondary fs-5 gap-2 d-flex flex-column align-items-center justify-content-center"
            style="height: 9rem; aspect-ratio: 4/3;">
            <i class="bi bi-printer fs-2"></i>
            CETAK
          </a>
          <div style="height: 9rem; aspect-ratio: 4/3;">
            <div class="d-flex gap-2">
              <i class="bi bi-tv"></i>
              <h6>TV</h6>
            </div>
            <div id="tv-list" class="d-flex flex-column overflow-auto border rounded-1 p-0" style="height: 7.4rem">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="main-spinner"
    class="d-flex align-items-center justify-content-center position-absolute top-0 start-0 bg-black bg-opacity-50 overflow-hidden"
    style="height: 100vh; width: 100vw; z-index: 9999999;">
    <div class="spinner-border text-secondary" style="width: 3rem; height: 3rem" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <script>
    function showMainSpinner() {
      $('#main-spinner').removeClass('d-none');
      $('#main-spinner').addClass('d-flex');
    }

    function hideMainSpinner() {
      $('#main-spinner').addClass('d-none');
      $('#main-spinner').removeClass('d-flex');
    }
    hideMainSpinner();

    const tvList = document.getElementById('tv-list');

    function groupGet() {
      $.ajax({
        url: '{{ route('ajax.group') }}',
        type: 'GET',
        success: function(res) {
          tvList.innerHTML = generateGroups(res.group);
        },
        error: function(xhr, status, error) {
          console.error(error);
        }
      })
    }
    groupGet();

    function generateGroups(data) {
      const baseUrl = @json(url('/'));
      let html = '';

      data.forEach(group => {
        const showRoute = `{{ route('show', 9999) }}`.replace(9999, group.id);

        html += `
          <a href="${showRoute}" class="btn btn-outline-secondary text-start border-0 rounded-0 px-2 py-1"
            style="font-size: 14px;">${group.name}</a>
        `;
      });

      return html;
    }

    if ($(window).width() >= 992) {
      $('#sidebarToggle').removeAttr('data-bs-toggle', 'offcanvas');
      $('#sidebarToggle').removeAttr('href', '#sidebar');
      $('#sidebarToggle').removeAttr('aria-controls', 'sidebar');
    }

    const sidebarToggle = document.querySelector('#sidebarToggle');
    const offcanvasContainer = document.querySelector('.offcanvas-container');
    const main = document.querySelector('#main');
    const sidebar = document.querySelector('#sidebar');
    const navText = document.querySelectorAll('.nav-text');
    const navTitle = document.querySelectorAll('.nav-title');
    const navLink = document.querySelectorAll('.nav-link');
    const navLine = document.querySelector('.nav-line');
    const navbarBrand = document.querySelector('.navbar-brand');
    const navbarHeader = document.querySelector('#navbar-header');
    const navbarLogo = document.querySelector('#navbar-logo');

    function handleViewportChange(mediaQuery) {
      if (mediaQuery.matches) {
        sidebar.classList.add('show');

        let isCollapsed = localStorage.getItem('collapse') == 'true';

        function toggleCollapse() {
          for (let i = 0; i < navText.length; i++) {
            navText[i].classList.toggle('d-none');
          }
          for (let i = 0; i < navTitle.length; i++) {
            navTitle[i].classList.toggle('d-none');
          }

          // sidebar.classList.toggle('sidebar-collapse');
          navLine.classList.toggle('d-none');
          navbarBrand.classList.toggle('col-2');
          offcanvasContainer.classList.toggle('col-lg-2');

          @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
            navbarHeader.classList.toggle('d-none');
            navbarLogo.classList.toggle('w-40');
            navbarLogo.classList.toggle('w-75');
            navbarLogo.classList.toggle('mx-auto');
          @endif

          main.classList.toggle('w-100');
          main.classList.toggle('col-lg-10');
          main.classList.toggle('col');
          main.classList.toggle('main-collapse');

          if (isCollapsed) {
            $('.nav-link').attr('data-tooltip', 'tooltip');
            $('.nav-link').attr('data-bs-placement', 'right');
          } else {
            $('.nav-link').removeAttr('data-tooltip', 'tooltip');
            $('.nav-link').removeAttr('data-bs-placement', 'right');
          }
          localStorage.setItem('collapse', isCollapsed);
        }

        if (isCollapsed) {
          toggleCollapse();
        }

        sidebarToggle.addEventListener('click', () => {
          isCollapsed = !isCollapsed;
          toggleCollapse();
        });
      } else {
        sidebar.classList.remove('show');
        navbarHeader.classList.add('d-none');
      }
    }
    const breakpoint = window.matchMedia('(min-width: 992px)');
    handleViewportChange(breakpoint);
    breakpoint.addListener(handleViewportChange);

    // dark mode switch
    const darkIcon = document.querySelector('#darkIcon');
    const darkSwitch = document.getElementById('darkSwitch');

    function toggleTheme() {
      if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
        document.documentElement.setAttribute('data-bs-theme', 'light');
        darkIcon.classList.toggle('bi-sun');
      } else {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        darkIcon.classList.toggle('bi-sun');
      }

      const currentTheme = document.documentElement.getAttribute('data-bs-theme');
      localStorage.setItem('theme', currentTheme);

      updateIcon(currentTheme);
    }

    function updateIcon(theme) {
      darkIcon.classList.toggle('bi-sun', theme === 'dark');
    }

    darkSwitch.addEventListener('click', toggleTheme);
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
      updateIcon(savedTheme);
    }

    function isColorBright(variable, bright) {
      const rgbString = getComputedStyle(document.documentElement).getPropertyValue(variable).trim();

      const rgbValues = rgbString.match(/\d+/g).map(Number);

      let r = rgbValues[0];
      let g = rgbValues[1];
      let b = rgbValues[2];

      const brightness = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

      return brightness > bright;
    }

    const currentRoute = "{{ Route::currentRouteName() }}";

    // SET TEXT COLOR FOR NAV LINKS
    for (let i = 0; i < navLink.length; i++) {
      const routeAttribute = navLink[i].getAttribute('data-route');
      if (routeAttribute) {
        const routes = routeAttribute.split(',');
        let isRouteMatched = false;
        for (let j = 0; j < routes.length; j++) {
          const route = routes[j].trim();
          if (currentRoute === route) {
            isRouteMatched = true;
            break;
          }
        }

        if (isColorBright('--bs-primary-rgb', 0.5)) {
          navLink[i].classList.add(isRouteMatched ? 'bg-secondary' : 'text-white');

          if (isColorBright('--bs-primary-rgb', 0.8)) {
            navLink[i].style.textShadow = isRouteMatched ? '' : '1px 1px 2px rgba(0,0,0,1)';
          }
        } else {
          navLink[i].classList.add(isRouteMatched ? 'bg-secondary' : 'text-medium');
        }
        navLink[i].classList.add(isRouteMatched ? 'text-white' : 'a');
        navLink[i].classList.add(isRouteMatched ? 'rounded' : 'a');
        navLink[i].classList.add(isRouteMatched ? 'shadow-lg' : 'a');
      }
    }

    // SET TEXT COLOR FOR NAV TITLES
    for (let i = 0; i < navTitle.length; i++) {
      const routeAttribute = navTitle[i].getAttribute('data-route');
      if (routeAttribute) {
        const routes = routeAttribute.split(',');
        let isRouteMatched = false;
        for (let j = 0; j < routes.length; j++) {
          const route = routes[j].trim();
          if (currentRoute === route) {
            isRouteMatched = true;
            break;
          }
        }

        if (isColorBright('--bs-primary-rgb', 0.5)) {
          navTitle[i].classList.add('text-white');
          navTitle[i].style.opacity = isRouteMatched ? 1 : 0.5;

          if (isColorBright('--bs-primary-rgb', 0.8)) {
            navTitle[i].classList.add(isRouteMatched ? 'text-white' : 'text-medium');
          }
        } else {
          navTitle[i].classList.add(isRouteMatched ? 'text-white' : 'text-medium');
        }
      }
    }


    // ADJUST UI COLOR
    // function adjustUIBrightness(variable, percent) {
    //   const rgbString = getComputedStyle(document.documentElement).getPropertyValue(variable).trim();

    //   const rgbValues = rgbString.match(/\d+/g).map(Number);

    //   let r = rgbValues[0];
    //   let g = rgbValues[1];
    //   let b = rgbValues[2];

    //   r = Math.round((r * percent) / 100);
    //   g = Math.round((g * percent) / 100);
    //   b = Math.round((b * percent) / 100);

    //   r = Math.max(0, Math.min(255, r));
    //   g = Math.max(0, Math.min(255, g));
    //   b = Math.max(0, Math.min(255, b));

    //   const adjustedRGBString = `rgb(${r}, ${g}, ${b})`;

    //   return adjustedRGBString;
    // }
    // const offcanvas = document.querySelector('.offcanvas');
    // offcanvas.style.backgroundColor = adjustUIBrightness('--bs-primary-rgb', 100);
    // navbarBrand.style.backgroundColor = adjustUIBrightness('--bs-primary-rgb', 100);

    // SET UI COLOR FOR NON ADMIN
    function setColor(name, value, days) {
      let expires = "";
      if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
      window.location.reload();
    }
  </script>
@endsection
