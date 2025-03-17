@extends('layouts.adminlte.template',['page_title' => trans('auth_types.title')])

@push('styles')

@endpush
@section('content')
    <div class="row">
        {!! Form::model($auth_type, ['method' => 'patch', 'route' => ['admin::auth_types.update',$auth_type->id],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
        @component('components.widget',['size' => 10, 'title' => trans('auth_types.edit.title')])
            @slot('body')

                <div class="form-group required">
                    <label for="" class="col-md-2 control-label">@lang('auth_types.attributes.name')</label>
                    <div class="col-md-9">
                        <p class="form-control-static">{{ $auth_type->name }}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="enabled" class="col-md-2 control-label">@lang('auth_types.attributes.enabled')</label>
                    <div class="col-md-1">
                        <input type="hidden" name="enabled" value="0">
                        <div class="icheck-primary">
                            {!! Form::checkbox('enabled', 1, null, [ 'id' => 'enabled']) !!}
                            <label for="enabled"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="default" class="col-md-2 control-label">@lang('auth_types.attributes.default')</label>
                    <div class="col-md-1">
                        <input type="hidden" name="default" value="0">
                        <div class="icheck-primary">
                            {!! Form::checkbox('default', 1, null, [ 'id' => 'default']) !!}
                            <label for="default"></label>
                        </div>
                    </div>
                </div>
                @if($auth_type->auto_register !== null)
                    <div class="form-group">
                        <label for="auto_register"
                               class="col-md-2 control-label">@lang('auth_types.attributes.auto_register')</label>
                        <div class="col-md-1">
                            <input type="hidden" name="auto_register" value="0">
                            <div class="icheck-primary">
                                {!! Form::checkbox('auto_register', 1, null, [ 'id' => 'auto_register']) !!}
                                <label for="auto_register"></label>
                            </div>
                        </div>
                    </div>
                @endif
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                    <a href="{{ backToSource('admin::auth_types.index')}}"><button type="button" class="btn btn-md btn-secondary pull-right"> <i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button></a>
                </div>
            @endslot
        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    <!-- iCheck -->
    <script>
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-orange',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    </script>
@endpush