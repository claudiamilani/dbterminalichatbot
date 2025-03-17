<div class="modal-header {{$header_classes ?? ''}}">
    <h4 class="modal-title">{{$title ?? 'Loading...'}}</h4>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 my-modal-content">
                {!!  $content ?? "<p class='text-center'><img alt='Loading' src='/images/ajax-loader.gif'></p>"!!}
            </div>
        </div>
    </div>
</div>
@if(isset($custom_required_legend) || isset($show_required_legend) || isset($footer_content))
    <div class="modal-footer mdl-modal">
        <div class="pull-left">
            @if(isset($custom_required_legend) && !empty($custom_required_legend))
                {!! $custom_required_legend !!}
            @else
                @if(isset($show_required_legend))
                    <span style="color:red">*</span> @lang('common.form.required_legend')
                @endif
            @endif
        </div>
        {!! $footer_content ?? '' !!}
    </div>
@endif