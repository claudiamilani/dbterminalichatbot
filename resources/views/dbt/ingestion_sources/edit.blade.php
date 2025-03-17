@extends('layouts.adminlte.template',['page_title' => trans('DBT/ingestion_sources.edit.title'), 'fa_icon_class' => 'fa-users'])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8, 'title' => trans('DBT/ingestion_sources.edit.title')])
            @slot('body')
                {!! Form::model($ingestion_source,['route' => ['admin::dbt.ingestion_sources.update', paramsWithBackTo($ingestion_source->id)], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    <label for="enabled"
                           class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <input type="hidden" name="enabled" value="0">
                        {!! Form::checkbox('enabled', 1, null, [ 'id' => 'enabled','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' =>trans('common.active'), 'data-off' => trans('common.disabled'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="name"
                           class="col-md-3 control-label">@lang('DBT/ingestion_sources.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null,[ 'id' => 'enabled','class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="priority"
                           class="col-md-3 control-label">@lang('DBT/ingestion_sources.attributes.priority')</label>
                    <div class="col-md-2">
                        {!! Form::number('priority', null,[ 'id' => 'priority','class' => 'form-control', 'min'=>0]) !!}
                    </div>
                </div>
                <fieldset>
                    <legend>@lang('DBT/ingestion_sources.attributes.default_options')</legend>
                </fieldset>
                @foreach($ingestion_source->default_options as $key => $value)
                    <div class="form-group">
                        <label for=""
                               class="col-md-3 control-label">@lang('DBT/ingestion_sources.options.'.$key)</label>
                        <div class="col-md-1">
                            <input type="hidden" name="{{$key}}" value="0">
                            {!! Form::checkbox($key, 1, $value, ['id' => $key, 'class' => 'form-control']) !!}
                        </div>
                    </div>
                @endforeach
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{backToSource('admin::dbt.ingestion_sources.index')}}" class="btn btn-md btn-secondary">
                        <i class="fas fa-fw fa-arrow-left" ></i>@lang('common.form.back')
                    </a>
                    <button type="submit" class="btn btn-md btn-primary"><i
                                class="fas  fa-save fa-fw"></i> @lang('common.form.save')</button>
                </div>
            @endslot
        @endcomponent
    </div>
    {!! Form::close() !!}

@endsection
@push('scripts')
    <script>
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-orange pull-right',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    </script>
@endpush