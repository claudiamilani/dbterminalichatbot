@extends('layouts.adminlte.template',['page_title' => trans('DBT/attr_categories.title')])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/attr_categories.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.published')</label>
                        <div class="col-md-9">
                            <p class="form-control-static"><i
                                        class="fas fa-fw {{$attr_category->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$attr_category->published ? trans('common.yes') : trans('common.no') }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attr_category->name}} </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.display_order')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attr_category->display_order}} </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.description')</label>
                        <div class="col-md-9">
                            {!! Form::textarea('description', $attr_category->description, ['class' => 'noResize form-control vertical height-md','disabled']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="created_at"
                               class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attr_category->CreatedAtInfo}} </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at"
                               class="col-md-3 control-label">@lang('DBT/attr_categories.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attr_category->UpdatedAtInfo}} </p>
                        </div>
                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backToSource('admin::dbt.attr_categories.index') }}"
                       class="btn btn-md btn-warning"><i class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')
                    </a>
                    @can('update',$attr_category)
                        <a href="{{ route('admin::dbt.attr_categories.edit', paramsWithBackTo($attr_category->id,'admin::dbt.attr_categories.show',$attr_category->id)) }}"
                                class="btn btn-md btn-primary"><i
                                    class="fas fa-pen fa-fw"></i> @lang('common.form.edit')</a>
                    @endcan
                </div>
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')

@endpush