<div class="progress-group col-md-12">
    <span class="progress-text">{{ trans('common.available_storage_units',['value' => getAppAvailableStorage()]) }}</span>
    <span class="progress-number"><b> {{ trans('common.used_storage_percent',['value' => getAppUsedStoragePercent()]) }}%</b></span>
    <div class="progress sm">
        <div class="progress-bar @if(getAppAvailableStoragePercent() <= 5) progress-bar-red @elseif(getAppAvailableStoragePercent() <= 20) progress-bar-warning @else progress-bar-aqua @endif"
             style="width: {{getAppUsedStoragePercent()}}%"></div>
    </div>
</div>
