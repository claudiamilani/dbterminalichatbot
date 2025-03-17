<div class="{{ $card_classes ?? 'col-lg-10 col-lg-offset-1' }}">
    <div class="col-lg-{{ $size ?? '12' }} mdl-card-layout">
        <h{{ $h_size ?? 3 }}>{{ $title ?? '' }}</h{{ $h_size ?? 3 }}>
        {{ $content ?? ''}}
    </div>
</div>