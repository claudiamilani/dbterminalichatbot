@extends('layouts.adminlte.template',['page_title' => trans('DBT/attribute_header_mappings.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/attribute_header_mappings.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => ['admin::dbt.ingestion_sources.attribute_header_mappings.store', paramsWithBackTo(['id' => $ingestion_source->id], null, ['#'.nav_fragment('DBT/attribute_header_mappings.title')])],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group required">
                    <label for="header_name" class="col-md-3 control-label"> @lang('DBT/attribute_header_mappings.attributes.header_name')</label>
                    <div class="col-md-9">
                        {!! Form::text('header_name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="dbt_attribute_id" class="col-md-3 control-label"> @lang('DBT/attribute_header_mappings.attributes.dbt_attribute_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('dbt_attribute_id', [], null, ['id' => 'dbt_attribute_id', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                    <a href="{{ backToSource('admin::dbt.ingestion_sources.show', $ingestion_source->id) }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script',['name' => 'dbt_attribute_id', 'inputTooShort' => trans('common.min_required_chars',['charNum' => 3]),'route' => route('admin::dbt.ingestion_sources.attribute_header_mappings.select2'), 'linkedClear'=>''])
        @slot('format_output')
            return output.text;
        @endslot
        @slot('format_selection')
            output = output.text;
            return output;
        @endslot
    @endcomponent
@endpush