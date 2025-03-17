<div class="table-responsive">
    <table @if(!empty($element_id)) id="{{$element_id}}" @endif class="table @if(empty($no_table_style))table-striped @endif">
        @if(!empty($head))
            <thead>{{$head}}</thead>@endif
        @if(!empty($body))
            <tbody>{{$body}}</tbody>@endif
        @if(!empty($footer))
            <tfoot>{{$footer}}</tfoot>@endif
    </table>
    {!! $paginator ?? '' !!}
</div>
