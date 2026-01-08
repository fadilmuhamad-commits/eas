@props(['loket' => [], 'selectedLoket' => ''])

@if (!auth()->user()->Counter || auth()->user()->Role->hasPermission('view_all_counter'))
  <select id="loket-select" class="form-select btn btn-outline-medium text-start dropdown-toggle py-1"
    style="font-size: 14px; height: 31px; width: 7.25rem;">
    <option value="" {{ $selectedLoket == '' ? 'selected' : '' }}>Pilih Loket</option>
    @foreach ($loket as $item)
      <option value="{{ $item->id }}" {{ $selectedLoket == $item->id ? 'selected' : '' }}>
        {{ $item->name }}
      </option>
    @endforeach
  </select>
@endif
