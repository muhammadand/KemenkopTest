<?php

namespace App\Exports;

use App\Models\News;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return News::with('province')
            ->select('id','title','sub_title','content','province_id','created_at','updated_at')
            ->get()
            ->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'Title' => $item->title,
                    'Sub Title' => $item->sub_title,
                    'Content' => $item->content,
                    'Province' => $item->province->name ?? '-',
                    'Created At' => $item->created_at,
                    'Updated At' => $item->updated_at,
                ];
            });
    }
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Sub Title',
            'Content',
            'Province',
            'Created At',
            'Updated At',
        ];
    }
}
