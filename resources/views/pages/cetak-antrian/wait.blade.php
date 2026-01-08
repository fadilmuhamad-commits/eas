@extends('layouts.bar')
@section('title', 'Wait')
@section('col')
  <style>
    .lds-roller {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 80px;
    }

    .lds-roller div {
      animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      transform-origin: 40px 40px;
    }

    .lds-roller div:after {
      content: " ";
      display: block;
      position: absolute;
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: rgb(var(--bs-primary-rgb));
      margin: -4px 0 0 -4px;
    }

    .lds-roller div:nth-child(1) {
      animation-delay: -0.036s;
    }

    .lds-roller div:nth-child(1):after {
      top: 63px;
      left: 63px;
    }

    .lds-roller div:nth-child(2) {
      animation-delay: -0.072s;
    }

    .lds-roller div:nth-child(2):after {
      top: 68px;
      left: 56px;
    }

    .lds-roller div:nth-child(3) {
      animation-delay: -0.108s;
    }

    .lds-roller div:nth-child(3):after {
      top: 71px;
      left: 48px;
    }

    .lds-roller div:nth-child(4) {
      animation-delay: -0.144s;
    }

    .lds-roller div:nth-child(4):after {
      top: 72px;
      left: 40px;
    }

    .lds-roller div:nth-child(5) {
      animation-delay: -0.18s;
    }

    .lds-roller div:nth-child(5):after {
      top: 71px;
      left: 32px;
    }

    .lds-roller div:nth-child(6) {
      animation-delay: -0.216s;
    }

    .lds-roller div:nth-child(6):after {
      top: 68px;
      left: 24px;
    }

    .lds-roller div:nth-child(7) {
      animation-delay: -0.252s;
    }

    .lds-roller div:nth-child(7):after {
      top: 63px;
      left: 17px;
    }

    .lds-roller div:nth-child(8) {
      animation-delay: -0.288s;
    }

    .lds-roller div:nth-child(8):after {
      top: 56px;
      left: 12px;
    }

    @keyframes lds-roller {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>

  <script>
    // GENERATE PDF
    let token = @json($token);

    function redirectSuccess() {
      window.location.href = "{{ route('success', ['token' => $token]) }}";
    }
    // setTimeout(() => {
    //   redirectSuccess();
    // }, 10000);

    function printPDF(pdfData) {
      let iframe = document.createElement('iframe');
      iframe.style.display = 'none';
      document.body.appendChild(iframe);

      iframe.src = pdfData;

      iframe.onload = function() {
        iframe.contentWindow.print();

        setTimeout(() => {
          redirectSuccess();
        }, 10000);
      };
    }

    $.ajax({
      type: 'GET',
      url: '{{ route('pdf') }}',
      data: {
        token: token
      },
      success: function(response) {
        console.log('PDF generated successfully');
        // window.open(response.url, '_blank');
        printPDF(response.url);
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  </script>

  <div class="d-flex flex-column">
    <div class="row d-flex flex-column justify-content-center text-center align-items-center">
      @if (file_exists(public_path('storage/' . $config->loading)) && $config->loading)
        <img src="{{ asset('storage/' . $config->loading) }}" loading="lazy"
          style="width: 40rem; height: 21rem; object-fit: cover; border-radius: 16px" alt="Mohon Ditunggu">
      @endif
      <div class="lds-roller my-3">
        @for ($i = 0; $i < 7; $i++)
          <div></div>
        @endfor
      </div>
      <p><b style="font-size: 33px">{{ $ticket->Counter_Category->name }}</b><br>Nomor antrian sedang dicetak</p>
    </div>
  </div>
@endsection
