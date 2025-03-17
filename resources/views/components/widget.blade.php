@php $sort_key = $sort_key ?? 'sort' @endphp
<div class='col-md-{{$size ?? '6'}}'>
    <!-- Box -->
    <div class="box box-{{$header_class ?? 'primary'}} @if(!empty($collapsed)) collapsed-box @endif">
        <div class="box-header with-border">
            <h3 class="box-title @if(!isset($title) && !isset($withAnchor)) hide @endif">
                @isset($withAnchor)
                    <a id="_{{ nav_fragment($withAnchor) }}" class="mdl-page-anchor"
                       data-anchor-title="{{ (isset($title) && !empty($title))?$title:$withAnchor }}"></a>
                @endisset
                {{$title ?? ''}}</h3>
            @isset($extraToolbar)
                <div class="mdl-extra-toolbar">
                    {!! $extraToolbar !!}
                </div>
            @endisset
            <div class="box-tools pull-right">
                @if($collapsible ?? false)
                    <button class="btn btn-box-tool {{$collapsible ?? 'hide'}}" data-widget="collapse"
                            data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa @if(empty($collapsed))fa-minus @else fa-plus @endif"></i></button>
                @endif
                @if($removable ?? false)
                    <button class="btn btn-box-tool {{$removable ?? 'hide'}}" data-widget="remove" data-toggle="tooltip"
                            title="Remove"><i
                                class="fa fa-times"></i></button>
                @endif
            </div>
            <div class="btn-toolbar pull-right clear @if(isset($searchbox)) search-toolbar @endif ">
                @if(isset($searchbox))
                    {!! Form::open(['route' => array_merge($searchbox,(isset($withAnchor)?['#'.$withAnchor]:[])), 'class' => 'form-horizontal', 'style', 'method' => 'get']) !!}
                    @foreach(pageSortSearchParams() as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ request($k) }}">
                    @endforeach
                    <div class="btn-toolbar vertical-margin-sm">
                        <div class="input-group input-group-sm" style="max-width:250px;">
                            {!! Form::text((isset($searchbox_name) && !empty($searchbox_name)?$searchbox_name:'search'), request((isset($searchbox_name) && !empty($searchbox_name)?$searchbox_name:'search')), [ 'class' => 'form-control pull-right', 'placeholder' => trans('common.search.placeholder')]) !!}
                            <div class="input-group-btn">
                                {!! Form::button('', ['type' => 'submit','class' => 'btn btn-default fa fa-search']) !!}
                            </div>
                        </div>
                        {!! $advancedSearchBox ?? '' !!}
                    </div>
                    {!! Form::close() !!}
                @endif

                    @if(isset($sortable) && ((isset($sort_key) && !empty(request($sort_key))) || (isset($sort_key) && !empty(request('sort'))) || (!isset($sort_key) && !empty(request('sort'))) ))
                    <div id="filter-reset" class="box-tools pull-left"
                         style="margin-top:10px; margin-left:5px; font-size:12px">
                        <a class="glyphicon glyphicon-refresh">{{sort_reset_button($sort_key ?? 'sort')}}</a>
                    </div>
                @endif
            </div>
        </div>
        @if(!empty($body))
            <div class="box-body">
                {{$body ?? 'Empty content'}}
            </div><!-- /.box-body -->
        @endif
        @if(!empty($footer))
            <div class="box-footer">
                @if(isset($custom_required_legend) && !empty($custom_required_legend))
                    <div class="pull-left">
                        {!! $custom_required_legend !!}
                    </div>
                @else
                    @if(!isset($hide_required_legend) || $hide_required_legend != true)
                        <p><span style="color:red">*</span> @lang('common.form.required_legend')</p>
                    @endif
                @endif
                {!! $footer ?? ''!!}
            </div><!-- /.box-footer-->
        @endif
    </div><!-- /.box -->
</div><!-- /.col -->
@if(!isset($submitOnChange) || $submitOnChange !== false)
    @push('scripts')
        <script>
            $('.search-toolbar select').not('.noSubmit').change(function () {
                $(this).parents('form').submit();
            });
        </script>
    @endpush
@endif
@if(isset($sortable) && $sortable)
    @push('scripts')
        @once
            <script>
                $("[data-sort_reset]").contextmenu(function (e) {
                    e.preventDefault();
                    window.location.href = e.delegateTarget.dataset.sort_reset;
                });
            </script>
        @endonce
    @endPush
@endif