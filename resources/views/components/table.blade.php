@props(['heading' => [], 'select' => false, 'sort' => '', 'order' => '', 'type' => '', 'permission' => ''])

<div class="mb-3" style="overflow-x: auto; flex: 1 1 0;">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        @if ($select)
          <th scope="col" class="position-sticky z-1" style="top: -1px">
            <input class="form-check-input border-medium" type="checkbox" id="selectAllCheckbox"
              style="width: 20px; height: 20px;">
          </th>
        @endif
        @foreach ($heading as $th)
          <th scope="col" class="position-sticky z-1" style="top: -1px">
            <div class="d-flex gap-2 align-items-center justify-content-center px-2">
              {{ $th['title'] }}
              @if ($th['sort'] ?? false)
                <i class="bi bi-sort-{{ $order == 'asc' && $th['sort'] == $sort ? 'up' : 'down' }}"
                  style="font-size: 18px; color: {{ $th['sort'] == $sort ? '#ffffff' : '#a0a0a0' }}; text-shadow: 1px 1px 2px black;"></i>
              @endif
            </div>

            @if ($th['sort'] ?? false)
              <div
                class="th-sort position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                style="background-color: rgba(0, 0, 0, .7)">
                <a href="{{ route(\Request::route()->getName(), array_merge(request()->query(), ['sort' => $th['sort'], 'order' => 'desc'])) }}"
                  class="btn btn-sm px-1 fs-5" style="color: white">
                  <i class="bi bi-sort-down"></i>
                </a>
                <a href="{{ route(\Request::route()->getName(), array_merge(request()->query(), ['sort' => $th['sort'], 'order' => 'asc'])) }}"
                  class="btn btn-sm px-1 fs-5" style="color: white">
                  <i class="bi bi-sort-up"></i>
                </a>
              </div>
            @endif

          </th>
        @endforeach

        @can($permission)
          <th scope="col" class="position-sticky z-1" style="top: -1px">Action</th>
        @endcan
      </tr>
    </thead>
    {{ $slot }}
  </table>
</div>

@if ($select)
  <div id="toastContainer" class="position-fixed" style="z-index: 998; right: 16px; top: 64px;">
  </div>
  <div id="modalContainer"></div>
@endif

<script>
  $(document).ready(function() {
    $('#selectAllCheckbox').click(function() {
      $('input[type="checkbox"].select-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
      updateToastContent();
    });

    $('.select-checkbox').change(function() {
      let allChecked = $('.select-checkbox:checked').length === $('.select-checkbox').length;
      $('#selectAllCheckbox').prop('checked', allChecked);
    });

    $('.table').append(
      `<style type="text/css">
          .table thead tr th {
            text-shadow: ${isColorBright('--bs-primary-rgb', 0.6) ? '1px 1px 2px black' : ''} !important;
          }
        </style>`
    );
  });

  @if ($select)
    function updateToastContent() {
      let selectedRows = [];

      $('input[type="checkbox"].select-checkbox').each(function() {
        if ($(this).prop('checked')) {
          selectedRows.push($(this).data('row-id'));
        }
      });

      if (selectedRows.length > 0) {
        let toastHtml = `
        <div id="deleteToast" class="toast py-1 px-2 bg-body" style="width: fit-content;" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
          <div id="toastBody" class="toast-body d-flex align-items-center gap-4">
            <div>${selectedRows.length} row(s) selected</div>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
              data-bs-target="#modal-selected">Delete Selected</button>
          </div>
        </div>
            `;
        $('#toastContainer').html(toastHtml);

        let modalHtml = `
          <div class="modal fade" tabindex="-1" id="modal-selected">
            <div class="modal-dialog modal-dialog-centered" style="width: fit-content">
              <div class="modal-content py-3 px-5">
                <div class="modal-body text-center px-0">
                  <p>Apakah anda yakin ingin menghapus <br>
                    <b class="text-tertiary" style="font-size: 33px">${selectedRows.length} data terpilih?</b>
                  </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                  <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Tidak</button>
                  <form onsubmit="submitSelectedRows()" action="{{ route(\Request::route()->getName() . '.destroy.selected', ['type' => $type]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="selectedRows" value="${JSON.stringify(selectedRows)}">
                    <button id="btn-selected-rows" type="submit" class="btn btn-danger px-4 fw-bold">Ya</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        `;
        $('#modalContainer').html(modalHtml);

        let deleteToast = new bootstrap.Toast($('#deleteToast'));
        deleteToast.show();
      } else {
        $('#toastContainer').html('');
      }
    }

    $('input[type="checkbox"].select-checkbox').on('change', function() {
      updateToastContent();
    });

    $(document).ready(function() {
      updateToastContent();
    });

    function submitSelectedRows() {
      document.getElementById('btn-selected-rows').disabled = true;
    }
  @endif
</script>

<style>
  .table {
    /* border: 1px solid rgb(var(--bs-secondary-rgb)); */
    font-size: 14px;
    white-space: nowrap;
    margin-bottom: 0;
  }

  .table th,
  td {
    /* border-right: 1px solid rgb(var(--bs-secondary-rgb)); */
    vertical-align: middle;
  }

  .table thead tr th {
    background: rgb(var(--bs-primary-rgb));
    text-align: center;
    overflow: hidden;
    color: white;
    padding-block: 6px;
  }

  @if ($select)
    .table thead tr th:nth-child(1),
    .table tbody tr td:nth-child(1),
    .table thead tr th:nth-child(2),
    .table tbody tr td:nth-child(2) {
      width: 40px;
      text-align: center;
    }
  @else
    .table thead tr th:first-child,
    .table tbody tr td:first-child {
      width: 40px;
      text-align: center;
    }
  @endif

  .th-sort {
    transition: all 200ms;
    opacity: 0;
    transform: translateY(20%);
  }

  .table thead tr th:hover .th-sort {
    opacity: 1;
    transform: none;
  }
</style>
