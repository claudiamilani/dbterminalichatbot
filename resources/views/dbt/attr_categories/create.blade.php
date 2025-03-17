@extends('layouts.adminlte.template',['page_title' => trans('DBT/attr_categories.title')])
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8])
            @slot('title')
                @lang('DBT/attr_categories.create.title')
            @endslot
            @slot('body')
                {!! Form::open(['route' => 'admin::dbt.attr_categories.store','class' => 'form-horizontal']) !!}
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="published" value="0">
                        {!! Form::checkbox('published', 1, 1, [ 'id' => 'published','class' => 'form-control', 'data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('common.published'), 'data-off' => trans('common.unpublished'), 'data-onstyle' => 'success pull-right', 'data-offstyle' => 'danger pull-right', 'data-style' => 'android mdl-large']) !!}
                    </div>
                </div>
                <div class="form-group required">
                    <label for="name"
                           class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.name')</label>
                    <div class="col-md-9">
                        {!! Form::text('name', null, [ 'id' => 'name','class' => 'form-control','required']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <label for="display_order"
                           class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.display_order')</label>
                    <div class="col-md-9">
                        {!! Form::number('display_order',0, ['id' => 'display_order', 'class' => 'form-control', 'min'=>0]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.description')</label>
                    <div class="col-md-9">
                        {!! Form::textarea('description',null, ['id' => 'description', 'class' => 'form-control height-md noResize ']) !!}
                    </div>
                </div>

            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::dbt.attr_categories.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class="btn btn-md btn-primary">
                        <i class="fas fa-save fa-fw"></i>@lang('common.form.save')
                    </button>
                </div>
            @endslot
            {!! Form::close() !!}
        @endcomponent
    </div>
@endsection
@push('scripts')

@endpush