<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ModelsExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    private Builder $query;
    private ?array $headings;
    private ?array $fields;
    private null|Model $reference_model;
    private string $header_coords;
    private string $all_sheet_coords;

    public function __construct(Builder $query, ?array $fields = null, ?array $headings = null)
    {
        $this->query = $query;
        $this->fields = $fields;
        $this->headings = $headings;
        $this->reference_model = $this->query->getModel()->make();
    }

    public function query(): Relation|Builder|\Illuminate\Database\Query\Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        if (empty($this->headings) && empty($this->fields) && $this->reference_model) {
            return array_map(function ($item) {
                return strtoupper($item);
            }, array_keys($this->reference_model->toArray()));
        }
        return $this->headings ?? array_map(function ($item) {
                return strtoupper($item);
            }, $this->fields);
    }

    public function map($row): array
    {
        $fields = [];
        if (empty($this->fields)) {
            $this->fields = array_keys($this->reference_model->toArray());
        }
        foreach ($this->fields as $field) {
            $fields[] = $this->loadRelation($row,$field);
        }
        return $fields;
    }

    private function loadRelation($model,$field){
        $relations = explode('.',$field);
        foreach ($relations as $relation){
            $model = $model->{$relation};
            if(empty($model)) return null;
        }
        return $model;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $alphabet = range('A', 'Z');
        $ending_letter = 'A';
        $letters_cnt = 1;
        if ($this->reference_model) {
            $fields_cnt = $this->fields ? count($this->fields) : count(array_keys($this->reference_model->toArray()));
            while ($fields_cnt > 26) {
                $letters_cnt++;
                $fields_cnt = $fields_cnt - 26;
            }
            if ($letters_cnt == 1) {
                $ending_letter = $alphabet[$fields_cnt - 1];
            } else {
                $alphabet_cursor = 0;
                while ($letters_cnt > 0) {
                    $ending_letter .= $alphabet[$alphabet_cursor];
                    $alphabet_cursor++;
                    $letters_cnt--;
                }
            }
        }
        $this->header_coords = 'A1:' . $ending_letter . '1';
        $this->all_sheet_coords = 'A1:' . $ending_letter . ($this->query->count() + 1);
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getFont()->setName('SansSerif');
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getFont()->setSize('10');
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getAlignment()->setHorizontal('left');
                $event->sheet->getDelegate()->getStyle($this->header_coords)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getBorders()->getAllBorders()->setBorderStyle('thin')->getColor()->setARGB('000000');
                $event->sheet->getDelegate()->getStyle($this->header_coords)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle($this->header_coords)->getFill()->setFillType('solid')->getStartColor()->setARGB('9ACCFF');
                $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight('25.25');
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth('20.57');
                $event->sheet->getDelegate()->getStyle($this->all_sheet_coords)->getAlignment()->setWrapText(true);
            },
        ];
    }
}
