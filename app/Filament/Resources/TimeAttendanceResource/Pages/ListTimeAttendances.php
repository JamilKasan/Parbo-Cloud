<?php

namespace App\Filament\Resources\TimeAttendanceResource\Pages;

use App\Filament\Resources\TimeAttendanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeAttendances extends ListRecords
{
    protected static string $resource = TimeAttendanceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
