<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ScheduleResource;
use Coolsam\FilamentExcel\Actions\ImportField;
use Coolsam\FilamentExcel\Actions\ImportAction;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('schedules')->fields([
                ImportField::make('user.name')->required(),
                ImportField::make('shift.name')->required(),
                ImportField::make('office.name')->required(),
                ImportField::make('is_wfa')->required(),
                ImportField::make('is_banned')->required(),
            ]),
            Actions\CreateAction::make(),
        ];
    }
}
