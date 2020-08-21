<?php

namespace App\Exports;

use App\Utils\TransfersOutput;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransfersExport implements FromQuery, WithMapping, WithHeadings
{
    use TransfersOutput;

    /**
     * @var Builder
     */
    protected Builder $query;

    /**
     * TransfersExport constructor.
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query->orderBy('created_at');
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'USD amount',
            'Currency amount',
            'In recipient currency',
            'User',
            'Operation type',
            'From/To',
            'Datetime'
        ];
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function map($data): array
    {
        return $this->formatData($data);
    }
}
