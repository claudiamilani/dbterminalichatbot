@extends('layouts.adminlte.template',['page_title' => trans('DBT/legacy_imports.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 12, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/legacy_imports.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$import->id}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.type')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{trans('DBT/legacy_imports.types.'.$import->type)}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.update_existing')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $import->update_existing ? trans('common.yes') : trans('common.no') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.requested_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($import->requestedBy)->fullname }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($import->created_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ optional($import->updated_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.status')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">@lang('DBT/legacy_imports.status.'.$import->status)</p>
                        </div>
                    </div>
                    @if($import->status == \App\DBT\Models\LegacyImport::STATUS_ERROR)
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/legacy_imports.attributes.message')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$import->message}}</p>
                            </div>
                        </div>
                    @endif
                    @component('components.table-list')
                        @slot('head')
                            <tr>
                                <td colspan="4" class="text-center"><h2>RISULTATI</h2></td>
                            </tr>
                            <tr>
                                <td>@lang('DBT/legacy_imports.attributes.created_cnt')</td>
                                <td>@lang('DBT/legacy_imports.attributes.updated_cnt')</td>
                                <td>@lang('DBT/legacy_imports.attributes.skipped_cnt')</td>
                                <td>@lang('DBT/legacy_imports.attributes.error_cnt')</td>
                            </tr>
                        @endslot
                        @slot('body')
                            <tr>
                                <td>{{ $import->created_items_count }}</td>
                                <td>{{ $import->updated_items_count }}</td>
                                <td>{{ $import->skipped_items_count }}</td>
                                <td>{{ $import->error_items_count }}</td>
                            </tr>
                        @endslot
                    @endcomponent
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <a href="{{backToSource('admin::dbt.legacy_imports.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
    </div>
    <div class="row">
        @include('dbt.legacy_imports.items.index')
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