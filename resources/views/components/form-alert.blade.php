@props(['field' => ''])

@error($field)
  <div class="text-danger" style="font-size: 12px; margin-top: 4px"> {{ $message }} </div>
@enderror
