@extends('layouts.adminlte.template',['page_title' => trans('Ricerca globale'),'fa_icon_class' => 'fa-search'] )
@section('content')
    <div class="row">
        @forelse($results as $result)
            @component('components.widget')
                @slot('title')
                    {{ $result->first()::globalSearchName() }}
                @endslot
                @slot('body')
                    @component('components.table-list')
                        @slot('head')
                            <tr>
                                @if(is_array($header = $result->first()::globalSearchDisplayValue()))
                                    @foreach($header as $title)
                                        <td>{{ $title }}</td>
                                    @endforeach
                                @else
                                    <td></td>
                                @endif
                            </tr>
                        @endslot
                        @slot('body')
                            @foreach($result as $item)
                                <tr>
                                    @can('view',$item)
                                        @if(is_array($header))
                                            @foreach($header as $value => $title)
                                                <td>
                                                    <a href="{{ $item->globalSearchItemUrl() }}">{{ $item->$value }}</a>
                                                </td>
                                            @endforeach
                                        @else
                                            <td>
                                                <a href="{{ $item->globalSearchItemUrl() }}">{{ $item->{$item::globalSearchDisplayValue()} }}</a>
                                            </td>
                                        @endif

                                    @endcan
                                </tr>
                            @endforeach
                        @endslot
                        @slot('paginator')
                            @component('components.pagination',['contents' =>$result,'searchFilters' => request(['search'])])@endcomponent
                        @endslot
                    @endcomponent
                @endslot
            @endcomponent

        @empty
            <div class="col-md-12">
                <p>La ricerca per <b>{{ request('search') }}</b> non ha prodotto alcun risultato.</p>
            </div>

        @endforelse
    </div>
@endsection