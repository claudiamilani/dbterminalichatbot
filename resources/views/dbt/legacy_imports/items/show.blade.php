@component('components.modal')
    @slot('header_classes')
        bg-primary
    @endslot
    @slot('title')
        @lang('DBT/legacy_import_items.show.title')
    @endslot
    @slot('content')
        <div class="form-group">
            <p><b>@lang('DBT/legacy_import_items.attributes.legacy_id'): </b> {{$item->legacy_id}}</p>
            <p><b>@lang('DBT/legacy_import_items.attributes.status')
                    : </b> @lang('DBT/legacy_imports.status.'.$item->status)</p>
            <p><b>@lang('DBT/legacy_import_items.attributes.result')
                    : </b> {{$item->result ? trans('DBT/legacy_imports.items.result.'.$item->result) : null}}</p>
            @if($item->message)
                <p><b>@lang('DBT/legacy_import_items.attributes.message'): </b> {{$item->message}}</p>
            @endif
            <p><b>@lang('DBT/legacy_import_items.attributes.created_at')
                    : </b> {{ optional($item->created_at)->format('d/m/Y H:i:s')}}</p>
            <p><b>@lang('DBT/legacy_import_items.attributes.updated_at')
                    : </b> {{ optional($item->updated_at)->format('d/m/Y H:i:s')}}</p>
        </div>
        <hr>
        <div class="form-group col-md-6">
            <p><b>Legacy</b></p>
            {!! Form::textarea('legacy_item', json_encode($legacy_item,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), [ 'id' => 'legacy_item', 'class' => 'form-control', 'readonly','style']) !!}
        </div>
        <div class="form-group col-md-6">
            <p><b>Local</b></p>
            {!! Form::textarea('local_item', json_encode($local_item,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), [ 'id' => 'local_item', 'class' => 'form-control', 'readonly','style']) !!}
        </div>
        <div class="btn-toolbar pull-right">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><i
                        class="fas fa-arrow-left fa-fw"></i> @lang('common.form.back')</button>
        </div>
        {!! Form::close() !!}
    @endslot
@endcomponent
