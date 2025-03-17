<p>@if($contents->total() == 0) @lang('common.data_not_available') @else @lang('common.pagination_footer',['recordNum' => ($contents->perPage() > $contents->total()) ? $contents->total() : $contents->count(),'total' => $contents->total()]) @endif</p>
{{ $contents->appends(array_merge(pageSortSearchParams()->toArray(),(isset($searchFilters))?$searchFilters:[]))->links() }}
