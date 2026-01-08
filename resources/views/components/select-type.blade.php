@props(['selectedType' => ''])

<select id="type-select" class="form-select btn btn-outline-medium text-start dropdown-toggle py-1"
  style="font-size: 14px; height: 31px; width: 10rem;">
  <option value="" {{ $selectedType == '' ? 'selected' : '' }}>Tipe Pengunjung</option>
  <option value="default" {{ $selectedType == 'default' ? 'selected' : '' }}>
    default
  </option>
  <option value="partner" {{ $selectedType == 'partner' ? 'selected' : '' }}>
    partner
  </option>
</select>
