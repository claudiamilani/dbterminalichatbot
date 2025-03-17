@extends('layouts.adminlte.template',['page_title' => trans('external_roles.create.title')])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 12, 'hide_required_legend'=>true])
            @slot('title')
                @lang('external_roles.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::external_roles.store','class' => 'form-horizontal']) !!}
                <div class="form-group required row">
                    <label for="auth_type_id"
                           class="col-md-2 control-label">@lang('external_roles.attributes.auth_type_id')</label>
                    <div class="col-md-10">
                        <div class="col-md-4">
                            {!! Form::select('auth_type_id', $auth_types, null,  ['class' => 'form-control', 'id' => 'auth_type_id', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group required row">
                    <label for="external_role_id"
                           class="col-md-2 control-label">@lang('external_roles.attributes.external_role_id')</label>
                    <div class="col-md-10">
                        <div class="col-md-4 ">
                            {!! Form::select('external_role_id', [], null,  ['class' => 'form-control', 'id' => 'external_role_id', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="roles"
                           class="col-md-2 control-label">@lang('external_roles.attributes.role_id')</label>
                    <div class="col-md-10">
                        <div class="col-md-4">
                            {!! Form::select('roles[]', $roles, null,  ['class' => 'form-control', 'id' => 'roles','multiple']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="auto_register_users"
                           class="col-md-2 control-label">@lang('external_roles.attributes.auto_register_users')</label>
                    <div class="col-md-10">
                        <div class="col-md-4">
                            {!! Form::select('auto_register_users', [1=>trans('common.yes'), 0=>trans('common.no')], null,  ['class' => 'form-control', 'id' => 'auto_register_users']) !!}
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::external_roles.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit"
                            class=" btn btn-md btn-primary"> <i class="fas fa-fw fa-save"></i>@lang('common.form.save')</button>
                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'roles[]', 'route' => route('admin::roles.select2')])
        @slot('format_selection')
            if(output.id !== '') {
            output = output.text
            return output;
            }
            return '';
        @endslot
        @slot('format_output')
            if(!output.loading) {
            output = output.text
            return output;
            }
            return output.text;
        @endslot
    @endcomponent
    @component('components.select2_script', ['name' => 'external_role_id', 'route' => route('admin::external_roles.select2'), 'tags'=>true])
        @slot('dataParams')
            auth_type_id: $('#auth_type_id').val()
        @endslot
        @slot('format_selection')
            if(!output.loading) {
            output = output.text
            return output;
            }
            return output.text;
        @endslot
        @slot('format_output')
            if(!output.loading) {
            if(!output.existing) {
            output = 'Nuova Ruolo Esterno: ' + output.text
            }else{
            output = 'Ruolo esistente: ' + output.text
            }
            }
            return output
        @endslot
    @endcomponent
@endpush