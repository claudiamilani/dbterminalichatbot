<otaserver user="{{ $requestData->user }}" password="{{ $requestData->password }}" version="2">
    <log-info>Telefonino</log-info>
    <profile-name>wp</profile-name>
    <deliver>
        <receiver transid="{{ $requestData->transId }}">{{ $requestData->phone }}</receiver>
        <sender>OTA Manager</sender>
        <setting type="{{ $requestData->ota_type }}" vendor="{{ $requestData->ota_vendor }}"
                 model="{{ $requestData->ota_model }}"
                 subtype="{{ $requestData->ota_sub_type }}">
            @foreach($requestData->parameters as $key => $value)
                <parameter key="{{ $key }}" value="{{ $value }}"/>
            @endforeach
        </setting>
    </deliver>
</otaserver>
