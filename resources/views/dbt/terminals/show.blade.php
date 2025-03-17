@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminals.title'), 'fa_icon_class' => ''])
@section('page_nav_default')
    @component('components.page_navigation')@endcomponent
@endsection
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/terminals.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.vendor_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->vendor->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.certified')</label>
                        <div class="col-md-9">
                            <p class="form-control-static"><i
                                        class="fas fa-fw {{$terminal->certified ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$terminal->certified ? trans('common.yes') : trans('common.no') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.published')</label>
                        <div class="col-md-9">
                            <p class="form-control-static"><i
                                        class="fas fa-fw {{$terminal->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$terminal->published ? trans('common.yes') : trans('common.no') }}
                        </div>
                    </div>
                    @if(optional($terminal->ingestion)->id)
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ingestion_source_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$terminal->ingestionSource->name}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ingestion_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$terminal->ingestion->id}}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ota_vendor')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->ota_vendor}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.ota_model')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->ota_model}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.created_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->created_at_info}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminals.attributes.updated_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$terminal->updated_at_info}} </p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$terminal)
                        <a
                                href="{{ route('admin::dbt.terminals.edit',paramsWithBackTo([ $terminal->id],'admin::dbt.terminals.show', $terminal->id)) }}"
                                class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{backToSource('admin::dbt.terminals.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
    </div>

    <div class="row">
        <div id="terminal_config">
            @include('dbt.terminals.terminal_configs.index')
        </div>
    </div>

    <div class="row">
        <div id="terminal_pictures">
            @include('dbt.terminals.terminal_pictures.index')
        </div>
    </div>
    <div class="row">
        <div id="tacs_list">
            @include('dbt.terminals.tacs.index')
        </div>
    </div>
    <div class="row">
        <div id="attributes_list">
            @include('dbt.terminals.attributes.index')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("[data-sort_reset]").contextmenu(function (e) {
            e.preventDefault();
            window.location.href = e.delegateTarget.dataset.sort_reset;
        });
    </script>
@endpush