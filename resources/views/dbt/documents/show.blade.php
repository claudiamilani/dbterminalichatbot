@extends('layouts.adminlte.template',['page_title' => trans('DBT/documents.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/documents.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="file_path"
                               class="col-md-3 control-label"> @lang('DBT/documents.attributes.file_path')</label>
                        <div class="col-md-9">
                            <p class="form-control-static"><a
                                        href="{{ Storage::disk('documents')->url($document->file_path)}}"
                                        target="_blank">{{ $document->title }} <sup><i
                                                class="fas fa-fw fa-arrow-up-right-from-square"></i></sup></a></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="document_type_id"
                               class="col-md-3 control-label"> @lang('DBT/documents.attributes.document_type_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document->documentType->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file_mime_type"
                               class="col-md-3 control-label"> @lang('DBT/documents.attributes.file_mime_type')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{optional($document)->file_mime_type}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/documents.attributes.created_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document->created_at_info}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/documents.attributes.updated_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document->updated_at_info}}</p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', $document)
                        <a
                                href="{{ route('admin::dbt.documents.edit',paramsWithBackTo([$document->id],'admin::dbt.documents.show', $document->id)) }}"
                                class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{backToSource('admin::dbt.documents.index') }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
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