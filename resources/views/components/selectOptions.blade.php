@if(isset($dateCheck))
    @foreach($items as $item)
        <option data-date="{{$item->requires_datetime}}" value="{{ $item->id }}">{{ $item->name }}</option>
    @endforeach
@endif
@if(!isset($dateCheck))
    @foreach($items as $k => $item)
        <option value="{{ $k }}">{{ $item }}</option>
    @endforeach
@endif

@isset($placeholder)
    <option value="" selected>-</option>
@endisset