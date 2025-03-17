<script src="{{ @asset('/js/app.js') }}"></script>

<div class="modal-title">
    {{ trans('DBT/configuration.vas.techsheet.tech_features') }}
</div>

<table class="modal-content">
    <tr>
        <td>
            @if($terminalDetails->pictures->isEmpty())
                <p class="no-img">
                    <img src="{{ asset('/images/no-img-placeholder.jpg') }}"
                         alt="{{ trans('DBT/configuration.attributes.no_img') }}">
                </p>
            @else
                <img width="150" src="{{ Storage::disk('terminal-pictures')->url($terminalDetails->pictures->first()->file_path) }}"
                     alt="{{ $terminalDetails->pictures->first()->fileName }}">

            @endif

                <div class="button">
                    <button type="button" data-dismiss="modal">
                        {{ trans('DBT/configuration.attributes.close') }}
                    </button>
            </div>
        </td>

        <td>
            <div class="content">
                @foreach ($groupedAttributes as $category)
                    <div class="category-collapsible">
                        <h3 class="category-title" onclick="toggle('{{ $category->id }}', 'attribute-group')">
                            {{ $category->description ?? $category->name }}
                        </h3>

                        <div class="attribute-group" id="attribute-{{ $category->id }}" style="display: none;">
                            @foreach ($category->dbtAttributes as $attribute)
                                <div class="text-spacing">

                                    @if(count($exploded=explode('|', $attribute->getPublicValue($terminal, $sources)->getReadableValue()))  > 1)
                                        <div class="collapsible">

                                            <h2 class="category-subtitle"
                                                onclick="toggle('{{ $attribute->id }}', 'attribute-subgroup')">
                                                <div>
                                                    {{ $attribute->description ?? $attribute->name }}
                                                </div>
                                            </h2>

                                            <div class="attribute-group attribute-subgroup"
                                                 id="attribute-{{ $attribute->id }}"
                                                 style="display: none">
                                                @foreach($exploded as $attribute)
                                                    {{$attribute}}<br>
                                                @endforeach
                                            </div>
                                        </div>

                                    @else
                                        {{ $attribute->description ?? $attribute->name }}:
                                        {{ $attribute->getPublicValue($terminal, $sources) ?  $attribute->getPublicValue($terminal, $sources)->getReadableValue() : '' }}
                                    @endif

                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
                <br>
            </div>
        </td>
    </tr>
</table>

<script>
    $('.category-collapsible').first().find('.attribute-group').show();

    function toggle(selector, group) {
        $(`.${group}`).not(`#attribute-${selector}`).hide();
        $(`#attribute-${selector}`).toggle();
    }
</script>