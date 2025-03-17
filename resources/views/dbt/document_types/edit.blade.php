@extends('layouts.adminlte.template',['page_title' => trans('DBT/document_types.title'), 'fa_icon_class' => ''])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget', ['size' => 8])
            @slot('title')
                @lang('DBT/document_types.edit.title')
            @endslot
            @slot('body')
                {!! Form::model($documentType, ['method' => 'patch', 'route' => ['admin::dbt.document_types.update', $documentType->id],'class' => 'form-horizontal']) !!}
                <div class="form-group required">
                    <label for="name"
                           class="col-md-3 control-label"> @lang('DBT/document_types.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="channel_id"
                           class="col-md-3 control-label"> @lang('DBT/document_types.attributes.channel_id')</label>
                    <div class="col-md-9">
                        {!! Form::select('channel_id', $channel_id, $channel_id, ['id' => 'channel_id', 'class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                @slot('footer')
                    <div class="btn-toolbar">
                        <button type="submit" class="btn btn-md btn-primary pull-right"><i
                                    class="fas fa-save fa-fw"></i> @lang('common.form.save') </button>
                        <a href="{{ backToSource('admin::dbt.document_types.index') }}"
                           class="btn btn-md btn-warning pull-right"><i
                                    class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')</a>
                    </div>
                @endslot
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'channel_id', 'route' => route('admin::dbt.channels.select2')])
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