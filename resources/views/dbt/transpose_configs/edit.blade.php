@extends('layouts.adminlte.template',['page_title' => trans('DBT/transpose_configs.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        {!! Form::model($config, ['method' => 'patch', 'route' => ['admin::dbt.transpose_configs.update',$config->id],'class' => 'form-horizontal','size' => 12]) !!}
        @component('components.widget',['size' => 8])
            @slot('title')
                @lang('DBT/transpose_configs.edit.title')
            @endslot
            @slot('body')
                <div class="form-group required">
                    <label for="dbt_attribute_id"
                           class="col-md-3 control-label">@lang('DBT/transpose_configs.attributes.dbt_attribute_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('dbt_attribute_id',[$config->dbtAttribute->id => $config->dbtAttribute->name] ,$config->dbtAttribute->id , ['id' => 'dbt_attribute_id','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="label"
                           class="col-md-3 control-label"> @lang('DBT/transpose_configs.attributes.label')</label>
                    <div class="col-md-9">
                        {!! Form::text('label',null , ['id' => 'label','class' => 'form-control','required']) !!}
                    </div>
                    <div class="col-md-9 col-md-offset-3">
                        <small><i class="fa fa-info-circle text-info"></i> @lang('DBT/transpose_configs.validation.hint')</small>
                    </div>
                </div>
                <div class="form-group required">
                    <label for="type"
                           class="col-md-3 control-label"> @lang('DBT/transpose_configs.attributes.type')</label>
                    <div class="col-md-9">
                        {!! Form::select('type',$types , $config->type,['id' => 'type','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/transpose_configs.attributes.display_order')</label>
                    <div class="col-md-9">
                        {!! Form::number('display_order',null, ['id' => 'display_order', 'class' => 'form-control', 'min'=>0]) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                    <a href="{{ route('admin::dbt.transpose_configs.index') }}"
                       class="btn btn-md btn-warning pull-right">
                        <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection

@push('scripts')
    @component('components.select2_script', ['name' => 'dbt_attribute_id', 'route' => route('admin::dbt.transpose_configs.select2DbtAttribute')])
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
@endpush
