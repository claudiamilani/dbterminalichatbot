@extends('layouts.adminlte.template',['page_title' => trans('DBT/terminal_configs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/terminal_configs.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.published')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                <i class="fas fa-fw {{ $terminal_config->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                {{ $terminal_config->published ? 'Sì' : 'No' }}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.ota')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{ $terminal_config->ota->name }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.document')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {!! optional($terminal_config->document)->file_path
                                    ? '<a href="' . Storage::disk('documents')->url(optional($terminal_config->document)->file_path) . '" target="_blank">'
                                        . optional($terminal_config->document)->title . ' <sup><i class="fas fa-fw fa-arrow-up-right-from-square"></i></sup>'
                                    . '</a>'
                                    : '' !!}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $terminal_config->createdAtInfo }}
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"> @lang('DBT/terminal_configs.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                {{ $terminal_config->updatedAtInfo }}
                            </p>
                        </div>
                    </div>
                </div>
            @endslot

            @slot('footer')
                <div class="btn-toolbar">
                    <a
                            href="{{  route('admin::dbt.terminals.configs.edit', ['terminal_id' => $terminal_config->terminal->id, 'config_id' => $terminal_config->id]) }}"
                            class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-fw fa-pen"></i> @lang('common.form.edit')</a>

                    <a href="{{ backToSource('admin::dbt.terminals.show', $terminal_id) }}"
                       class="btn btn-md btn-warning pull-right"><i
                                class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent
    </div>
@endsection
