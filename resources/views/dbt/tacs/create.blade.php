@extends('layouts.adminlte.template',['page_title' => trans('DBT/tacs.title'), 'fa_icon_class' => ''])
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/tacs.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' =>['admin::dbt.tacs.store', paramsWithBackTo([],null,['#'.nav_fragment('DBT/tacs.title')])],'class' => 'form-horizontal', 'autocomplete' => 'off']) !!}
                <div class="form-group required">
                    <label for="terminal_id"
                           class="col-md-3 control-label">@lang('DBT/tacs.attributes.terminal_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('terminal_id',$terminal ? [$terminal->id => $terminal->name] : [] ,$terminal ? $terminal->id:null, ['id' => 'terminal_id','class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="value" class="col-md-3 control-label"> @lang('DBT/tacs.attributes.value')</label>
                    <div class="col-md-9">
                        {!! Form::text('value', null, ['id' => 'value', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar">
                    <button type="submit" class="btn btn-md btn-primary pull-right">
                        <i class="fas fa-save fa-fw"></i> @lang('common.form.save')
                    </button>
                    <a href="{{ backToSource('admin::dbt.tacs.index') }}" class="btn btn-md btn-warning pull-right">
                        <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                    </a>
                </div>
                {!! Form::close() !!}
            @endslot
        @endcomponent
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
