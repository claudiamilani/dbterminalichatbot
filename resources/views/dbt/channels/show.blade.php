@extends('layouts.adminlte.template',['page_title' => trans('DBT/channels.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/channels.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/channels.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$channel->name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/channels.attributes.description')</label>
                        <div class="col-md-9">
                            {!! Form::textarea('description', $channel->description, ['class' => 'noResize form-control vertical height-md','disabled']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/channels.attributes.created_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$channel->created_at_info}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/channels.attributes.updated_by_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$channel->updated_at_info}}</p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$channel)<a
                            href="{{ route('admin::dbt.channels.edit',paramsWithBackTo([ $channel->id],'admin::dbt.channels.show', $channel->id)) }}"
                            class="btn btn-md btn-primary pull-right"><i class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{backToSource('admin::dbt.channels.index') }}"
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