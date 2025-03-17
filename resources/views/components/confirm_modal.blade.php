<div class="modal-header {{$header_classes ?? ''}}">
    <h4 class="modal-title">{{$title ?? 'MyModal title'}}</h4>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 my-modal-message">
                {{$message ?? 'MyModal message'}}
            </div>
            <div class="col-md-12">
                @if(isset($action))
                    {!! Form::open(['route' => $action, 'class' => 'form-horizontal', 'style', 'method' => 'get']) !!}
                    <div class="btn-toolbar">
                        {{$footer ?? ''}}
                    </div>

                    {!! Form::close() !!}
                @else
                    <div class="btn-toolbar">
                        {{$footer ?? ''}}
                    </div>

                @endif
            </div>
        </div>
    </div>
</div>

@stack('modal_scripts')