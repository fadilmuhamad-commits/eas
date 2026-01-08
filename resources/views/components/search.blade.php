@props(['search' => '', 'searchBy' => '', 'options' => [], 'sort' => '', 'order' => '', 'type' => ''])

<form id="search-form" class="input-group" style="height: 31px; width: 19rem;">
  <input type="text" value="{{ $search }}" placeholder="Cari {{ !empty($options) ? 'berdasarkan' : '' }}"
    class="form-control py-0 border-medium" id="search" name="search" style="font-size: 14px; flex: 1.5 1.5 0%;">

  @if (!empty($options))
    <select class="form-select py-0 btn btn-outline-medium text-start" name="search_by" id="search_by"
      style="font-size: 14px; width: 2px !important;">
      <option value="" selected>Semua</option>

      @foreach ($options as $option)
        <option value="{{ $option['value'] }}" {{ $searchBy == $option['value'] ? 'selected' : '' }}>
          {{ $option['label'] }}
        </option>
      @endforeach
    </select>
  @endif

  <button id="search-submit" type="submit" class="btn btn-sm btn-primary rounded-start-0">Search</button>
</form>

<script>
  const loketSelect = document.getElementById('loket-select');
  if (loketSelect) {
    loketSelect.addEventListener('change', function() {
      updateUrl();
    });
  }

  const categorySelect = document.getElementById('category-select');
  if (categorySelect) {
    categorySelect.addEventListener('change', function() {
      updateUrl();
    });
  }

  const typeSelect = document.getElementById('type-select');
  if (typeSelect) {
    typeSelect.addEventListener('change', function() {
      updateUrl();
    });
  }

  const searchForm = document.getElementById('search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
      e.preventDefault();
      updateUrl();
    });
  }

  function updateUrl() {
    let selectedDate = document.querySelector('.datepicker input');
    let selectedCounter = document.getElementById('loket-select');
    let selectedCategory = document.getElementById('category-select');
    let selectedType = document.getElementById('type-select');
    let search = document.getElementById('search');
    let searchBy = document.getElementById('search_by');
    let perPage = document.getElementById('per-page');
    let sort = @json($sort);
    let order = @json($order);
    let type = @json($type);

    let baseUrl = "{{ Route::currentRouteName() }}";
    let urlParams = [];

    if (selectedDate && selectedDate.value) {
      urlParams.push('selected_date=' + selectedDate.value);
    }

    if (selectedCounter && selectedCounter.value) {
      urlParams.push('selected_counter=' + selectedCounter.value);
    }

    if (selectedCategory && selectedCategory.value) {
      urlParams.push('selected_category=' + selectedCategory.value);
    }

    if (selectedType && selectedType.value) {
      urlParams.push('customer_type=' + selectedType.value);
    }

    if (search && search.value) {
      urlParams.push('search=' + search.value);
    }

    if (searchBy && searchBy.value) {
      urlParams.push('search_by=' + searchBy.value);
    }

    if (perPage && perPage.value) {
      urlParams.push('perPage=' + perPage.value);
    }

    if (sort && order) {
      urlParams.push('sort=' + sort + '&order=' + order);
    }

    if (type) {
      urlParams.push('type=' + type);
    }

    window.location.href = baseUrl + (urlParams.length > 0 ? '?' + urlParams.join('&') : '');
  }
</script>
