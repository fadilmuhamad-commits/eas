@extends('layouts.bar')
@section('title', 'Show')

@section('col')
  @php
    $chunkLength = count($counters->chunk(6));
  @endphp

  <div class="d-flex gap-4 w-100">
    <div id="show-container" class="d-flex col position-relative">
      {{--
    @foreach ($lokets->chunk(6) as $i => $chunk)
      <div id="grid-{{ $i }}" class="d-grid gap-3 px-4 position-absolute top-0 start-0 translate-middle"
        style="width: 100vw; grid-template-columns: repeat(3, 1fr); opacity: 0; transition: opacity .8s;">
        @foreach ($chunk as $loket)
          @php
            $tiketAktif = $active->where('loket_id', $loket->id)->first();
          @endphp

          <div class="d-flex flex-column gap-2">
            <div id="status-{{ $loket->id }}"
              class="w-100 py-2 text-center rounded-1 fs-5 {{ $loket->status == 1 ? 'text-bg-primary fw-bold' : 'bg-medium bg-opacity-75' }}">
              {{ $loket->status == 1 ? 'SEDANG PRAKTEK' : 'TIDAK PRAKTEK' }}
            </div>

            <div class="d-flex gap-2">
              <div class="col card text-center" style="background-color: {{ $loket->Color->hexcode }}">
                <span class="card-header text-white border-white fw-bold" style="font-size: 20px;">NOMOR ANTRIAN</span>
                <div class="card-body" style="min-height: 124px;">
                  {{-- @if ($tiketAktif) --}}
      {{-- <p id="antrian-{{ $loket->id }}" class="pt-4 text-white fw-bold" style="font-size: 40px;"> --}}
      {{-- {{ $loket->kode }}-{{ $tiketAktif->queue_number }} --}}
      {{-- </p> --}}
      {{-- @else --}}
      {{-- <p class="pt-4 text-white fw-bold" style="font-size: 40px;">
                      --
                    </p> --}}
      {{-- @endif --}}
      {{-- </div>
                <span class="card-footer text-white border-white" style="font-size: 20px;">{{ $loket->name }}</span>
              </div> --}}

      {{-- @php
                $adjustedHexcode = adjustShowBrightness($loket->Color->hexcode, 50);
              @endphp --}}

      {{-- <div class="col-4 card text-center" style="background-color: {{ $adjustedHexcode }};"> --}}
      {{-- Header --}}
      {{-- <span class="card-header text-white border-white fw-bold" style="font-size: 20px;">NEXT</span> --}}

      {{-- List --}}
      {{-- <div id="queue-{{ $loket->id }}"
                  class="d-flex flex-column gap-1 text-white py-2 fs-5 fw-bold overflow-hidden" style="height: 160px;">
                  @foreach ($queue->where('loket_id', $loket->id) as $next)
                    <div>{{ $loket->kode }}-{{ $next->queue_number }}</div>
                  @endforeach
                </div>
              </div>

            </div>
          </div>
        @endforeach
      </div>
    @endforeach --}}
    </div>

    <div class="col-3" style="height: 100vh; padding-block: 112px 48px; padding-right: 24px;">
      <div class="card bg-secondary text-white h-100">
        <div class="card-header text-center text-white py-3 border-white position-relative">
          <div class="fs-4 position-relative z-1">ANTRIAN</div>
          <div class="fs-5 fw-bold position-relative z-1">{{ $group->name }}</div>

          <div
            class="h-100 w-100 position-absolute top-0 start-0 bg-black bg-opacity-25 text-black fw-bold overflow-hidden"
            style="font-size: 64px; opacity: 0.08; white-space: nowrap;">
            {{ $group->name }}
          </div>
        </div>
        <div class="card-body d-flex p-0 overflow-hidden" style="max-height: 100%;">
          <div id="queue-container" class="col py-3 text-center d-grid gap-0" style="place-items: center;">
          </div>
        </div>
      </div>
    </div>

    <button id="showModal" class="d-none" data-bs-toggle="modal" data-bs-target="#show-modal">Play Audio</button>

    <div class="modal fade" id="show-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content py-4">
          <div class="modal-body text-center">
            <h5 class="fw-semibold">TV Screen</h5>
            <h1 class="fw-bold mb-5">Antrian {{ $group->name }}</h1>
            <button id="closeModal" type="button" class="btn btn-primary fs-4" data-bs-dismiss="modal">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    @foreach ($audio as $char)
      <audio class="audios" id="audio-{{ $char }}" src="/sounds/{{ $char }}.wav" preload="auto"
        muted></audio>
    @endforeach
  </div>

  <style>
    #bar-container {
      padding-block: 0px !important;
    }
  </style>

  <script>
    let currentIndex = {{ $chunkLength - 1 }};

    // IS THE COLOR BRIGHT
    function isColorBright(hexcode) {
      const r = parseInt(hexcode.slice(1, 3), 16);
      const g = parseInt(hexcode.slice(3, 5), 16);
      const b = parseInt(hexcode.slice(5, 7), 16);

      const brightness = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

      return brightness > 0.8;
    }

    // GENERATE QUEUE CARD COLOR
    function adjustShowBrightness(hex, percent) {
      hex = hex.replace(/^#/, '');

      const bigint = parseInt(hex, 16);

      let r = (bigint >> 16) & 255;
      let g = (bigint >> 8) & 255;
      let b = bigint & 255;

      r = Math.round((r * percent) / 100);
      g = Math.round((g * percent) / 100);
      b = Math.round((b * percent) / 100);

      r = Math.max(0, Math.min(255, r));
      g = Math.max(0, Math.min(255, g));
      b = Math.max(0, Math.min(255, b));

      const adjustedHex = '#' + (1 << 24 | r << 16 | g << 8 | b).toString(16).slice(1);

      return adjustedHex;
    }

    // GENERATE LOKET GRIDS
    function generateLoketsView(loketsData) {
      let html = '';

      const chunkLength = Math.ceil(loketsData.length / 6);

      for (let i = 0; i < chunkLength; i++) {
        const chunkStart = i * 6;
        const chunkEnd = (i + 1) * 6;
        const chunk = loketsData.slice(chunkStart, chunkEnd);
        let gridColumns;
        let gridRows;

        if (chunk.length >= 3) {
          gridColumns = 3;
          gridRows = chunk.length == 3 ? 1 : 2;
        } else if (chunk.length == 2) {
          gridColumns = 2;
          gridRows = 1
        } else if (chunk.length == 1) {
          gridColumns = 1;
          gridRows = 1
        }

        html += `
          <div id="grid-${i}" class="d-grid gap-3 px-4 position-absolute top-0 start-0 ${i == currentIndex ? 'z-2' : ''}"
            style="width: 100%; height: 100%; padding-block: ${gridRows == 1 ? '10rem' : '7.5rem'} ${gridRows == 1 ? '96px' : '48px'}; padding-inline: 48px; grid-template-columns: repeat(${gridColumns}, 1fr); grid-template-rows: repeat(${gridRows}, 1fr); opacity: ${i == currentIndex ? '1' : '0'}; transition: opacity 800ms, translate 500ms cubic-bezier(0.65,0.05,0.36,1);">
              ${generateChildrenView(chunk)}
          </div>`;
      }

      return html;
    }

    // GENERATE LOKET CHILDREN
    function generateChildrenView(chunk) {
      let html = '';

      chunk.forEach(loket => {
        let tiketAktif = loket.has_many_tickets[0] ?
          loket.has_many_tickets[0].counter__category.code + '-' + loket.has_many_tickets[0].queue_number :
          '--';
        let nomorAktif = loket.has_many_tickets[0] ? loket.has_many_tickets[0].queue_number : '--';
        let loketAktif = loket.has_many_tickets[0] ? loket.has_many_tickets[0].counter__category.code : '';
        let textColor = isColorBright(loket.color.hexcode) ? 'text-black' : 'text-white border-white';

        html += `
      <div class="d-flex flex-column gap-2">
        <div class="w-100 py-2 text-center rounded-1 fs-5 text-white ${loket.status == 1 ? 'fw-bold bg-success' : 'bg-medium bg-opacity-75' }">
          ${loket.status == 1 ? 'SEDANG MELAYANI' : 'TIDAK AKTIF'}
        </div>

        <div class="d-flex gap-2 h-100">
          <div class="col card text-center ${loket.status == 2 ? 'bg-medium bg-opacity-75' : ''}">
            <div class="card-header ${textColor} fw-bold" style="font-size: 20px; background-color: ${loket.status == 1 ? loket.color.hexcode : ''};">NOMOR ANTRIAN</div>
            <div style="background-color: ${loket.status == 1 ? loket.color.hexcode : ''};" class="card-body d-flex align-items-center justify-content-center position-relative">
              <div id="antrian-${loket.id}" class="${isColorBright(loket.color.hexcode) ? 'text-black' : 'text-white'} position-relative fw-bold z-1" style="font-size: ${chunk.length > 3 ? '48px' : '64px'};">
                ${tiketAktif}
              </div>

              <div class="position-absolute d-flex align-items-center justify-content-center top-0 start-0 h-100 w-100 bg-black bg-opacity-25 overflow-hidden">
                <div class="${isColorBright(loket.color.hexcode) ? 'text-white' : 'text-black'} fw-bold d-flex flex-column ${chunk.length > 3 ? '' : 'gap-2'}" style="font-size: ${chunk.length > 3 ? '104px' : '112px'}; opacity: .08; white-space: nowrap;">
                  ${nomorAktif}
                </div>
              </div>
            </div>
            <div class="card-footer ${textColor}" style="background-color: ${loket.status == 1 ? loket.color.hexcode : ''}; font-size: 20px; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1;">${loket.name}</div>
          </div>
        </div>
      </div>`;
      });

      return html;
    }

    document.addEventListener('DOMContentLoaded', function() {
      let audios = Array.from(document.querySelectorAll('.audios'));
      audios.forEach(function(audio) {
        audio.play();
      })

      let loketsData = @json($counters);
      let groupData = @json($group);
      let categoriesData = @json($categories);
      let audioPlayed = false;

      const queueContainer = document.getElementById('queue-container');
      const showContainer = document.getElementById('show-container');
      let grids = document.querySelectorAll('[id^="grid-"]');

      // GENERATE QUEUE VIEW
      function generateQueueView(categories) {
        let html = '';
        let gridColumns;
        let gridRows;
        let fontSize;

        if (categories.length >= 4) {
          gridColumns = 'repeat(2, auto)';
          gridRows = 'repeat(100, 50px)';
          fontSize = '32px';
        } else {
          gridColumns = 'repeat(1, 1fr)';
          gridRows = '';
          fontSize = '56px';
        }
        queueContainer.style.gridTemplateColumns = gridColumns;
        queueContainer.style.gridTemplateRows = gridRows;

        categories.forEach(category => {
          html += `
          <div class="fw-bold" style="font-size: ${fontSize};">
            ${category.code}-${category.has_many_tickets[0] ? category.has_many_tickets[0].queue_number : ''}
          </div>
      `;
        });

        return html;
      }

      // UPDATE LOKET GRIDS
      function updateLoketGrids() {
        showContainer.innerHTML = generateLoketsView(loketsData);
        grids = document.querySelectorAll('[id^="grid-"]');
      }

      // UPDATE QUEUE VIEW
      function updateQueue() {
        queueContainer.innerHTML = generateQueueView(categoriesData);
      }

      // FIRST HTML INITIALIZATION
      updateLoketGrids();

      // GET DATA ON FOCUS ONLY
      // let visibilityChange = false;
      // document.addEventListener("visibilitychange", function() {
      //   if (document.hidden) {
      //     visibilityChange = true;
      //   } else {
      //     visibilityChange = false;
      //     ajaxGet();
      //   }
      // });

      // AJAX GET
      function ajaxGet() {
        $.ajax({
          url: '{{ route('ajax.show', 9999) }}'.replace(9999, groupData.id),
          type: 'GET',
          success: function(res) {
            updateData(res);

            // AUDIO
            if (res.antrian.length !== 0) {
              let kodeAntrian = res.antrian[0].code
              let splitKode = [...kodeAntrian.toLowerCase()];

              if (!audioPlayed) {
                setTimeout(() => {
                  playAudio(splitKode, res.antrian[0].id);
                }, 500);
              }
            }

            // if (!visibilityChange) {
            setTimeout(ajaxGet, 3000);
            // }
          },
          error: function(xhr, status, error) {
            console.error(error);
            // if (!visibilityChange) {
            setTimeout(ajaxGet, 3000);
            // }
          }
        })
      }

      document.getElementById("showModal").click();
      document.getElementById("closeModal").addEventListener("click", function() {
        ajaxGet();
      });

      // DELETE ANTRIAN
      function deleteAntrian(id) {
        $.ajax({
          url: '{{ route('ajax.antrian.delete', 9999) }}'.replace(9999, id),
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(res) {
            console.log('Antrian deleted');
          },
          error: function(xhr, status, error) {
            console.error(error);
          }
        })
      }

      // PLAY AUDIO
      function playAudio(kode, id) {
        audioPlayed = true;

        let bell = new Audio(`/sounds/notif.mp3`);
        bell.play().then(() => {
          setTimeout(() => {
            kode.forEach((char, i) => {
              setTimeout(() => {
                let audio = document.getElementById(`audio-${char}`);
                audio.muted = false;
                audio.play();

                setTimeout(() => {
                  audio.currentTime = 0;
                }, 100);
              }, i * 700);
            });

            setTimeout(() => {
              audioPlayed = false;
            }, kode.length * 700);
            deleteAntrian(id);
          }, 1000);
        })
      }


      // WEBSOCKET
      // window.Echo.channel('show')
      //   .listen('.WS_Show', (e) => {
      //     updateData(e);
      //   });

      // UPDATE DATA PER DATA RETRIEVED
      function updateData(data) {
        loketsData = data.counters;
        categoriesData = data.categories;

        updateLoketGrids();
        updateQueue();
      }

      // PAGE ANIM
      function hideAllGrids() {
        grids.forEach(grid => {
          grid.style.translate = '-5%';
          grid.style.opacity = '0';
        });
      }

      function showNextGrid() {
        hideAllGrids();
        currentIndex++;

        if (currentIndex >= grids.length) {
          currentIndex = 0;
        }

        grids[currentIndex].style.translate = '0';
        grids[currentIndex].style.opacity = '1';

        setTimeout(showNextGrid, 7000);
      }

      showNextGrid();
    });
  </script>
@endsection
