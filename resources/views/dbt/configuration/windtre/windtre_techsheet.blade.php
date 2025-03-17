<script src="{{ @asset('/js/app.js') }}"></script>

<div id="configs-ota-container">
    <div class="container">

        <div id="configs-ota-container">
            <div class="title">
                {{ trans('DBT/configuration.windtre.techsheet.config_features') }}
            </div>

            <table class="h-spacing">
                <tr>
                    <td class="column-left">
                        <div class="image">
                            @if($terminalDetails->pictures->isEmpty())
                                <p class="no-img">
                                    <img src="{{ asset('/images/no-img-placeholder.jpg') }}"
                                         alt="{{ trans('DBT/configuration.attributes.no_img') }}">
                                </p>
                            @else
                                <img src="{{ Storage::disk('terminal-pictures')->url($terminalDetails->pictures->first()->file_path) }}"
                                     alt="{{ $terminalDetails->pictures->first()->fileName }}">

                            @endif

                            <a href="{{ route('site::dbt.configurazione-windtre.show-tech-sheet', ['terminal_id' => $terminalDetails->id]) }}"
                               title="{{ trans('DBT/configuration.windtre.techsheet.tech_features') }}"
                               data-toggle="modal" data-target="#myModal" data-remote="false">
                                {{ trans('DBT/configuration.windtre.techsheet.tech_features') }}
                            </a>
                        </div>
                    </td>

                    <td class="column-right">
                        <table>
                            <tr>
                                <td class="label">{{ trans('DBT/configuration.attributes.config_type') }}</td>
                                <td class="label">{{ trans('DBT/configuration.attributes.show') }}</td>
                                <td class="label">{{ trans('DBT/configuration.attributes.send') }}</td>
                            </tr>
                        </table>

                        <table class="config-table">
                            <tr>
                            @foreach($terminalDetails->configs as $config)
                                <tr>
                                    <td>{{ $config->ota->name }}</td>

                                    <td>
                                        @if(isset($config->document) && $config->published)
                                            <a
                                                    href="{{ Storage::disk('documents')->url($config->document->file_path) }}"
                                                    target="_blank"
                                                    title="{{ trans('DBT/configuration.attributes.show_new_tab') }}">
                                                <img
                                                        src="{{asset('/images/visualizza_button_on.gif')}}"
                                                        alt="{{ trans('DBT/configuration.attributes.show_new_tab') }}">
                                            </a>
                                        @else
                                            <img
                                                    src="{{asset('/images/visualizza_button_off.gif')}}" alt="">
                                        @endif
                                    </td>

                                    <td>
                                        @if($config->published && $terminalDetails->ota_vendor !== null)
                                            <a href="{{ route('site::dbt.configurazione-windtre.sendOta', ['config_type' => 2, 'terminal_id' => $config->terminal->id, 'ota_id' => $config->ota->id]) }}"
                                               title="{{ trans('DBT/configuration.send.ota.title') }}"
                                               data-toggle="modal" data-target="#myModal" data-remote="false">
                                                <img src="{{asset('/images/download_button_on.gif')}}" alt="">
                                            </a>
                                        @else
                                            <img
                                                    src="{{asset('/images/download_button_off.gif')}}" alt="">
                                        @endif
                                    </td>
                                </tr>
                    @endforeach
                </tr>
            </table>
        </div>
    </div>
</div>

<div id="myModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>
