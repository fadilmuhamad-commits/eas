@extends('layouts.admin')
@section('title', 'Configuration')

@section('content')
  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">configuration</li>
    </x-breadcrumb>

    <form action="{{ route('config.store') }}" method="POST" enctype="multipart/form-data" id="form-simpan">
      @csrf
      @method('put')
      <div class="card col overflow-hidden p-4">
        <h5 class="fw-semibold">Configuration</h5>

        <table class="mx-auto" style="width: 70%">
          <tr>
            <td class="fs-2 fw-bold pb-4 pt-2 ">IDENTITAS</td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Nama Instansi</td>
            <td class="pb-3">
              <input type="text" class="form-control" style="font-size: 14px; padding: 10px 20px" id="nama-instansi"
                name="nama-instansi" placeholder="Masukan nama instansi" value="{{ $config->instance_name ?? '' }}">
              @error('nama-instansi')
                <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
              @enderror
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan sebagai identitas instansi di aplikasi">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Primary Color</td>
            <td class="pb-3">
              <input type="color" class="w-100" id="color" name="color" style="height: 48px"
                value="{{ $config->Color1->hexcode ?? '' }}">
              <div class="d-flex gap-2 mt-2">
                @foreach ($colors as $color)
                  <div onclick="pickColor('#color', '{{ $color->hexcode }}')" data-tooltip="tooltip"
                    data-bs-placement="bottom" data-bs-title="{{ $color->hexcode }}"
                    style="cursor: pointer; width: 24px; height: 24px; background-color: {{ $color->hexcode }};"></div>
                @endforeach
              </div>
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan untuk warna <b> Sidebar, Table, Counter Data </b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Secondary Color</td>
            <td class="pb-3">
              <input type="color" class="w-100" id="color1" name="color1" style="height: 48px"
                value="{{ $config->Color2->hexcode ?? '' }}">
              <div class="d-flex gap-2 mt-2">
                @foreach ($colors as $color)
                  <div onclick="pickColor('#color1', '{{ $color->hexcode }}')" data-tooltip="tooltip"
                    data-bs-placement="bottom" data-bs-title="{{ $color->hexcode }}"
                    style="cursor: pointer; width: 24px; height: 24px; background-color: {{ $color->hexcode }};"></div>
                @endforeach
              </div>
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan untuk warna <b> Button, Header Sidebar </b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Tertiary Color</td>
            <td class="pb-3">
              <input type="color" class="w-100" id="color2" name="color2" style="height: 48px"
                value="{{ $config->Color3->hexcode ?? '' }}">
              <div class="d-flex gap-2 mt-2">
                @foreach ($colors as $color)
                  <div onclick="pickColor('#color2', '{{ $color->hexcode }}')" data-tooltip="tooltip"
                    data-bs-placement="bottom" data-bs-title="{{ $color->hexcode }}"
                    style="cursor: pointer; width: 24px; height: 24px; background-color: {{ $color->hexcode }};"></div>
                @endforeach
              </div>
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan untuk warna <b> Highlight Text </b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Running Text</td>
            <td class="pb-3">
              <textarea type="text" class="form-control" style="font-size: 14px; padding: 10px 20px" id="running-text"
                name="running-text" placeholder="Masukan running text">{{ $config->running_text ?? '' }}</textarea>
              @error('running-text')
                <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
              @enderror
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Ditampilkan pada page <b>Cetak Tiket dan Antrian Client</b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Logo 1</td>
            <td class="pb-3">
              <div>
                <input type="file" class="form-control" style="font-size: 14px; padding: 10px 20px" id="logo1"
                  name="logo1" onchange="previewImage(this, 'logo1-preview')">
                <div class="text-medium mt-1" style="font-size: 12px;">format: jpeg, png, jpg, gif, webp |
                  max size: 5mb
                </div>
              </div>
              <div class="d-flex gap-2">
                <img id="logo1-preview" src="{{ asset('storage/' . $config->logo1) }}" class="mt-2 border p-2 rounded"
                  style="height: 160px; display: {{ $config->logo1 ? 'block' : 'none' }};" alt="Logo 1">

                @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
                  <button onclick="deleteImage('logo1')" type="button" class="btn btn-danger mt-2 py-0"
                    style="height: fit-content" data-bs-toggle="modal" data-bs-target="#modal-config">
                    <i class="bi bi-trash"></i>
                @endif
                </button>
              </div>
              @error('logo1')
                <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
              @enderror
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan sebagai logo utama, ditampilkan pada <b> Admin Page, Cetak Tiket, Tiket Antrian, Booking Page, Antrian Client </b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Logo 2</td>
            <td class="pb-3">
              <div>
                <input type="file" class="form-control" style="font-size: 14px; padding: 10px 20px" id="logo2"
                  name="logo2" onchange="previewImage(this, 'logo2-preview')">
                <div class="text-medium mt-1" style="font-size: 12px;">format: jpeg, png, jpg, gif, webp |
                  max size: 5mb
                </div>
              </div>
              <div class="d-flex gap-2">
                <img id="logo2-preview" src="{{ asset('storage/' . $config->logo2) }}" class="mt-2 border p-2 rounded"
                  style="height: 160px; display: {{ $config->logo2 ? 'block' : 'none' }};" alt="Logo 2">

                @if (file_exists(public_path('storage/' . $config->logo2)) && $config->logo2)
                  <button onclick="deleteImage('logo2')" type="button" class="btn btn-danger mt-2 py-0"
                    style="height: fit-content" data-bs-toggle="modal" data-bs-target="#modal-config">
                    <i class="bi bi-trash"></i>
                @endif
              </div>
              @error('logo2')
                <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
              @enderror
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Digunakan sebagai logo kedua di page <b> Cetak Tiket dan Antrian Client </b>">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold " style="width: 8rem">Loading Image</td>
            <td class="pb-3">
              <div>
                <input type="file" class="form-control" style="font-size: 14px; padding: 10px 20px" id="loading"
                  name="loading" onchange="previewImage(this, 'loading-preview')">
                <div class="text-medium mt-1" style="font-size: 12px;">format: jpeg, png, jpg, gif, webp |
                  max size: 5mb
                </div>
              </div>
              <div class="d-flex gap-2">
                <img id="loading-preview" src="{{ asset('storage/' . $config->loading) }}"
                  class="mt-2 border p-2 rounded"
                  style="height: 160px; display: {{ $config->loading ? 'block' : 'none' }};" alt="Loading Image">

                @if (file_exists(public_path('storage/' . $config->loading)) && $config->loading)
                  <button onclick="deleteImage('loading')" type="button" class="btn btn-danger mt-2 py-0"
                    style="height: fit-content" data-bs-toggle="modal" data-bs-target="#modal-config">
                    <i class="bi bi-trash"></i>
                @endif
              </div>
              @error('loading')
                <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
              @enderror
            </td>
            <td class="d-flex align-items-start ms-2">
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="Ditampilkan pada saat proses cetak tiket">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fs-2 fw-bold py-4 ">PENGATURAN</td>
          </tr>
          <tr>
            <td class="fw-bold" style="width: 8rem">Isi Identitas</td>
            <td class="d-flex align-items-start gap-2">
              <div class="form-check form-switch ps-0">
                <input name="config-switch" onchange="configSwitch()" class="form-check-input mx-auto mt-0"
                  type="checkbox" role="switch" style="height: 32px; width: 56px;"
                  @if ($config->status === 2) checked @endif />
              </div>
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="<b>Off</b> = Mesin KiosK tidak dapat digunakan untuk mengisi identitas pengunjung <br> <b>On</b> = Mesin KiosK dapat digunakan untuk mengisi identitas oleh pengunjung">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td class="fw-bold pt-3" style="width: 8rem">Partnership</td>
            <td class="d-flex align-items-start gap-2 pt-3">
              <div class="form-check form-switch ps-0">
                <input name="partner-switch" onchange="partnerSwitch()" class="form-check-input mx-auto mt-0"
                  type="checkbox" role="switch" style="height: 32px; width: 56px;"
                  @if ($config->partnership === 2) checked @endif />
              </div>
              <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                data-bs-title="<b>Off</b> = Tidak dapat menggunakan Kode Anggota untuk melakukan Booking atau Cetak <br> <b>On</b> = Pengunjung dapat menggunakan Kode Anggota di API untuk melakukan Booking atau Cetak">
                <i class="bi bi-info-circle text-medium"></i>
              </div>
            </td>
          </tr>
          @if ($config->partnership === 2)
            <tr>
              <td class="fw-bold pt-3" style="width: 8rem">Partner API</td>
              <td class="d-flex  align-items-start gap-2 pt-3">
                <div>
                  <input type="text" class="form-control" style="font-size: 14px; padding: 10px 20px; width: 100%"
                    id="partner_api" name="partner_api" placeholder="e.g. https://example.com/api/user/{slug}"
                    value="{{ $config->partner_api ?? '' }}">
                  <div class="text-medium mt-1" style="font-size: 12px;">Format URL harus memiliki <b>"{slug}"</b> yang
                    merepresentasikan parameter API
                  </div>
                </div>
                <div data-tooltip="tooltip" data-bs-placement="right" data-bs-html="true"
                  data-bs-title="URL API untuk partnership">
                  <i class="bi bi-info-circle text-medium"></i>
                </div>
              </td>
            </tr>
          @endif
        </table>

        <div class="d-flex gap-2 p-3">
          <button type="submit" class="btn btn-primary fw-bold px-3 ms-auto" id="btn-simpan">Simpan</button>
        </div>
      </div>
    </form>
  </section>

  {{-- MODAL --}}
  <div class="modal fade" tabindex="-1" id="modal-config">
    <div class="modal-dialog modal-dialog-centered" style="width: fit-content">
      <div class="modal-content py-3 px-5">
        <div class="modal-body text-center px-0">
          <p>Apakah anda yakin ingin menghapus <br>
            <b class="modal-name text-tertiary" style="font-size: 33px"></b>
          </p>
        </div>
        <div class="modal-footer border-0 d-flex justify-content-center pt-0">
          <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Tidak</button>
          <form method="POST" id="form-delete">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-danger px-4 fw-bold" id="btn-delete-config">Ya</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div id="config-toast" class="toast align-items-center border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    function pickColor(id, hex) {
      $(id).val(hex);
    }

    function deleteImage(image) {
      let imageText;
      if (image == 'logo1') {
        imageText = 'Logo 1';
      } else if (image == 'logo2') {
        imageText = 'Logo 2';
      } else if (image == 'loading') {
        imageText = 'Loading Image';
      }

      $('#modal-config .modal-dialog .modal-content .modal-body .modal-name').text(imageText + '?');
      $('#form-delete').attr('action', "{{ route('config.delete.image', ['image' => 9999]) }}".replace(9999, image));
    }

    function configSwitch() {
      $.ajax({
        type: 'PUT',
        url: `{{ route('config.update.status') }}`,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#config-toast .d-flex .toast-body').text(response.message);
          $('#config-toast').addClass('text-bg-success');
          $('#config-toast').toast('show');
        },
        error: function(error) {
          console.error(error);
        }
      });
    }

    function partnerSwitch() {
      $.ajax({
        type: 'PUT',
        url: `{{ route('config.update.partner.status') }}`,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          window.location.reload();

          $('#config-toast .d-flex .toast-body').text(response.message);
          $('#config-toast').addClass('text-bg-success');
          $('#config-toast').toast('show');
        },
        error: function(error) {
          console.error(error);
        }
      });
    }

    function previewImage(input, previewId) {
      const preview = document.getElementById(previewId);
      const file = input.files[0];
      const reader = new FileReader();

      reader.onloadend = function() {
        preview.src = reader.result;
        preview.style.display = 'block';
      }

      if (file) {
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("form-simpan").addEventListener("submit", function() {
        document.getElementById("btn-simpan").disabled = true;
      });

      document.getElementById("form-delete").addEventListener("submit", function() {
        document.getElementById("btn-delete-config").disabled = true;
      });
    });
  </script>
@endsection
