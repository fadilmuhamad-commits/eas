@props(['title' => '', 'value' => ''])

<style>
  #data-count {
    min-width: 144px
  }

  @media (max-width: 768px) {
    #data-count {
      width: fit-content !important;
      min-width: 0;
    }

    #data-count * {
      width: 100% !important;
      padding-inline: 24px !important;
    }
  }
</style>

<div id="data-count" class="card bg-primary text-white">
  <div class="card-header py-1 text-center" style="font-size: 14px; border-color: rgba(255,255,255,0.2);">
    {{ $title }}</div>
  <div class="card-body fs-2 fw-semibold py-0 text-center">{{ $value === '0' ? '-' : $value }}</div>
</div>
