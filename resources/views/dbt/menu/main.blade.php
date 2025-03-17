<li class="treeview {{ (isActiveRoute('admin::dbt.*') && isNotActiveRoute('admin::dbt.transpose_requests*','admin::dbt.transpose_configs*','admin::dbt.channels*','admin::dbt.document_types*','admin::dbt.documents*','admin::dbt.dwh_operations*','admin::dbt.ingestions*','admin::dbt.ingestion_sources*','admin::dbt.legacy_imports*')) ? 'active':'' }}">
    @can('list','App\DBT\Models\Terminal')
        <a href="#"><i class="fa fa-fw fa-mobile-alt"></i> <span>@lang('DBT/common.terminals_menu')</span>
            <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>
        </a>
    @endcan
    <ul class="treeview-menu">
        @can('list','App\DBT\Models\Terminal')
            <li class="{{ isActiveRoute('admin::dbt.terminals*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.terminals.index') }}">
                    <span>@lang('DBT/terminals.menu_title')</span></a>
            </li>
        @endcan
        @can('list', 'App\DBT\Models\Vendor')
            <li class="{{ isActiveRoute('admin::dbt.vendors*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.vendors.index') }}">
                    <span>@lang('DBT/vendors.menu_title')</span></a>
            </li>
        @endcan
        @can('list','App\DBT\Models\Tac')
            <li class="{{ isActiveRoute('admin::dbt.tacs*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.tacs.index') }}">
                    <span>@lang('DBT/tacs.menu_title')</span></a>
        @endcan
        @can('list','App\DBT\Models\DbtAttribute')
            <li class="{{ isActiveRoute('admin::dbt.attributes*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.attributes.index') }}">
                    <span>@lang('DBT/attributes.menu_title')</span></a>
            </li>
        @endcan
        @can('list','App\DBT\Models\AttrCategory')
            <li class="{{ isActiveRoute('admin::dbt.attr_categories*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.attr_categories.index') }}">
                    <span>@lang('DBT/attr_categories.menu_title')</span></a>
            </li>
        @endcan
        @can('list','App\DBT\Models\Ota')
            <li class="{{ isActiveRoute('admin::dbt.otas*') ? 'active': '' }}">
                <a href="{{ route('admin::dbt.otas.index') }}">
                    <span>@lang('DBT/otas.menu_title')</span></a>
        @endcan
    </ul>
</li>

@if (Auth::user()->can('list', 'App\DBT\Models\Ingestion') || Auth::user()->can('list', 'App\DBT\Models\IngestionSource'))
    <li class="treeview {{ isActiveRoute('admin::dbt.ingestions*','admin::dbt.ingestion_sources*') ? 'active':'' }}">
        <a href="#"><i class="fa fa-file-import fa-fw"></i> <span>@lang('DBT/common.ingestion_menu')</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
        </a>
        <ul class="treeview-menu">
            @can('list','App\DBT\Models\Ingestion')
                <li class="{{ isActiveRoute('admin::dbt.ingestions*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.ingestions.index') }}">
                        <span>@lang('DBT/ingestions.menu_title')</span></a>
                </li>
            @endcan
            @can('list','App\DBT\Models\IngestionSource')
                <li class="{{ isActiveRoute('admin::dbt.ingestion_sources*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.ingestion_sources.index') }}">
                        <span>@lang('DBT/ingestion_sources.menu_title')</span></a>
                </li>
            @endcan
        </ul>
    </li>
@endif
@if (Auth::user()->can('list', 'App\DBT\Models\Channel') || Auth::user()->can('list', 'App\DBT\Models\DocumentType') || Auth::user()->can('list', 'App\DBT\Models\Document'))
    <li class="treeview {{ isActiveRoute('admin::dbt.channels*','admin::dbt.document_types*','admin::dbt.documents*') ? 'active':'' }}">
        <a href="#"><i class="fa fa-paperclip fa-fw"></i> <span>@lang('DBT/common.docs_menu')</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
        </a>
        <ul class="treeview-menu">
            @can('list','App\DBT\Models\Document')
                <li class="{{ isActiveRoute('admin::dbt.documents*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.documents.index') }}">
                        <span>@lang('DBT/documents.menu_title')</span></a>
                </li>
            @endcan
            @can('list','App\DBT\Models\Channel')
                <li class="{{ isActiveRoute('admin::dbt.channels*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.channels.index') }}">
                        <span>@lang('DBT/channels.menu_title')</span></a>
                </li>
            @endcan
            @can('list','App\DBT\Models\DocumentType')
                <li class="{{ isActiveRoute('admin::dbt.document_types*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.document_types.index') }}">
                        <span>@lang('DBT/document_types.menu_title')</span></a>
                </li>
            @endcan
        </ul>
    </li>
@endif
@if (Auth::user()->can('list', 'App\DBT\TransposeRequest') || Auth::user()->can('list', 'App\DBT\Models\TransposeConfig'))
    <li class="treeview {{ isActiveRoute('admin::dbt.transpose_requests*','admin::dbt.transpose_configs*') ? 'active':'' }}">
        <a href="#"><i class="fa fa-table fa-fw"></i> <span>@lang('DBT/common.transpose_menu')</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
        </a>
        <ul class="treeview-menu">
            @can('list','App\DBT\TransposeRequest')
                <li class="{{ isActiveRoute('admin::dbt.transpose_requests*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.transpose_requests.index') }}">
                        <span>@lang('DBT/transpose_requests.menu_title')</span></a>
            @endcan
            @can('list','App\DBT\Models\TransposeConfig')
                <li class="{{ isActiveRoute('admin::dbt.transpose_configs*') ? 'active': '' }}">
                    <a href="{{ route('admin::dbt.transpose_configs.index') }}">
                        <span>@lang('DBT/transpose_configs.menu_title')</span></a>
            @endcan
        </ul>
    </li>
@endif
@can('list_dwh_operations','App\DBT\Models\TransposeConfig')
    <li class="{{ isActiveRoute('admin::dbt.dwh_operations*') ? 'active': '' }}">
        <a href="{{ route('admin::dbt.dwh_operations.index') }}">
            <i class="fa fa-fw fa-database"></i><span>@lang('DBT/dwh_operations.menu_title')</span></a>
@endcan
@can('list','App\DBT\Models\LegacyImport')
    <li class="{{ isActiveRoute('admin::dbt.legacy_imports*') ? 'active': '' }}">
        <a href="{{ route('admin::dbt.legacy_imports.index') }}">
            <i class="fa fa-fw fa-exchange-alt"></i><span>@lang('DBT/legacy_imports.menu_title')</span></a>
    </li>
@endcan