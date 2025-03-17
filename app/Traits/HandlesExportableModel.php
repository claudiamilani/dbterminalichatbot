<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Traits;

use App\Exports\ModelsExport;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait HandlesExportableModel
{
    /**
     * Download the exported data to excel
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new ModelsExport($this->exportQuery()), 'report.xlsx');
    }

    abstract private function exportQuery(): Builder;

}