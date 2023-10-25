<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeAttendanceResource\Pages;
use App\Filament\Resources\TimeAttendanceResource\RelationManagers;
use App\Models\TimeAttendance;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeAttendanceResource extends Resource
{
    protected static ?string $model = TimeAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('in'),
                Tables\Columns\TextColumn::make('out'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeAttendances::route('/'),
            'create' => Pages\CreateTimeAttendance::route('/create'),
            'edit' => Pages\EditTimeAttendance::route('/{record}/edit'),
        ];
    }
}
