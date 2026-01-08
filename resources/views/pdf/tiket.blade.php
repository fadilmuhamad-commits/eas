<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    {{-- {{ date('dmy') }}_{{ $categoryL->code . '-' . $tiket->queue_number }} --}}
    {{ $categoryL->code . '-' . $tiket->queue_number }}
  </title>

  @vite('resources/css/app.scss')

  <style>
    @page {
      size: 65mm 120mm;
    }

    body {
      font-family: Inter, sans-serif;
    }
  </style>
</head>

<body>
  <div style="aspect-ratio: 5/6;">
    <div style="text-align: center; position: relative; padding-block: 0">
      <div style="font-size: 3.5mm; position: absolute; left: 50%; transform: translateX(-50%); font-weight: 600;">
        @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
          <img src="{{ public_path('storage/' . $config->logo1) }}" alt="{{ $config->instance_name }}"
            style="height: 11mm; object-fit: contain; margin-bottom: 2mm;" />
        @endif
        <div style="font-size: 4mm; margin-bottom: 4.5mm; font-weight: bold">{{ $config->instance_name }}</div>
        <div>{{ $date }}</div>
        <div style="margin-bottom: 3mm">{{ $time }} WIB</div>
        <div style="font-size: 3.5mm">No. Antrian :</div>
        <div style="font-size: 16mm; white-space: nowrap; margin-bottom: 5mm; font-weight: bold">
          {{ $categoryL->code . '-' . $tiket->queue_number }}
        </div>
        {{-- @if ($anggota == 1 || $anggota == true) --}}
        <div style="font-size: 4mm; margin-bottom: 4mm;">
          {{ $pengunjung->name ?? '' }}
        </div>
        {{-- @endif --}}
        <div style="font-size: 3mm">Silakan menunggu dengan tertib,</div>
        <div style="font-size: 3.5mm;">Terima kasih.</div>
      </div>
    </div>
  </div>
</body>

</html>
