@php foreach($items ?? [] as $k => $item)
    if(($item['hide_if'] ?? false) || !($item['show_if'] ?? true)) unset($items[$k]);
@endphp
@if($items ?? false)
    <div class="btn-group">
        <a href="{{ Illuminate\Support\Arr::first($items)['url'] ?? '#' }}"
           class="btn btn-{{ $size ?? 'sm' }} {{ $classes ?? 'btn-primary' }} @if($uppercase ?? false) text-uppercase  @endif" @foreach(Illuminate\Support\Arr::first($items)['attributes'] ?? [] as $k => $v)
            {!! $k."='$v'" !!}
                @endforeach title="{{Illuminate\Support\Arr::first($items)['title'] ?? Illuminate\Support\Arr::first($items)['label']}}">@if(isset(Illuminate\Support\Arr::first($items)['icon']))
                <i class="{{ Illuminate\Support\Arr::first($items)['icon'] }}"></i>
            @endif</a>
        @if(count($items) > 1)
            <button type="button" class="btn btn-{{ $size ?? 'sm' }} {{ $classes ?? 'btn-primary' }} dropdown-toggle"
                    data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>

            <div class="dropdown-menu" role="menu">
                @foreach($items as $item)
                    <a class="dropdown-item @if($uppercase ?? false) text-uppercase  @endif" href="{{ $item['url'] ?? '#' }}"
                           title="{{ $item['title'] ?? $item['label'] }}" @foreach($item['attributes'] ?? [] as $k => $v)
                            {!! $k."='$v'" !!}
                                @endforeach>@if(isset($item['icon']))
                                <i class="{{ $item['icon'] }}"></i>
                            @endif{{ $item['label'] }}</a>
                @endforeach
            </div>
        @endif
    </div>
@endif