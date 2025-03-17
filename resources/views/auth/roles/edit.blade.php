@extends('layouts.adminlte.template',['page_title' => trans('roles.edit.title')])
@section('content')
    <div class="row">
        {!! Form::model($role,['method' => 'patch', 'route' => ['admin::roles.update', $role->id],'class' => 'form-horizontal', 'style']) !!}
        @component('components.widget', ['size' => 12])
            @slot('title')@lang('roles.edit.title') @if($role->users_count > 0)  <br><small class=""><i class="text-warning fas fa fa-warning"></i> @lang('roles.edit.in_use')</small>@endif @endslot
            @slot('body')
                <div class="form-group required">
                    <label for="name"
                           class="col-md-2 control-label">@lang('roles.attributes.name')</label>
                    <div class="col-md-10">
                        {!! Form::text('name', null, [ 'id' => 'name', 'class' => 'form-control', 'required', ($role->users_count)?'readonly': null]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="col-md-2 control-label">@lang('roles.attributes.description')</label>
                    <div class="col-md-10">
                        {!! Form::textarea('description', null, [ 'id' => 'description','class' => 'form-control vertical height-sm', ($role->users_count)?'readonly': null]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-md-2 control-label">@lang('roles.attributes.permissions')</label>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::select('types[]', $types_select, null,  ['class' => 'form-control', 'id' => 'types_select']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-title">
                                        {!! Form::label('check_all', trans('permissions.attributes.check_all')) !!} {!! Form::checkbox('select_all', 0, 0, ['class' => 'pull-right check_all']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="role">
                            @foreach ($types as $type)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-title" id="type{{$type->id}}">
                                            {{$type->name}} {!! Form::checkbox('select_all', 0, 0, ['class' => 'pull-right check_all_card', 'data-list' => $type->id ]) !!}
                                        </div>
                                        <ul class="list-group list-group-flush" id="list{{$type->id}}">
                                            @foreach($type->permissions as $permission)
                                                <li class="list-group-item">{!! Form::label('check', trans('permissions.attributes.'.$permission->label)) !!} {!! Form::checkbox('permissions[]', $permission->id, $permission->roles->contains($role->id) ? 1 : 0, ['class' => 'pull-right'] ) !!}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @if (!($loop->iteration%2))
                                    <div class="clearfix"></div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                    <a href="{{ backtoSource('admin::roles.index') }}">
                        <button type="button" class="btn btn-md btn-secondary">
                            <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                        </button>
                    </a>
                    <button type="submit" class="btn btn-md btn-primary control-btn"><i
                                class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
                </div>
            @endslot
        @endcomponent
        {!! Form::close() !!}
    </div>
    @push('scripts')
        <script>
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-orange pull-right',
                radioClass: 'iradio_square-orange',
                increaseArea: '20%' // optional
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.check_all').on('ifClicked', function() {
                    if (this.checked) {
                        $('#role').find(':checkbox').each(function() {
                            $(this).iCheck('uncheck'); // deselect all
                        });
                    } else {
                        $('#role').find(':checkbox').each(function() {
                            $(this).iCheck('check'); // select all
                        });
                    }
                });

                $('.check_all_card').on('ifClicked', function() {
                    targetList = $(this).data('list');
                    if (this.checked) {
                        $('#role').find('#list'+targetList).find(':checkbox').each(function() {
                            $(this).iCheck('uncheck'); // deselect all
                        });
                    } else {
                        $('#role').find('#list'+targetList).find(':checkbox').each(function() {
                            $(this).iCheck('check'); // select all
                        });
                    }
                });
            });

            $(function () {
                // Anchor to permission type when selected from dropdown
                $("#types_select").change(function () {
                    const typeSelected = $("#types_select option:selected").val();
                    $('html, body').animate({scrollTop: $('#type' + typeSelected).offset().top}, "slow");
                });
            });
        </script>
    @endpush
@endsection