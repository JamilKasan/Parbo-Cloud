<?php

namespace App\Filament\Resources\ImportCsvResource\Pages;

use App\Filament\Resources\ImportCsvResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportCsvs extends ListRecords
{
    protected static string $resource = ImportCsvResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
