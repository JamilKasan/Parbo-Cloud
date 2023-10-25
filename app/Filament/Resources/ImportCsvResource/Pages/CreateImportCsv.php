<?php

namespace App\Filament\Resources\ImportCsvResource\Pages;

use App\Filament\Resources\ImportCsvResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateImportCsv extends CreateRecord
{
    protected static string $resource = ImportCsvResource::class;

    protected function afterCreate()
    {
        logTimeAttendance(fopen( ('storage/' . ($this->record->file)),"r"));
        Storage::disk('public')->delete($this->record->file);
        $this->record->delete();
        return redirect()->route('filament.resources.time-attendances.index');
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.resources.time-attendances.index');
    }
}
