@extends('index')
@section('title', 'Login')

@section('layout')
  <main id="login-main" class="overflow-hidden d-flex position-relative" style="height: 100vh;">
    <div id="main-card" class="card h-100 overflow-hidden position-relative z-1">
      {{-- Form Login --}}
      <div id="formL" class="card-body d-flex flex-column align-items-center justify-content-center">
        {{-- Title --}}
        <h3 class="text-black fw-bold mb-5" style="font-size: 28px;">Login</h3>

        <form id="form-login" style="font-size: 14px;" action="{{ route('login.validate') }}" method="POST">
          @csrf
          {{-- FormL-Username --}}
          <div class="mb-3">
            <label for="usernameL" class="form-label text-black fw-semibold">Username / Email</label>
            <input type="text" class="form-control py-2" style="font-size: 14px" id="usernameL" name="usernameL"
              placeholder="Masukkan username atau email" value="{{ old('usernameL') }}" autocomplete="username">
            @error('usernameL')
              <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
            @enderror
          </div>

          {{-- FormL-Password --}}
          <div class="mb-5">
            <label for="passwordL" class="form-label text-black fw-semibold">Password</label>
            <div class="d-flex align-items-center ">
              <input type="password" class="form-control py-2" style="font-size: 14px" name="passwordL" id="passwordL"
                placeholder="Masukkan password" autocomplete="current-password">
              <i class="bi bi-eye-slash fs-5 text-black " style="margin-left: -32px; cursor: pointer;"
                id="togglePasswordL"></i>
            </div>
            @error('passwordL')
              <span class="text-danger" style="font-size: 12px;"> {{ $message }} </span>
            @enderror
          </div>

          {{-- FormL-Submit --}}
          <button onclick="disableOnClick()" type="submit" id="login_submit"
            class="btn btn-primary w-100 fw-semibold mb-3">Login</button>

          {{-- Change Page --}}
          {{-- <div class="d-flex gap-2 fw-medium ">
            <span class="text-medium">Belum memiliki akun?</span>
            <a id="change-pageL" class="text-decoration-none text-black fw-bold" style="cursor: pointer">Buat Akun</a>
          </div> --}}
        </form>
      </div>
    </div>

    @if (file_exists(public_path('storage/' . $config->loading)) && $config->loading)
      <img src="{{ asset('storage/' . $config->loading) }}" alt="Logo {{ $config->nama_instansi }}"
        class="object-fit-cover position-absolute end-0 h-100" style="width: calc(100% - 54%);" loading="lazy" />
    @endif

    @if (file_exists(public_path('storage/' . $config->logo1)) && $config->logo1)
      <div class="bg-black bg-opacity-50 d-flex align-items-center justify-content-center position-absolute end-0 h-100"
        style="width: calc(100% - 54%);">
        <img src="{{ asset('storage/' . $config->logo1) }}" alt="Logo {{ $config->nama_instansi }}"
          class="object-fit-contain" style="height: 160px; filter: drop-shadow(2px 2px 5px #1c1c1c);" loading="lazy" />
      </div>
    @endif
  </main>

  <script>
    const togglePasswordL = document.querySelector('#togglePasswordL');
    const passwordL = document.querySelector('#passwordL');

    // const togglePasswordR = document.querySelector('#togglePasswordR');
    // const passwordR = document.querySelector('#passwordR');

    // const toggleCPassword = document.querySelector('#toggleCPassword');
    // const confirmPassword = document.querySelector('#confirm-password');

    // const changePageL = document.querySelector('#change-pageL')
    // const changePageR = document.querySelector('#change-pageR')

    function isViewportMatched(viewport) {
      return window.innerWidth < viewport;
    }

    // disable on click
    function disableOnClick() {
      setTimeout(() => {
        document.getElementById('login_submit').disabled = true;
        // document.getElementById('register_submit').disabled = true;
      }, 1);
    }

    // password visibility
    const togglePasswordVisibility = (toggleBtn, passwordInput) => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleBtn.classList.toggle('bi-eye');
    };

    // event listeners
    togglePasswordL.addEventListener('click', () => togglePasswordVisibility(togglePasswordL, passwordL));
    // togglePasswordR.addEventListener('click', () => togglePasswordVisibility(togglePasswordR, passwordR));
    // toggleCPassword.addEventListener('click', () => togglePasswordVisibility(toggleCPassword, confirmPassword));

    // change page
    // const changeToL = (dur) => {
    //   if (!isViewportMatched(768)) {
    //     gsap.fromTo('#main-card', {
    //       x: "66.5%",
    //     }, {
    //       x: 0,
    //       borderRadius: '0 12px 12px 0',
    //       ease: "expo.inOut",
    //       duration: dur,
    //     });
    //   }

    //   gsap.fromTo('#formL', {
    //     opacity: 0,
    //     x: '-200%',
    //   }, {
    //     opacity: 1,
    //     x: 0,
    //     ease: "expo.inOut",
    //     duration: dur,
    //   });

    //   gsap.fromTo('#formR', {
    //     left: 0,
    //   }, {
    //     left: '100%',
    //     ease: "expo.inOut",
    //     duration: dur,
    //   });
    // };

    // const changeToR = (dur) => {
    //   if (!isViewportMatched(768)) {
    //     gsap.fromTo('#main-card', {
    //       x: 0,
    //     }, {
    //       x: "66.5%",
    //       borderRadius: '12px 0 0 12px',
    //       ease: "expo.inOut",
    //       duration: dur,
    //     })
    //   }

    //   gsap.fromTo('#formL', {
    //     opacity: 1,
    //     x: 0,
    //   }, {
    //     opacity: 0,
    //     x: "-200%",
    //     ease: "expo.inOut",
    //     duration: dur,
    //   })

    //   gsap.fromTo('#formR', {
    //     left: '100%',
    //   }, {
    //     left: 0,
    //     ease: "expo.inOut",
    //     duration: dur,
    //   })
    // }

    // let x = window.matchMedia("(max-width: 768px)")
    // x.addEventListener("change", function() {
    //   if (isViewportMatched(768)) {
    //     gsap.fromTo('#main-card', {
    //       x: "66.5%",
    //     }, {
    //       x: 0,
    //       borderRadius: '0 12px 12px 0',
    //       ease: "expo.inOut",
    //       duration: 0,
    //     });
    //   }
    // });

    // changePageR.addEventListener('click', () => changeToL(1));
    // changePageL.addEventListener('click', () => changeToR(1));

    // @if ($errors->has('usernameR') || $errors->has('email') || $errors->has('passwordR') || $errors->has('confirm-password'))
    //   changeToR(0);
    // @endif
  </script>

  <style>
    @if (!file_exists(public_path('storage/' . $config->loading)) || !$config->loading)
      #login-main {
        background: url('/images/loginBg.png');
        background-repeat: no-repeat;
        background-size: cover;
      }
    @endif

    #main-card {
      width: 55%;
      border-radius: 0 12px 12px 0;
    }

    #form-login {
      width: 70%;
    }

    @media (max-width: 768px) {
      #main-card {
        width: 100%;
        border-radius: 0;
      }

      #form-login {
        width: 90%;
      }
    }
  </style>
@endsection
