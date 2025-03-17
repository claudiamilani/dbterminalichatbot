@extends('layouts.adminlte.template',['page_title' => trans('DBT/attributes.title')])
@push('styles')
@endpush
@section('content')
    <div class="row">
        @component('components.widget',['size' => 8,'hide_required_legend' => true])
            @slot('title')
                @lang('DBT/attributes.show.title')
            @endslot
            @slot('body')
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.published')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                <i class="fas fa-fw {{$attribute->published ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i> {{$attribute->published ? trans('common.yes') : trans('common.no') }}
                            </p>
                        </div>
                    </div>

                    @if($attribute->ingestion_id)
                        <div class="form-group">
                            <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.ingestion_source_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{optional($attribute->ingestionSource)->name}} </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.ingestion_id')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{optional($attribute->ingestion)->id}} </p>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.name')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attribute->name}} </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.attr_category_id')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attribute->category->name}} </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.display_order')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attribute->display_order}} </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">@lang('DBT/attributes.attributes.description')</label>
                        <div class="col-md-9">
                            {!! Form::textarea('description', $attribute->description, ['class' => 'noResize form-control vertical height-md','disabled']) !!}
                        </div>
                    </div>
                    <fieldset>
                        <legend>@lang('DBT/attributes.attributes.type_options')</legend>
                        <div class="form-group">
                            <label for="type"
                                   class="col-md-3 control-label">@lang('DBT/attributes.attributes.type')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$attribute->AttributeTypeLabel}} </p>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type_options"
                                   class="col-md-3 control-label">@lang('DBT/attributes.attributes.input_type')</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$attribute->InputTypeLabel}} </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="required"
                                   class="col-md-3 control-label">@lang('DBT/attributes.type_options.required')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">
                                    <i class="fas fa-fw fa-xl {{$attribute->getRequiredOption() ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                </p>
                            </div>
                            <label for="multiple"
                                   class="col-md-3 control-label">@lang('DBT/attributes.type_options.multiple')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">
                                    <i class="fas fa-fw fa-xl {{$attribute->getMultipleOption() ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                </p>
                            </div>
                            <label for="searchable"
                                   class="col-md-3 control-label">@lang('DBT/attributes.type_options.searchable')</label>
                            <div class="col-md-1">
                                <p class="form-control-static">
                                    <i class="fas fa-fw fa-xl {{$attribute->getSearchableOption() ? 'fa-check text-success' : 'fa-xmark text-danger' }}"></i>
                                </p>
                            </div>
                        </div>

                        @if($attribute->type !== \App\DBT\Models\DbtAttribute::TYPE_BOOLEAN)
                            <div class="form-group">
                                <label for="type_options"
                                       class="col-md-3 control-label">@lang('DBT/attributes.attributes.type_options')</label>
                                <div class="col-md-9">
                                    {!! Form::select('options[]', $attribute->getOptions()   ,$attribute->getOptions(), [ 'id' => 'options','class' => 'form-control', 'multiple', 'disabled']) !!}
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="default_value"
                                       class="col-md-3 control-label">@lang('DBT/attributes.attributes.default_value')</label>
                                <div class="col-md-9">
                                    {!! Form::select('default_value[]',$attribute->getDefaultValueOptions(),$attribute->getDefaultValue(), ['id' => 'default_value', 'class' => 'form-control height-sm noResize', 'multiple', 'disabled']) !!}
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="default_value"
                                       class="col-md-3 control-label">@lang('DBT/attributes.attributes.default_value')</label>
                                <div class="col-md-9">
                                    <p class="form-control-static"> {{$attribute->getDefaultValue() == '1' ? 'True' : 'False'}}</p>
                                </div>
                            </div>
                        @endif


                    </fieldset>

                    <div class="form-group">
                        <label for="created_at"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.created_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attribute->CreatedAtInfo}} </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="updated_at"
                               class="col-md-3 control-label">@lang('DBT/attributes.attributes.updated_at')</label>
                        <div class="col-md-9">
                            <p class="form-control-static">{{$attribute->UpdatedAtInfo}} </p>
                        </div>
                    </div>

                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backToSource('admin::dbt.attributes.index') }}"
                       class="btn btn-md btn-warning"> <i
                                class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</a>
                    @can('update',$attribute)
                        <a
                                href="{{ route('admin::dbt.attributes.edit', paramsWithBackTo($attribute->id,'admin::dbt.attributes.show',$attribute->id )) }}"
                                class="btn btn-md btn-primary"> <i
                                    class="fas fa-pen fa-fw"></i> @lang('common.form.edit')</a>
                    @endcan
                </div>
            @endslot
        @endcomponent
    </div>

@endsection
@push('scripts')
    @component('components.select2_script', ['name' => 'options[]', 'route' => route('admin::dbt.attributes.select2TypeOptions'), 'tags'=>true])
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
    @component('components.select2_script', ['name' => 'default_value[]', 'route' => route('admin::dbt.attributes.select2TypeOptions'), 'tags'=>true])
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