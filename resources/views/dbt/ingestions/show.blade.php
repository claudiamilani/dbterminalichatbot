@extends('layouts.adminlte.template',['page_title' => trans('DBT/ingestions.title')])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend'=>true])
            @slot('title')
                @lang('DBT/ingestions.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="id"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion->id}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ingestion_source_id"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.ingestion_source_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion->source->name}}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.status')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion->statusLabel}}</p>
                        </div>
                    </div>
                    @if(isset($ingestion->message))
                        <div class="form-group">
                            <label for="message"
                                   class="col-md-3 control-label">@lang('DBT/ingestions.attributes.message')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$ingestion->message}}</p>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="notify_mails"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.notify_mails')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{is_array($ingestion->notify_mails) ? implode('; ',$ingestion->notify_mails) : ''}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="started_at"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.started_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{optional($ingestion->started_at)->format('d/m/Y H:i')}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ended_at"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.ended_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{optional($ingestion->ended_at)->format('d/m/Y H:i')}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="created_at"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion->CreatedAtInfo}} </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at"
                               class="col-md-3 control-label">@lang('DBT/ingestions.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$ingestion->UpdatedAtInfo}} </p>
                        </div>
                    </div>
                    <fieldset>
                        <legend>@lang('DBT/ingestions.attributes.options')</legend>
                        @foreach($ingestion->options as $key => $value)
                            <div class="form-group">
                                <label for=""
                                       class="col-md-3 control-label">@lang('DBT/ingestion_sources.options.'.$key)</label>
                                <div class="col-md-9">
                                    <p class="form-control-static"><i class="{{$value ? 'fas fa-fw fa-check text-success' : 'fas fw-fw fa-xmark text-danger' }}"></i> {{$value ? trans('common.yes') : trans('common.no') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </fieldset>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::dbt.ingestions.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    @can('update',$ingestion)
                        <a
                                href="{{ route('admin::dbt.ingestions.edit', paramsWithBackTo($ingestion->id, 'admin::dbt.ingestions.show',$ingestion->id)) }}"
                                class="btn btn-md btn-primary"
                                title="{{trans('DBT/ingestions.edit.title')}}">
                            <i class="fas fa-pen fa-fw"></i> @lang('common.form.edit')
                        </a>
                    @endcan
                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    <script>
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-orange pull-right',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    </script>
@endpush