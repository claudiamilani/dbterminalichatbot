@extends('layouts.adminlte.template',['page_title' => trans('DBT/tacs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/tacs.show.title')
            @endslot

            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.terminal_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $tac->terminal->name }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.value')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $tac->value }}</p>
                        </div>
                    </div>

                    @if($tac->ingestion_id)
                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.ingestion_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $tac->ingestionSource->name }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.ingestion_source_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{ $tac->ingestion->id }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $tac->createdAtInfo }}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/tacs.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $tac->updatedAtInfo }}
                            </p>
                        </div>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$tac)
                        <a href="{{ route('admin::dbt.tacs.edit', $tac->id) }}"
                           class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{ backToSource('admin::dbt.tacs.index') }}" class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i>@lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
    </div>
@endsection
