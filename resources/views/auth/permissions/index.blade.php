@extends('layouts.adminlte.template',['page_title' => trans('permissions.title')])
@section('content')
    <div class="row">
        {!! Form::open(['route' => ['admin::permissions.update']]) !!}
        @component('components.widget', ['title' => trans('permissions.index.title'), 'size' => 12])
            @slot('body')
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            {!! Form::select('roles[]', $roles_select, null, ['class' => 'form-control', 'id' => 'roles_select']) !!}
                        </div>
                        <div class="col-md-8">
                            {!! Form::select('types[]', $types_select, null,  ['class' => 'form-control', 'id' => 'types_select']) !!}
                        </div>
                    </div>
                </div>
                @foreach ($roles as $role)
                    <div class="row {!! $loop->first ? 'show' : 'hide'  !!} check_all{{$role->id}}">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-title">
                                {!! Form::label('check_all', trans('permissions.attributes.check_all')) !!} {!! Form::checkbox('select_all', 0, 0, ['class' => 'pull-right check_all']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row {!! $loop->first ? 'show' : 'hide'  !!}" id="role{{$role->id}}">
                        @foreach ($types as $type)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-title" id="type{{$type->id}}">
                                        {{$type->name}} {!! Form::checkbox('select_all', 0, 0, ['class' => 'pull-right check_all_card', 'data-list' => $type->id]) !!}
                                    </div>
                                    <ul class="list-group list-group-flush" id="list{{$type->id}}">
                                        @foreach ($type->permissions as $permission)
                                            <li class="list-group-item">{!! Form::label('check',trans('permissions.attributes.'.$permission->label)) !!} {!! Form::checkbox($role->id.'[]', $permission->id, $permission->roles->contains($role->id) ? 1 : 0 , ['class' => 'pull-right permission']) !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @if(!($loop->iteration%2))
                                <div class="clearfix"></div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @endslot
            @slot('footer')
                <div class="btn-toolbar pull-right">
                        <a href="{{backToSource('admin::dashboard')}}">
                            <button type="button" class="btn btn-md btn-secondary">
                                <i class="fas fa-fw fa-arrow-left"></i> @lang('common.form.back')
                            </button>
                        </a>
                    @can('managePermissions','App\Auth\Role')
                        <button type="submit" class="btn btn-md btn-primary control-btn"><i
                                    class="fas fa-save fa-fw"></i> @lang('common.form.save')</button>
                    @endcan
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
                let targetRole = '';

                $('.check_all').on('ifClicked', function() {
                    targetRole = $('#roles_select').val();;
                    if (this.checked) {
                        $('#role'+targetRole).find(':checkbox').each(function() {
                            $(this).iCheck('uncheck'); // deselect all
                        });
                    } else {
                        $('#role'+targetRole).find(':checkbox').each(function() {
                            $(this).iCheck('check'); // select all
                        });
                    }
                });

                $('.check_all_card').on('ifClicked', function() {
                    targetRole = $('#roles_select').val();
                    targetList = $(this).data('list');
                    if (this.checked) {
                        $('#role'+targetRole).find('#list'+targetList).find(':checkbox').each(function() {
                            $(this).iCheck('uncheck'); // deselect all
                        });
                    } else {
                        $('#role'+targetRole).find('#list'+targetList).find(':checkbox').each(function() {
                            $(this).iCheck('check'); // select all
                        });
                    }
                });
            });

            $(function () {

                // Show and hide roles permissions when selected from dropdown
                const roles = [];
                $("#roles_select option").each(function () {
                    roles.push(this.value);
                });

                $("#roles_select").change(function () {
                    let roleSelected = $('#roles_select').val();

                    for (let i = 0; i < roles.length; i++) {
                        const role = roles[i];
                        $('#role' + role).removeClass('show').addClass('hide');
                        $('.check_all' + role).removeClass('show').addClass('hide');
                    }

                    $('#role' + roleSelected).removeClass('hide').addClass('show');
                    $('.check_all' + roleSelected).removeClass('hide').addClass('show');
                });

                // Anchor to permission type when selected from dropdown
                $("#types_select").change(function () {
                    const typeSelected = $("#types_select option:selected").val();
                    const roleSelected = $('#roles_select').val();
                    $('html, body').animate({scrollTop: $('#role' +roleSelected).find('#type' + typeSelected).offset().top}, "slow");
                });
            });
        </script>
    @endpush
@endsection