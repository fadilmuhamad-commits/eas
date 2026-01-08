@extends('layouts.admin')
@section('title', 'Panggil Antrian')

@section('content')
  <style>
    @media (min-width: 1024px) {

      #queue-list,
      #note-form {
        position: absolute;
        transform: translateY(-50%);
      }
    }

    .swapped {
      background-color: rgba(var(--bs-tertiary-rgb), 0.5);
      font-style: italic;
    }
  </style>

  <section class="h-100 d-flex flex-column">
    <x-breadcrumb>
      <li class="breadcrumb-item active" aria-current="page">panggil-antrian</li>
    </x-breadcrumb>

    <div class="card col d-flex flex-column gap-4 overflow-hidden p-4 position-relative">
      <div class="d-flex">
        <h5 class="fw-semibold">Panggil Antrian</h5>
      </div>

      {{-- Note --}}
      @if ($tiket)
        <form method="POST" id="note-form" class="top-50 z-1" style="left: 24px;">
          @csrf
          @method('PUT')
          <span style="font-size: 14px">Catatan Pengunjung</span>
          <textarea class="form-control mb-2 p-2 rounded-2 my-2" name="note-input" cols="16" rows="8"
            placeholder="Masukkan catatan pengunjung" style="font-size: 14px; max-height: 288px;">{{ $tiket->note ?? '' }}</textarea>
          {{-- <button class="btn btn-sm btn-primary">Simpan</button> --}}
          <div style="font-size: 12px">
            <div class="mb-1">
              <span class="text-medium">Nama: </span>
              <span id="pengunjung-name">
                {{ $tiket->Customer->name ?? '' }}
              </span>
            </div>
            <div>
              <span class="text-medium">No. Registrasi: </span>
              <span id="pengunjung-regis">
                {{ $tiket->Customer->registration_code ?? '' }}
              </span>
            </div>
          </div>
        </form>
      @endif

      {{-- Queue --}}
      <div id="queue-list" class="z-1 mt-lg-0 mt-2 mb-5 top-50" style="right: 24px; height: 224px;">
        <select id="panggil-select" class="form-select btn btn-outline-medium text-start dropdown-toggle py-1"
          style="font-size: 14px; height: 31px; width: 9rem;">
          <option value="" {{ $selectedCategory == '' ? 'selected' : '' }}>Kategori Loket</option>
          @foreach ($categories as $item)
            <option value="{{ $item->id }}" {{ $selectedCategory == $item->id ? 'selected' : '' }}>
              {{ $item->name }} ({{ $item->code }})
            </option>
          @endforeach
        </select>

        <div class="mt-2" style="font-size: 14px">Antrian Selanjutnya</div>

        <ul id="queue"
          class="d-flex flex-column overflow-auto border rounded-1 h-100 my-2 px-0 pb-2 position-relative"
          style="list-style-position: unset; font-size: 14px;">
          @foreach ($queue as $i => $item)
            <li data-id="{{ $item->id }}" data-pos="{{ $item->position }}"
              class="ps-2 pe-5 py-2 queue-item {{ $i == 0 ? 'bg-secondary fw-bold text-white' : '' }}">
              {{ $item->position . '. ' . $item->Counter_Category->code . '-' . $item->queue_number }}
            </li>
          @endforeach
        </ul>

        <div id="spinner" style="top: 65px;"
          class="w-100 h-100 bg-black bg-opacity-75 position-absolute left-0 d-flex align-items-center justify-content-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <div style="font-size: 12px">
          <span class="text-medium">Total: </span>
          <span id="queue-count">
            {{ count($queue) ?? 0 }}
          </span>
        </div>
      </div>

      <div class="d-flex flex-column align-items-center gap-5 my-lg-auto mt-5 position-relative">
        @if ($calling)
          <div class="position-absolute" style="top: -32px;">
            SEDANG MEMANGGIL
          </div>
        @endif

        {{-- Nomor Card --}}
        <div class="card bg-primary text-white px-4" style="width: fit-content">
          <div class="card-body fs-2 text-center">
            Nomor Antrian :
            <span class="fw-bold">
              {{ $tiket->Counter_Category->code ?? '' }}-{{ $tiket->queue_number ?? '' }}
            </span>
          </div>
        </div>

        {{-- Timer --}}
        @if (Auth::user()->Counter->status === 1)
          <div id="timer-container"
            class="{{ $tiket->Customer ?? false ? 'd-flex' : 'd-none' }} align-items-end flex-wrap justify-content-center gap-2">
            <div class="text-center">
              <span>Waktu:</span>
              <div class="card mt-1" style="height: 52px;">
                <div id="antrian-timer" class="card-body fs-3 fw-semibold py-2 px-4">00:00:00</div>
              </div>
            </div>

            <button id="timer-btn" onclick="toggleTimer()" class="btn btn-primary fw-semibold px-4"
              style="height: 52px;">Start</button>

            <form action="{{ route('submit-antrian', $tiket->id ?? '') }}" method="POST">
              @csrf
              @method('PUT')
              <input type="hidden" name="seconds_res" id="seconds_res_1" value="">
              <button type="submit" id="reset-timer" onclick="stopTimer()" class="btn link-danger fw-semibold px-4"
                style="height: 52px;">Selesai</button>
            </form>
          </div>
        @endif

        {{-- ISI IDENTITAS --}}
        <button id="btn-identitas" type="button"
          class="{{ $calling && !$tiket->Customer ? '' : 'd-none' }} btn btn-medium fs-5 py-2 px-4 border-0 fw-semibold text-white"
          data-bs-toggle="modal" data-bs-target="#modal-input-identitas">
          ISI IDENTITAS
        </button>

        {{-- Panggil BTNs --}}
        @if (Auth::user()->Counter->status === 1)
          <div class="d-flex gap-2 flex-wrap justify-content-center">
            @if ($calling)
              <form
                action="{{ route('next-antrian', ['ticket' => $tiket->id, 'selected_category' => $selectedCategory]) }}"
                method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="seconds_res" id="seconds_res_2" value="">
                <button type="submit" onclick="stopTimer()" id="next"
                  class="btn btn-primary fs-4 py-3 px-5 border-0 fw-semibold text-white">NEXT</button>
              </form>
            @endif

            <form id="panggil-form" method="POST">
              @csrf
              @method('PUT')
              <button type="button" onclick="submitForm()" id="panggil"
                class="btn btn-danger fs-4 py-3 px-5 border-0 fw-semibold text-white"
                style="background-color: #A00018;">PANGGIL</button>
            </form>

            @if ($calling)
              <button onclick="recallAntrian({{ $tiket->id }})" id="panggil-ulang"
                class="btn btn-warning fs-4 py-3 px-5 border-0 fw-semibold text-white"
                style="background-color: #A46C00;">PANGGIL ULANG</button>
            @endif
          </div>
        @endif

        {{-- Loket Switch --}}
        @if (!$calling || ($calling && Auth::user()->Counter->status === 2))
          <div class="form-check form-switch p-0 d-flex align-items-center gap-3">
            <form id="switch-form" action="{{ route('switch-antrian', Auth::user()->counter_id) }}" method="POST">
              @csrf
              @method('PUT')

              <input onchange="loketSwitch()" id="switch" class="form-check-input mx-auto mt-0" type="checkbox"
                name="switch_loket" role="switch" style="height: 32px; width: 56px;" value="1"
                @if (Auth::user()->Counter->status === 1) checked @endif />
              <input type="hidden" name="switch_loket" value="2" id="hidden-switch" />

            </form>
            <label class="{{ Auth::user()->Counter->status === 1 ? 'text-success' : 'text-danger' }}">Loket
              {{ Auth::user()->Counter->status === 1 ? 'Aktif' : 'Nonaktif' }}</label>
          </div>
        @endif
      </div>
    </div>
  </section>

  <div id="panggil-toast" class="toast align-items-center border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  {{-- MODAL INPUT IDENTITAS --}}
  @if ($tiket)
    <div class="modal fade" tabindex="-1" id="modal-input-identitas">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content py-3 px-5">
          <div class="modal-body px-0">
            <form class="w-100" method="POST" id="form-identitas">
              @csrf
              <div class="fs-1 fw-bold text-center mt-2">
                Input Identitas
                <hr>
              </div>
              <div class="mb-3">
                <label for="nama-lengkap" class="form-label fw-semibold mt-4">Nama
                  Lengkap</label>
                <input type="text" autocomplete="name" class="form-control py-2" style="font-size: 14px"
                  id="nama-lengkap" name="name" placeholder="Masukkan nama lengkap">
                @error('name')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>
              <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
                <div class="col">
                  <label for="email" class="form-label fw-semibold">Email</label>
                  <input type="email" autocomplete="email" class="form-control py-2" style="font-size: 14px"
                    id="email" name="email" placeholder="Masukkan email">
                  @error('email')
                    <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                  @enderror
                </div>
                <div class="col">
                  <label for="nomor-telepon" class="form-label fw-semibold">Nomor
                    Telepon</label>
                  <input type="tel" pattern="[^a-zA-Z]*" autocomplete="tel" class="form-control py-2"
                    style="font-size: 14px" id="nomor-telepon" name="phone_number" placeholder="Masukkan nomor">
                  @error('phone_number')
                    <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="d-flex flex-column flex-md-row gap-3 mb-3" style="flex-wrap: wrap">
                <div class="col">
                  <label for="birth_place" class="form-label fw-semibold">Tempat
                    Lahir</label>
                  <input type="text" class="form-control py-2" style="font-size: 14px" id="birth_place"
                    name="birth_place" placeholder="Masukkan tempat lahir">
                  @error('birth_place')
                    <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                  @enderror
                </div>
                <div class="col">
                  <label for="birth_date" class="form-label fw-semibold">Tanggal
                    Lahir</label>
                  <input type="date" autocomplete="bday" class="form-control py-2" style="font-size: 14px"
                    id="birth_date" name="birth_date" max="9999-12-31">
                  @error('birth_date')
                    <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="mb-4">
                <label for="address" class="form-label fw-semibold">Alamat</label>
                <input type="text" autocomplete="street-address" class="form-control py-2" style="font-size: 14px"
                  id="address" name="address" placeholder="Masukkan alamat">
                @error('address')
                  <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                @enderror
              </div>

              <button data-bs-toggle="modal" data-bs-target="#myModal" type="submit"
                class="btn btn-primary w-100 fw-bold mb-3" id="btn-booking">Simpan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- DEBOUNCE LIBRARY --}}
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21"></script>

  <script>
    // TIKET
    let panggilForm = document.getElementById('panggil-form');
    let calling = @json($calling);
    let queue = @json($queue);
    let tiket = calling ? @json($tiket) : queue[0];

    // CATEGORY SELECT
    let selectedCategory = @json($selectedCategory);
    $("#panggil-select").change(function() {
      let baseUrl = "{{ Route::currentRouteName() }}";
      let urlParams = [];

      if (this.value) {
        urlParams.push('selected_category=' + this.value);
      }

      window.location.href = baseUrl + (urlParams.length > 0 ? '?' + urlParams.join('&') : '');
    });

    // TIMER
    let startTime;
    let elapsedTime = 0;
    let timerInterval;
    let timerElement = document.getElementById('antrian-timer');
    let toggleBtn = document.getElementById('timer-btn');
    let isTimerRunning = false;
    let isPicked = false;

    function showSpinner() {
      $('#spinner').removeClass('d-none');
      $('#spinner').addClass('d-flex');
    }

    function hideSpinner() {
      $('#spinner').addClass('d-none');
      $('#spinner').removeClass('d-flex');
    }
    hideSpinner();

    // SORTABLE
    $('#queue').sortable({
      animation: 150,
      easing: "cubic-bezier(1, 0, 0, 1)",
      handle: '.handle',
      swap: true,
      swapClass: 'swapped',
      onStart: function() {
        isPicked = true;
      },
      onSort: function(evt) {
        let positions = [];
        let swapItemId = $(evt.swapItem).data('id');
        let swapItemPos = $(evt.swapItem).data('pos');
        let itemId = $(evt.item).data('id');
        let itemPos = $(evt.item).data('pos');

        // if (swapItemPos === itemPos) {
        //   swapItemPos++;
        // }

        positions.push({
          id: itemId,
          position: swapItemPos
        });
        positions.push({
          id: swapItemId,
          position: itemPos
        });

        showSpinner();

        $.ajax({
          url: '{{ route('update-queue') }}',
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            positions: positions
          },
          success: function(response) {
            console.log(response.success);
            isPicked = false;
            getQueue(selectedCategory);

            setTimeout(hideSpinner, 700);
          },
          error: function(xhr, status, error) {
            console.error(error);
            setTimeout(hideSpinner, 700);
          }
        });
      }
    });

    function updateQueueView(data) {
      let html = ``;

      data.forEach((item, i) => {
        html += `
          <li data-id="${item.id}" data-pos="${item.position}"
            class="ps-2 pe-5 py-2 position-relative queue-item ${i == 0 ? 'bg-secondary fw-bold text-white' : ''}">
            ${item.position + '. ' + item.counter__category.code + '-' + item.queue_number}
            <div class="btn btn-sm ${i == 0 ? 'text-white' : ''} p-1 handle position-absolute top-50 translate-middle-y" style="right: 4px;">
              <i class="bi bi-arrows-move"></i>
            </div>
          </li>
        `;
      })

      return html
    }

    // GET QUEUE ON FOCUS ONLY
    let visibilityChange = false;
    document.addEventListener("visibilitychange", function() {
      if (document.hidden) {
        visibilityChange = true;
      } else {
        visibilityChange = false;
        getQueue(selectedCategory);
      }
    });

    // GET QUEUE AJAX
    function getQueue(categoryId) {
      let ajaxUrl;
      if (categoryId == null) {
        ajaxUrl = "{{ route('ajax.queue') }}"
      } else {
        ajaxUrl = "{{ route('ajax.queue', ['category' => 9999]) }}".replace(9999, categoryId);
      }

      $.ajax({
        url: ajaxUrl,
        type: 'GET',
        success: function(res) {
          // console.log(res);
          hideMainSpinner();
          $('#queue').html(updateQueueView(res.queue));
          $('#queue-count').text(queue.length);
          queue = res.queue;

          if (queue.length !== 0) {
            let queueId = queue[0].id;
            let newAction =
              "{{ route('call-antrian', ['ticket' => 9999, 'selected_category' => $selectedCategory]) }}".replace(
                9999, queueId);
            $('#panggil-form').attr('action', newAction);

            if (!calling) {
              $('#panggil-form').removeClass('d-none');
            }
          }

          if (!visibilityChange && !isPicked) {
            setTimeout(function() {
              getQueue(categoryId);
            }, 2000);
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
          if (!visibilityChange && !isPicked) {
            setTimeout(function() {
              getQueue(categoryId);
            }, 2000);
          }
        }
      })

      if (calling || queue.length == 0) {
        $('#panggil-form').addClass('d-none');
      }
    }
    getQueue(selectedCategory);

    // IDENTITAS
    $("#form-identitas").submit(function(e) {
      e.preventDefault();
      submitIdentitas();
    });

    function recallAntrian(id) {
      disableOnClick();
      $.ajax({
        type: 'POST',
        url: '{{ route('recall-antrian', $tiket->id ?? '') }}',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          console.log(response.success)
        },
        error: function(error) {
          console.error(error);
        }
      });
    }

    function submitIdentitas() {
      let formData = $('#form-identitas').serialize();
      $.ajax({
        type: 'POST',
        url: '{{ route('identitas-antrian', $tiket->id ?? '') }}',
        data: formData,
        success: function(response) {
          $('#panggil-toast .d-flex .toast-body').text(response.message);
          $('#panggil-toast').addClass('text-bg-success');
          $('#panggil-toast').toast('show');

          setTimeout(() => {
            $('#pengunjung-name').text(response.pengunjung.name);
            $('#pengunjung-regis').text(response.pengunjung.registration_code);
          }, 1000);

          $('#btn-identitas').addClass('d-none');
          $('#timer-container').removeClass('d-none');
          $('#timer-container').addClass('d-flex');
        },
        error: function(error) {
          console.error(error);
        }
      });
    }

    // SUBMIT PANGGIL FORM
    function submitForm() {
      disableOnClick();
      panggilForm.submit();
    }

    // DISABLE ON CLICK
    function disableOnClick() {
      setTimeout(() => {
        document.getElementById('next') ? document.getElementById('next').disabled = true : ''
        document.getElementById('panggil') ? document.getElementById('panggil').disabled = true : ''
        document.getElementById('panggil-ulang') ? document.getElementById('panggil-ulang').disabled =
          true : ''
        document.getElementById('reset-timer') ? document.getElementById('reset-timer').disabled = true : ''
        document.getElementById('timer-btn') ? document.getElementById('timer-btn').disabled = true : ''
        document.getElementById('switch') ? document.getElementById('switch').disabled = true : ''
      }, 1);

      setTimeout(() => {
        document.getElementById('next') ? document.getElementById('next').disabled = false : ''
        document.getElementById('panggil') ? document.getElementById('panggil').disabled = false : ''
        document.getElementById('panggil-ulang') ? document.getElementById('panggil-ulang').disabled =
          false : ''
        document.getElementById('reset-timer') ? document.getElementById('reset-timer').disabled = false :
          ''
        document.getElementById('timer-btn') ? document.getElementById('timer-btn').disabled = false : ''
        document.getElementById('switch') ? document.getElementById('switch').disabled = false : ''
      }, 6000);
    }

    // NOTE
    $(document).ready(function() {
      showMainSpinner();
      let debouncedSubmitNoteForm = _.debounce(submitNoteForm, 1000);

      $('#note-form textarea[name="note-input"]').on('input', function() {
        debouncedSubmitNoteForm();
      });

      function submitNoteForm() {
        let formData = $('#note-form').serialize();

        $.ajax({
          type: 'PUT',
          url: '{{ route('note-antrian', $tiket->id ?? '') }}',
          data: formData,
          success: function(response) {
            $('#panggil-toast .d-flex .toast-body').text(response.message);
            $('#panggil-toast').addClass('text-bg-success');
            $('#panggil-toast').toast('show');
          },
          error: function(error) {
            console.error(error);
          }
        });
      }
    });

    // TIMER FUNCTIONS
    function toggleTimer() {
      if (isTimerRunning) {
        pauseTimer();
      } else {
        startTimer();
      }
    }

    function startTimer() {
      startTime = new Date().getTime() - elapsedTime;
      timerInterval = setInterval(updateTimer, 1000);
      toggleBtn.textContent = 'Pause';
      isTimerRunning = true;

      toggleBtn.classList.add('btn-warning');
    }

    function pauseTimer() {
      clearInterval(timerInterval);
      toggleBtn.textContent = 'Resume';
      isTimerRunning = false;

      toggleBtn.classList.remove('btn-warning');
    }

    function stopTimer() {
      disableOnClick();
      clearInterval(timerInterval);
      toggleBtn ? toggleBtn.textContent = 'Start' : '';
      isTimerRunning = false;

      let secondsRes = Math.floor(elapsedTime / 1000); // to be submitted to database
      document.getElementById('seconds_res_1') ? document.getElementById('seconds_res_1').value = secondsRes : '';
      document.getElementById('seconds_res_2') ? document.getElementById('seconds_res_2').value = secondsRes : '';

      elapsedTime = 0;
      toggleBtn ? toggleBtn.classList.remove('btn-warning') : '';
      updateTimer();
    }

    function updateTimer() {
      if (isTimerRunning) {
        let currentTime = new Date().getTime();
        elapsedTime = currentTime - startTime;
      } else {
        elapsedTime = tiket ? tiket.duration * 1000 : 0
      }

      let hours = Math.floor(elapsedTime / (60 * 60 * 1000));
      let minutes = Math.floor((elapsedTime % (60 * 60 * 1000)) / (60 * 1000));
      let seconds = Math.floor((elapsedTime % (60 * 1000)) / 1000);

      hours = hours < 10 ? '0' + hours : hours;
      minutes = minutes < 10 ? '0' + minutes : minutes;
      seconds = seconds < 10 ? '0' + seconds : seconds;

      timerElement ? timerElement.textContent = `${hours}:${minutes}:${seconds}` : '';
    }
    updateTimer();

    function loketSwitch() {
      disableOnClick();
      const switchForm = document.getElementById('switch-form');
      const checkbox = switchForm.querySelector('[name="switch_loket"]');
      const hiddenSwitch = switchForm.querySelector('#hidden-switch');

      hiddenSwitch.value = checkbox.checked ? 1 : 2;

      switchForm.submit();
    }
  </script>

@endsection
