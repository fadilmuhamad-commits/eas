@props(['categories' => [], 'selectedCategory' => ''])

@if (auth()->user()->Counter || auth()->user()->Role->hasPermission('view_all_counter'))
  <select id="category-select" class="form-select btn btn-outline-medium text-start dropdown-toggle py-1"
    style="font-size: 14px; height: 31px; width: 9rem;">
    <option value="" {{ $selectedCategory == '' ? 'selected' : '' }}>Kategori Loket</option>
    @foreach ($categories as $item)
      <option value="{{ $item->id }}" {{ $selectedCategory == $item->id ? 'selected' : '' }}>
        {{ $item->name }} ({{ $item->code }})
      </option>
    @endforeach
  </select>
@endif
