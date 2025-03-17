@extends('layouts.adminlte.template',['page_title' => trans('DBT/vendors.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/vendors.show.title')
            @endslot

            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $vendor->name }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.published')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                <i class="fas fa-fw {{ $vendor->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                {{ $vendor->published ? 'Sì' : 'No' }}
                            </p>
                        </div>
                    </div>

                    @if($vendor->ingestion_id)
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.ingestion_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $vendor->ingestionSource->name }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.ingestion_source_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $vendor->ingestion->id }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.description')</label>
                        <div class="col-md-9">
                            {!! Form::textarea('description',$vendor->description, [ 'class' => 'form-control vertical noResize height-md', 'readonly']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $vendor->createdAtInfo }}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/vendors.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $vendor->updatedAtInfo }}
                            </p>
                        </div>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$vendor)
                        <a href="{{ route('admin::dbt.vendors.edit', $vendor->id) }}"
                           class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{ route('admin::dbt.vendors.index') }}" class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i>@lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
    </div>
@endsection
