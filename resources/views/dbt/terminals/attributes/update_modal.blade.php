@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/terminals.dbt_attributes.edit.title')
    @endslot
    @slot('content')
        <p>@lang('DBT/terminals.dbt_attributes.edit.confirm_msg',['attribute'=>$attribute->name ?? $attribute->description])</p>
        {!! Form::open(['route' => ['admin::dbt.terminals.updateAttribute', ['terminal_id'=>$terminal->id, 'attribute_id'=>$attribute->id]], 'method' => 'post', 'class' => 'form-horizontal', 'style' => 'padding: 15px 15px 0 15px', 'id'=>'formValue']) !!}
        {!! Form::hidden($attribute->id,null,['id'=>'attribute_value_field']) !!}
        <div class="form-group">
            <p><b>@lang('Terminale'): </b> {{$terminal->name}}</p>
            <p><b>@lang('Attributo'): </b> {{$attribute->name ?? $attribute->description}}</p>
            <p><b>@lang('Valore'): </b><span id="attribute_name"></span> </p>
        </div>
        <div class="btn-toolbar pull-right">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">@lang('common.form.back')</button>
            <a id="send_form"
               class="btn btn-sm btn-primary"><i
                        class=""></i> @lang('common.form.save')</a>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
@component('components.ajax', ['redirect_on_success'=>route('admin::dbt.terminals.edit',$terminal->id),'name' => 'saveValues', 'route' => route('admin::dbt.terminals.updateAttribute',[$terminal->id,$attribute->id]), 'form' => 'formValue', 'bind_to' => '#send_form'  ])
@endcomponent
<script>

     attribute = $("#{{'attribute_'.$attribute->id}}")
     attribute_value = attribute.val();
     checkboxes_values = [];

    if(attribute.hasClass('checkbox_'+{{$attribute->id}})){
        $('.checkbox_'+{{$attribute->id}}+':checked').each(function() {
            checkboxes_values.push($(this).val());
        });
        attribute_value = checkboxes_values;
    }
    $('#attribute_value_field').val(attribute_value)
    if(attribute_value === 0 || attribute_value.length === 0){
        $('#attribute_name').text('Falso')
    }else if(attribute_value === 1){
        $('#attribute_name').text('Vero')
    }else if(attribute_value === ''){
        $('#attribute_name').text('Vuoto')
    }else{
        $('#attribute_name').text(attribute_value)
    }
    console.log(attribute_value)
</script>