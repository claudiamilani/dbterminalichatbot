@extends('layouts.adminlte.template',['page_title' => trans('DBT/ingestions.title')])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8, 'hide_required_legend'=>true])
            @slot('title')
                @lang('DBT/ingestions.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.ingestions.store','class' => 'form-horizontal', 'files'=>true]) !!}
                <div class="form-group required">
                    <label for="ingestion_source_id"
                           class="col-md-3 control-label">@lang('DBT/ingestions.attributes.ingestion_source_id')</label>
                    <div class="col-md-6">
                        {!! Form::select('ingestion_source_id', $sources, null,  ['class' => 'form-control', 'id' => 'ingestion_source_id', 'required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="file_path"
                           class="col-md-3 control-label">@lang('DBT/ingestions.attributes.file_path')</label>
                    <div class="col-md-6">
                        {!! Form::file('file_path',  ['class' => 'form-control', 'id' => 'file_path', 'required']) !!}
                    </div>
                    <div class="col-md-12 col-md-offset-3">
                        <small><i class="fa fa-info-circle text-info"></i> @lang('DBT/ingestions.placeholder_hints.file_types')
                        </small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notify_mails"
                           class="col-md-3 control-label">@lang('DBT/ingestions.attributes.notify_mails')</label>
                    <div class="col-md-9">
                        {!! Form::select('notify_mails[]',[],  null,['class' => 'form-control', 'id' => 'notify_mails', 'multiple']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-md-3 control-label">@lang('DBT/ingestions.attributes.status')</label>
                    <div class="col-md-8">
                        <div class="pull-left">
                            {!! Form::select('status', $status, 0, ['id' => 'status', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <fieldset>
                    <legend>@lang('DBT/ingestions.attributes.options')</legend>
                    @foreach($default_options as $key => $value)
                        <div class="form-group">
                            <label for=""
                                   class="col-md-3 control-label">@lang('DBT/ingestion_sources.options.'.$key)</label>
                            <div class="col-md-8">
                                <div class="pull-left">
                                    <input type="hidden" name="{{$key}}" value="0">
                                    {!! Form::checkbox($key, 1, $value, ['id' => $key, 'class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </fieldset>

            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::dbt.ingestions.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class=" btn btn-md btn-primary"><i
                                class="fas fa-fw fa-save"></i> @lang('common.form.save')
                    </button>
                </div>
                {!! Form::close() !!}
            @endslot
        @endcomponent
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name'=>'notify_mails[]','route'=>route('admin::dbt.ingestions.listUserMails'), 'tags'=>true, 'inputTooShort'=>trans('DBT/ingestions.notify_mails_placeholder'), 'search'=>''])
        @slot('format_selection')
            if(output.id !== '') {
            output = output.text
            return output;
            }
            return '';
        @endslot
        @slot('format_output')
            if(!output.loading) {
            if(!output.existing) {
            output =  output.text
            return output;
            }
            output = output.text
            return output;
            }
            return output.text;
        @endslot
    @endcomponent

    <script>
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-orange pull-right',
            radioClass: 'iradio_square-orange',
            increaseArea: '20%' // optional
        });
    </script>
    <script>

        let source = $('#ingestion_source_id');

        source.on('change', function () {
            $.ajax({
                url: '{{ route('admin::dbt.ingestions.loadOptions')}}',
                data: {'id': source.val()},
                type: 'GET',
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    $.each(response, function (key, value) {

                        if ($('#' + key).length) {
                            console.log(value == true);

                            if (value == true) {

                                $('#' + key).iCheck('check');
                            } else {
                                $('#' + key).iCheck('uncheck');
                            }
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Errore nella chiamata AJAX:', error);
                }
            });
        })
    </script>

@endpush