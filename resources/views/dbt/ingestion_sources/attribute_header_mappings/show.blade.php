@extends('layouts.adminlte.template',['page_title' => trans('DBT/attribute_header_mappings.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/attribute_header_mappings.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/attribute_header_mappings.attributes.header_name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$mapping->header_name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attribute_header_mappings.attributes.dbt_attribute_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$mapping->dbtAttribute->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/attribute_header_mappings.attributes.created_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$mapping->created_at_info}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/attribute_header_mappings.attributes.updated_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$mapping->updated_at_info}}</p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', $mapping)<a
                            href="{{ route('admin::dbt.ingestion_sources.attribute_header_mappings.edit',paramsWithBackTo([ $mapping->id],'admin::dbt.ingestion_sources.attribute_header_mappings.show', $mapping->id)) }}"
                            class="btn btn-md btn-primary pull-right"><i class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{backToSource('admin::dbt.ingestion_sources.attribute_header_mappings.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
    </div>
@endsection

@push('scripts')

@endpush