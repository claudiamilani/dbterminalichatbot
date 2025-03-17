@extends('layouts.adminlte.template',['page_title' => trans('DBT/transpose_configs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/transpose_configs.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group ">
                        <label for="dbt_attribute_id"
                               class="col-md-3 control-label">@lang('DBT/transpose_configs.attributes.dbt_attribute_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$config->dbtAttribute->name}}</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="label"
                               class="col-md-3 control-label"> @lang('DBT/transpose_configs.attributes.label')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$config->label}}</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="type"
                               class="col-md-3 control-label"> @lang('DBT/transpose_configs.attributes.type')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$config->type}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="display_order"
                               class="col-md-3 control-label">@lang('DBT/transpose_configs.attributes.display_order')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$config->display_order}}</p>
                        </div>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$config)
                        <a href="{{ route('admin::dbt.transpose_configs.edit', $config->id) }}"
                           class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{ route('admin::dbt.transpose_configs.index') }}" class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i>@lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
    </div>
@endsection
