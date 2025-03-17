@extends('layouts.adminlte.template',['page_title' => trans('DBT/otas.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/otas.show.title')
            @endslot

            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $ota->name }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.type')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $ota->type }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.sub_type')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $ota->sub_type }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.ext_0')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $ota->ext_0 }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.ext_number')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $ota->ext_number }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{  $ota->createdAtInfo }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/otas.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $ota->updatedAtInfo }}
                            </p>
                        </div>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    @can('update',$ota)
                        <a href="{{ route('admin::dbt.otas.edit', $ota->id) }}"
                           class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>
                    @endcan
                    <a href="{{ route('admin::dbt.otas.index') }}" class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i>@lang('common.form.back')</a>
                </div>
            @endslot

        @endcomponent
    </div>
@endsection
