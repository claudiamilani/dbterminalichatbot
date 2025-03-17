@extends('layouts.adminlte.template',['page_title' => 'Dashboard', 'fa_icon_class' => 'fa-home'])

@section('content')
    <p>@lang('dashboard.welcome_msg',['app_name' => config('app.name')])</p>
    <div class="row">
        @component('components.widget',['title' => trans('common.storage_stats'), 'size' => 12])
            @slot('body')
                @component('widgets.appStorageStats')@endcomponent
            @endslot
        @endcomponent
    </div>

@endsection