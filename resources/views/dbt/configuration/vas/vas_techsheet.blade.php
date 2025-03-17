<script src="{{ @asset('/js/app.js') }}"></script>

<div id="tech-container">
    <div class="title">
        {{ trans('DBT/configuration.vas.techsheet.title') }}
    </div>

    <div class="title tech-title">
        {{ trans('DBT/configuration.vas.techsheet.tech_features') }}
    </div>

    <div class="border-box">
        <table>
            <tr>
                <td class="column-left">
                    @if($terminalDetails->pictures->isEmpty())
                        <p class="no-img">
                            <img src="{{ asset('/images/no-img-placeholder.jpg') }}"
                                 alt="{{ trans('DBT/configuration.attributes.no_img') }}">
                        </p>
                    @else
                        <img src="{{ Storage::disk('terminal-pictures')->url($terminalDetails->pictures->first()->file_path) }}"
                             alt="{{ $terminalDetails->pictures->first()->fileName }}">
                    @endif
                </td>

                <td class="column-right">
                    <div class="content">
                        <table>
                            @foreach ($attributeDetails as $attribute)
                                <tr class="text-spacing">
                                    <td><span>{{ $attribute->description ?? $attribute->name  }}:</span></td>
                                    <td></td>
                                    <td>
                                        <span>{{optional($attribute->getPublicValue($terminal, $sources))->getReadableValue()}}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="configs-ota-container">
    <div class="title">
        {{ trans('DBT/configuration.vas.techsheet.config_features') }}
    </div>

    <div class="border-box">
        <table>
            <thead>
            <tr>
                <td>{{ trans('DBT/configuration.attributes.config_type') }}</td>
                <td class="text-center">{{ trans('DBT/configuration.attributes.show') }}</td>
                <td class="text-center">{{ trans('DBT/configuration.attributes.send_mail') }}</td>
                <td class="text-center">{{ trans('DBT/configuration.attributes.send_ota') }}</td>
            </tr>
            </thead>

            <tbody>
            @foreach($terminalDetails->configs as $config)
                <tr>
                    <td class="first-column">
                        {{ $config->ota->name }}
                    </td>

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
                        @if(isset($config->document) && $config->published)
                            <a href="{{ route('site::dbt.configurazione-vas.sendMail', ['file_path' => $config->document->file_path]) }}"
                               title="{{ trans('DBT/configuration.send.mail.title') }}"
                               data-toggle="modal" data-target="#myModal" data-remote="false">
                                <img src="{{asset('/images/visualizza_button_on.gif')}}" alt="">
                            </a>
                        @else
                            <img
                                    src="{{asset('/images/visualizza_button_off.gif')}}" alt="">
                        @endif
                    </td>

                    <td>
                        @if($config->published && $terminalDetails->ota_vendor !== null)
                            <a href="{{ route('site::dbt.configurazione-vas.sendOta', ['terminal_id' => $terminalDetails->id, 'ota_id' => $config->ota->id]) }}"
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
            </tbody>
        </table>
    </div>
</div>

<div id="myModal" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"></div>
    </div>
</div>
