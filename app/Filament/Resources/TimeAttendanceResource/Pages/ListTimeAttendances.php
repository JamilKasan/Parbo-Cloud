<?php

namespace App\Filament\Resources\TimeAttendanceResource\Pages;

use App\Filament\Resources\TimeAttendanceResource;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeAttendances extends ListRecords
{
    protected static string $resource = TimeAttendanceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\CreateAction::make()->action(
                function (array $data)
                {

                }
            )
            ->form(
                FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->directory('csv')
            )
        ];
    }
}
