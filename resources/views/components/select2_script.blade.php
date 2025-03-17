<script>
    $("{!! $mySelector = ($selector ?? "select[name='".$name."']") !!}").select2({
        language: {
            inputTooShort: function () {
                return '@if(!empty($inputTooShort)) {{ trans($inputTooShort) }} @else {{ trans('common.min_required_chars',['charNum' => (!empty($minChars))?$minChars:3]) }}@endif';
            },
            searching: function (params) {
                @if(isset($search) && $search !== true)
                    return '...';
                @else
                if (params.term !== undefined) {
                    return '@lang('common.searching') ' + params.term;
                }
                return '@lang('common.searching')';
                @endif
            },
            noResults: function(){
                return '@lang('common.search.no_results')';
            }

        },
        theme: 'bootstrap',
        width: 'resolve',
        allowClear: true,
        // placeholder doesn't work with tags enabled
        placeholder: '{{ $placeholder ?? '' }}',
        @if(!empty($action) && !empty($class))
                @can($action,$class)
        tags: @if(!empty($tags) && $tags === true) {{ 'true' }}@else {{ 'false' }}@endif,
        @endcan
                @else
        tags: @if(!empty($tags) && $tags === true) {{ 'true' }}@else {{ 'false' }}@endif,
        @endif
                @if(!empty($tags) && $tags === true)
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            if (Array.isArray($("{!! $mySelector !!}").select2('data')) && $("{!! $mySelector !!}").select2('data').length === 0) {
                console.log('was empty, adding empty option');
                var newOption = new Option('', '');
                $("{!! $mySelector !!}").append(newOption).trigger('change');
            }
            return {
                id: term,
                text: term,
            }
        },
        @endif

        ajax: {
            url: "{{ $route }}",
            dataType: 'json',
            delay: 500,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    @if(isset($dataParams))
                    {!! $dataParams !!}
                    @endif
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                @if(isset($debug) && $debug)
                console.log(data);
                @endif
                    return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: {{ $minChars ?? 3 }},
        templateResult: formatOutput{{ $uid = $uid ?? '_'.str_random(5) }},
        @if(!empty($format_selection))
        templateSelection: formatSelection{{ $uid }},
        @endif
    });

    @if(!empty($format_output))function formatOutput{{ $uid }}(output) {
        {!! $format_output !!}
    }
    @else
    function formatOutput{{$uid}}(output) {
        if (!output.existing) {
            return '<b>@lang('common.new'):</b> ' + output.text;
        }
        return output.text;
    }

    @endif
    @if(!empty($format_selection))function formatSelection{{ $uid }}(output) {
        {!! $format_selection !!}
    }

    @endif
    @if(!empty($linkedClear) || (!empty($tags) && $tags === true))
    $("{!! $mySelector !!}").on('select2:clear', function (e) {
        @if(!empty($linkedClear))
        $('{!! $linkedClear !!}').val(null).trigger('change');
        var callback = {{ $callback ?? 'null' }}
        @endif
        @if(!empty($tags) && $tags === true)
        $("{!! $mySelector !!} option").each(function() {
            $(this).remove();
        });
        e.preventDefault();
        @endif
    });
    @endif
</script>