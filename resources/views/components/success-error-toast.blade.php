@if (session()->has('error'))
  <div id="error-toast" class="toast align-items-center text-bg-danger border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 998;" role="alert" aria-live="assertive" aria-atomic="true"
    data-bs-autohide="true">
    <div class="d-flex">
      <div class="toast-body">
        {{ session('error') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      let errorToast = new bootstrap.Toast($('#error-toast'));
      errorToast.show();
    });
  </script>
@endif

@if (session()->has('success') && Request::is('admin/*') && !Request::is('admin/client/*'))
  <div id="success-toast" class="toast align-items-center text-bg-success border-0 position-fixed p-2"
    style="top: 16px; left: 16px; z-index: 998;" role="alert" aria-live="assertive" aria-atomic="true"
    data-bs-autohide="true">
    <div class="d-flex">
      <div class="toast-body">
        {{ session('success') }}
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      let successToast = new bootstrap.Toast($('#success-toast'));
      successToast.show();
    });
  </script>
@endif
