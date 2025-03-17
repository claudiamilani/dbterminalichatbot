<title>@if(!empty($title)){{ $title. ' - ' .env('APP_NAME')}}@else {{ env('APP_NAME') }}@endif</title>
@if(!empty($description))
    <meta name="description" content="{{ str_limit($description,155,'') }}">
@endif
@if(!empty($keywords))
    <meta name="description" content="{{ is_array($keywords) ? implode(' ',$keywords): $keywords }}">
@endif