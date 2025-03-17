<div class="{{ $size ?? 'col-lg-3 col-xs-6'}}">
    <div class="small-box {{ $bg_classes ?? 'bg-aqua'}}">
        <div class="inner">
            <h3>{{ $value }}</h3>

            <p>{{ $title }}</p>
        </div>
        <div class="icon">
            <i class="{{ $icon_classes ?? 'ion ion-bag' }}"></i>
        </div>
        <a href="{{ $url ?? '#' }}" class="small-box-footer" title="{{ $link_title ?? '' }}">
            @if(empty($link_label))
                @lang('common.details')
            @else
                {{$link_label}}
            @endif <i class="fa fa-arrow-circle-right"></i></a>
    </div>
</div>