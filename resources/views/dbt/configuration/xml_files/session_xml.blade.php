<otamanager username="{{ $sessionData->user }}" password="{{ $sessionData->password }}">
    <query>
        <parameters profile="wp" settingname="{{ $sessionData->ota_ext_0 }}"/>
    </query>
</otamanager>
