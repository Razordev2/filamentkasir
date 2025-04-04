<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranResource\Pages;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $modelLabel = 'Expenses';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Management'; 
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Pengeluaran'),
                Forms\Components\Textarea::make('notes')
                    ->nullable()
                    ->label('Catatan'),
                Forms\Components\DatePicker::make('dateorder')
                    ->required()
                    ->label('Tanggal Order'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('Jumlah'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Pengeluaran')->searchable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(50),
                Tables\Columns\TextColumn::make('dateorder')->label('Tanggal Order')->date(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPengeluarans::route('/'),
        ];
    }
}
