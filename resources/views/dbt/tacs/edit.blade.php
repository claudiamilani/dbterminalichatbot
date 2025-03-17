@extends('layouts.adminlte.template',['page_title' => trans('DBT/tacs.title'), 'fa_icon_class' => ''])

@section('content')
    <div class="row">
        {!! Form::model($tac, ['method' => 'patch', 'route' => ['admin::dbt.tacs.update',paramsWithBackTo($tac->id)],'class' => 'form-horizontal','size' => 12]) !!}
        @component('components.widget',['size' => 8])
            @slot('title')
                @lang('DBT/tacs.edit.title')
            @endslot
            @slot('body')
                <div class="form-group required">
                    <label for="terminal_id"
                           class="col-md-2 control-label"> @lang('DBT/tacs.attributes.terminal_id')</label>
                    <div class="col-md-10">
                        {!! Form::select('terminal_id', $terminal, $tac->terminal_id, [ 'id' => 'terminal_id', 'class' => 'form-control','required']) !!}
                    </div>
                </div>

                <div class="form-group required">
                    <label for="value"
                           class="col-md-2 control-label"> @lang('DBT/tacs.attributes.value')</label>
                    <div class="col-md-10">
                        {!! Form::text('value', null, ['id' => 'value', 'class' => 'form-control','required']) !!}
                    </div>
                </div>

                @if($tac->ingestion_id)
                    <div class="form-group">
                        <label class="col-md-2 control-label"> @lang('DBT/tacs.attributes.ingestion_id')</label>
                        <div class="col-md-10">
                            <p class="form-control-static">{{ $tac->ingestionSource->name }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"> @lang('DBT/tacs.attributes.ingestion_source_id')</label>
                        <div class="col-md-10">
                            <p class="form-control-static">{{ $tac->ingestion->id }}</p>
                        </div>
                    </div>
                @endif
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                    <a href="{{ backToSource('admin::dbt.tacs.index') }}"
                       class="btn btn-md btn-warning pull-right">
                        <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                </div>
            @endslot
        @endcomponent

        {!! Form::close() !!}
    </div>
@endsection

@push('scripts')
    @component('components.select2_script', ['name' => 'terminal_id', 'route' => route('admin::dbt.tacs.select2terminal')])
        @slot('format_selection')
            if(output.id !== '') {
            output = output.text
            return output;
            }
            return '';
        @endslot
        @slot('format_output')
            if(!output.loading) {
            output = output.text
            return output;
            }
            return output.text;
        @endslot
    @endcomponent
@endpush
