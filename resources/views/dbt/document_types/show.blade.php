@extends('layouts.adminlte.template',['page_title' => trans('DBT/document_types.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/document_types.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/document_types.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document_type->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/document_types.attributes.channel_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document_type->channel->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/document_types.attributes.created_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document_type->created_at_info}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/document_types.attributes.updated_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$document_type->updated_at_info}}</p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update', $document_type)<a
                            href="{{ route('admin::dbt.document_types.edit',paramsWithBackTo([$document_type->id],'admin::dbt.document_types.show', $document_type->id)) }}"
                            class="btn btn-md btn-primary pull-right"><i class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{backToSource('admin::dbt.document_types.index') }}"
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