<div class="col-md-6 form-group {{$attribute->getRequiredOption()}}">
    <label for="{{$attribute->name}}" class="col-md-3 control-label"> {{$attribute->name  }} </label>
    @switch($attribute->getInputTypeOption())
        @case('TEXT')
            <div class="col-md-8">
                {!! Form::text($attribute->name, !$attribute->attributeValues->isEmpty() ? $attribute->attributeValues->first()->value : null, ['class' => 'form-control', $attribute->getRequiredOption()]) !!}
            </div>
            @break
        @case('TEXTAREA')
            <div class="col-md-8">
                {!! Form::textarea($attribute->name, !$attribute->attributeValues->isEmpty() ? $attribute->attributeValues->first()->value : null, ['class' => 'form-control', $attribute->getRequiredOption()]) !!}
            </div>
            @break
        @case('CHECKBOX')
            <div class="col-md-8">
                {!! Form::checkbox($attribute->name, 1,$attribute->attributeValues->first()  ? 1 : 0 , ['class' => 'form-control', $attribute->getRequiredOption()]) !!}
            </div>
            @break
        @case('SELECT')
            <div class="col-md-8">
                {!! Form::select($attribute->name,  $attribute->getOptions(),  !$attribute->attributeValues->isEmpty() ? json_decode($attribute->attributeValues->first()->value) : null,['class' => 'form-control', $attribute->getRequiredOption(), $attribute->getMultipleOption()]) !!}
            </div>
            @break
        @case('NUMBER')
            <div class="col-md-8">
                {!! Form::number($attribute->name, !$attribute->attributeValues->isEmpty() ?$attribute->attributeValues->first()->value: null,['class' => 'form-control', $attribute->getRequiredOption(), $attribute->getDecimals() ] ) !!}
            </div>
            @break
        @default
            <div class="col-md-2">
                {!! Form::text($attribute->name, !$attribute->attributeValues->isEmpty() ? $attribute->attributeValues->first()->value : null, ['class' => 'form-control']) !!}
            </div>
            @break
    @endswitch
    @if($attribute->getSearchableOption() != 0 ||  $attribute->getMultipleOption() != 0)
        @push('scripts')
            @component('components.select2_script', ['name'=>$attribute->name,'route'=>route('admin::dbt.attributes.select2SearchableAttributes')])
                @if($attribute->getSearchableOption())
                    @slot('dataParams')
                        attribute_id: '{{$attribute->id}}'
                    @endslot
                @endif
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
    @endif
</div>
