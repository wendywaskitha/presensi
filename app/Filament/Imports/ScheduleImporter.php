<?php

namespace App\Filament\Imports;

use App\Models\Schedule;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ScheduleImporter extends Importer
{
    protected static ?string $model = Schedule::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user')
                // ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('shift')
                // ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('office')
                // ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('is_wfa')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_banned')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Schedule
    {
        // return Schedule::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Schedule();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your schedule import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
