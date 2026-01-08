<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Error 404 (Page not found)</title>

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

  @vite('resources/css/app.scss')
</head>

<body>
  <div class="d-flex align-items-center justify-content-center bg-black text-white" style="height: 100vh">
    <div class="text-center d-flex flex-column mx-4">
      <span class="fw-medium user-select-none" style="font-size: 80px">404</span>
      <span class=" user-select-none" style="font-size: 24px">Page not found</span>
      <span class="fw-light mt-4 user-select-none" style="font-size: 16px">The page you are currently looking for is not
        found or doesn’t
        exist.</span>
    </div>
  </div>
</body>

</html>
