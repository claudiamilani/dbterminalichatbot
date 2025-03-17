@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/terminals.dbt_attributes.delete.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/terminals.dbt_attributes.edit.confirm_msg',['attribute'=>$attribute->name ?? $attribute->description])</p>
        {!! Form::open(['route' => ['admin::dbt.terminals.forceDeleteAttribute', ['terminal_id'=>$terminal->id, 'attribute_id'=>$attribute->id]], 'method' => 'post', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px', 'id'=>'formValue']) !!}
        <div class="form-group">
            <p><b>@lang('Terminale'): </b> {{$terminal->name}}</p>
            <p><b>@lang('Attributo'): </b> {{$attribute->name ?? $attribute->description}}</p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">@lang('common.form.back')</button>
            <button type="submit" class="btn btn-sm btn-danger">
                @lang('common.form.delete')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
