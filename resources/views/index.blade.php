<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>{{ \Config::get('app.name') }} | @yield('title') </title>

  <!-- Fonts -->
  {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&display=swap" rel="stylesheet"> --}}

  {{-- Icons --}}
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> --}}

  {{-- GSAP --}}
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script> --}}

  {{-- Flatpickr --}}
  <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

  {{-- Favicon --}}
  @if (isset($config) && file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/' . $config->logo1) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/' . $config->logo1) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/' . $config->logo1) }}">
  @else
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  @endif
  <link rel="manifest" href="/site.webmanifest">

  {{-- jQuery --}}
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  {{-- Sortable --}}
  <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

  {{-- UI Color for non admin --}}
  @php
    $colorTheme = isset($_COOKIE['colorTheme']) ? $_COOKIE['colorTheme'] : 'default';

    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex)
        {
            $hex = ltrim($hex, '#');
            if (!ctype_xdigit($hex) || strlen($hex) != 6) {
                throw new InvalidArgumentException('Invalid hex color code');
            }

            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));

            return [$r, $g, $b];
        }
    }
  @endphp

  @vite('resources/js/app.js')
  @vite('resources/css/app.scss')

  <style>
    @if (isset($config) && Auth::check() && Auth::user()->Counter && Request::is('admin/*') && $colorTheme == 'custom')
      :root {
        --bs-primary-rgb: {{ implode(',', hexToRgb($config->Color1->hexcode)) }};
        --bs-secondary-rgb: {{ implode(',', hexToRgb(Auth::user()->Counter->Color->hexcode)) }};
        --bs-tertiary-rgb: {{ implode(',', hexToRgb($config->Color3->hexcode)) }};
      }
    @elseif(isset($config))
      :root {
        --bs-primary-rgb: {{ $config->Color1 ? implode(',', hexToRgb($config->Color1->hexcode)) : '' }};
        --bs-secondary-rgb: {{ $config->Color2 ? implode(',', hexToRgb($config->Color2->hexcode)) : '' }};
        --bs-tertiary-rgb: {{ $config->Color3 ? implode(',', hexToRgb($config->Color3->hexcode)) : '' }};
      }
    @endif

    .text-tertiary {
      color: rgb(var(--bs-tertiary-rgb)) !important;
    }

    .page-link.active,
    .active>.page-link {
      background-color: rgb(var(--bs-secondary-rgb)) !important;
      border-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    .form-check-input:checked[type=checkbox] {
      background-color: rgb(var(--bs-tertiary-rgb)) !important;
    }

    .form-switch .form-check-input:checked {
      background-color: rgb(var(--bs-success-rgb)) !important;
    }

    .btn-primary {
      background-color: rgb(var(--bs-secondary-rgb)) !important;
      border-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    .btn-outline-primary {
      color: rgb(var(--bs-secondary-rgb)) !important;
      border-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    .btn-outline-primary:hover {
      color: rgb(var(--bs-white-rgb)) !important;
      background-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    .dropdown-item.active,
    .dropdown-item:active {
      background-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    .nav-pills .nav-link.active {
      background-color: rgb(var(--bs-secondary-rgb)) !important;
      border-color: rgb(var(--bs-secondary-rgb)) !important;
    }

    body {
      /* background: url('/images/greyBg.png'); */
      background-color: #eee;
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }

    [data-bs-theme=dark] body {
      /* background: url('/images/blackBg.png'); */
      background-color: rgb(var(--bs-black-rgb));
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
  </style>
</head>

<body>
  <x-success-error-toast />

  @yield('layout')

  <script>
    function isColorBrightIndex(variable, bright) {
      const rgbString = getComputedStyle(document.documentElement).getPropertyValue(variable).trim();

      const rgbValues = rgbString.match(/\d+/g).map(Number);

      let r = rgbValues[0];
      let g = rgbValues[1];
      let b = rgbValues[2];

      const brightness = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

      return brightness > bright;
    }

    /* function adjustTextBrightness(rgbString, percent) {
      const rgbValues = rgbString.match(/\d+/g).map(Number);

      let r = rgbValues[0];
      let g = rgbValues[1];
      let b = rgbValues[2];

      r = Math.round((r * percent) / 100);
      g = Math.round((g * percent) / 100);
      b = Math.round((b * percent) / 100);

      r = Math.max(0, Math.min(255, r));
      g = Math.max(0, Math.min(255, g));
      b = Math.max(0, Math.min(255, b));

      return `rgb(${r}, ${g}, ${b})`;
    } */

    $(document).ready(function() {
      $('[data-bs-toggle="tooltip"]').tooltip();
      $('body').tooltip({
        selector: "[data-tooltip=tooltip]",
        container: "body"
      });

      /* const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary-rgb').trim(); */
      /* const adjustedPrimaryColor = adjustTextBrightness(primaryColor, 200); */
      /* [data-bs-theme=dark] .text-primary {color: ${adjustedPrimaryColor} !important} */

      $('head').append(
        `<style type="text/css">
          ${isColorBrightIndex('--bs-primary-rgb', 0.7) ? '.bg-primary>.text-white, .bg-primary.text-white {color: black !important}' : ''}
          ${isColorBrightIndex('--bs-secondary-rgb', 0.7) ? '.bg-secondary>.text-white, .bg-secondary.text-white {color: black !important}' : ''}
          ${isColorBrightIndex('--bs-tertiary-rgb', 0.7) ? '.bg-tertiary>.text-white, .bg-tertiary.text-white {color: black !important}' : ''}
          ${isColorBrightIndex('--bs-primary-rgb', 0.7) ? '.nav-title.text-white {color: black !important}' : ''}
          .btn-primary {color: ${isColorBrightIndex('--bs-secondary-rgb', 0.7) ? 'black' : 'white'} !important}
          .nav-pills .nav-link.active {color: ${isColorBrightIndex('--bs-secondary-rgb', 0.7) ? 'black' : 'white'} !important}
          .page-link.active, .active>.page-link {color: ${isColorBrightIndex('--bs-secondary-rgb', 0.7) ? 'black' : 'white'} !important}
          .form-check-input:checked[type=checkbox] {--bs-form-check-bg-image: ${isColorBrightIndex('--bs-tertiary-rgb', 0.7)
            ? 'url(data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20fill%3D%22%23000%22%20class%3D%22bi%20bi-check-lg%22%20viewBox%3D%220%200%2016%2016%22%3E%0A%20%20%3Cpath%20d%3D%22M12.736%203.97a.733.733%200%200%201%201.047%200c.286.289.29.756.01%201.05L7.88%2012.01a.733.733%200%200%201-1.065.02L3.217%208.384a.757.757%200%200%201%200-1.06.733.733%200%200%201%201.047%200l3.052%203.093%205.4-6.425z%22%2F%3E%0A%3C%2Fsvg%3E)'
            : 'url(data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20fill%3D%22%23fff%22%20class%3D%22bi%20bi-check-lg%22%20viewBox%3D%220%200%2016%2016%22%3E%0A%20%20%3Cpath%20d%3D%22M12.736%203.97a.733.733%200%200%201%201.047%200c.286.289.29.756.01%201.05L7.88%2012.01a.733.733%200%200%201-1.065.02L3.217%208.384a.757.757%200%200%201%200-1.06.733.733%200%200%201%201.047%200l3.052%203.093%205.4-6.425z%22%2F%3E%0A%3C%2Fsvg%3E)'}}
        </style>`
      );
    });
  </script>

</body>

</html>
