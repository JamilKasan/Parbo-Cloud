<?php

namespace App\Filament\Resources\ImportCsvResource\Pages;

use App\Filament\Resources\ImportCsvResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportCsv extends EditRecord
{
    protected static string $resource = ImportCsvResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
