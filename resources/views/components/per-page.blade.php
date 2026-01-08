@props(['perPage' => ''])

<div class="d-flex justify-content-between align-content-center">
  <div class="d-flex gap-3 align-items-center" style="font-size: 14px; height: fit-content;">
    <span style="white-space: nowrap; font-size: 14px;">Rows per page</span>
    <select name="per-page" id="per-page" class="form-select" style="font-size: 14px;">
      <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
      <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
      <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
      <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
      <option value="250" {{ $perPage == 250 ? 'selected' : '' }}>250</option>
      <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
    </select>
  </div>
  <div class="pagination-container">
    {{ $slot }}
  </div>
</div>

<style>
  .pagination-container .d-sm-flex {
    gap: 16px;
  }
</style>

<script>
  const perPageSelect = document.getElementById('per-page');
  perPageSelect.addEventListener('change', function() {
    updateUrl();
  });
</script>
