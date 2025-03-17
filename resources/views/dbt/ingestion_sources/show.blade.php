@extends('layouts.adminlte.template', ['page_title' => trans('DBT/ingestion_sources.title'), 'fa_icon_class' => ''])
@section('page_nav_default')
    @component('components.page_navigation')@endcomponent
@endsection
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/ingestion_sources.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="enabled"
                               class="col-md-3 control-label"> @lang('DBT/ingestion_sources.attributes.enabled')</label>
                        <div class="col-md-3">
                            <i class="form-control-static {{$ingestion_source->enabled ? 'fas fa-fw fa-check text-success' : 'fas fw-fw fa-xmark text-danger' }}"></i> {{$ingestion_source->enabled ? trans('common.yes') : trans('common.no') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name"
                               class="col-md-3 control-label"> @lang('DBT/ingestion_sources.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion_source->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="priority"
                               class="col-md-3 control-label"> @lang('DBT/ingestion_sources.attributes.priority')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion_source->priority}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="updated_at"
                               class="col-md-3 control-label">@lang('DBT/ingestion_sources.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion_source->UpdatedAtInfo}} </p>
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
                                <i class="form-control-static {{$value ? 'fas fa-fw fa-check text-success' : 'fas fw-fw fa-xmark text-danger' }}"></i> {{$value ? trans('common.yes') : trans('common.no') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', $ingestion_source)
                        <a href="{{ route('admin::dbt.ingestion_sources.edit', paramsWithBackTo($ingestion_source->id,'admin::dbt.ingestion_sources.show',$ingestion_source->id)) }}"
                           class="btn btn-md btn-primary pull-right"> <i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{ backToSource('admin::dbt.ingestion_sources.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
    </div>
    <div class="row">
        @include('dbt.ingestion_sources.attribute_header_mappings.index')
    </div>
@endsection
