<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranResource\Pages;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
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
                    ->label('Nama Barang'),
                Forms\Components\Textarea::make('notes')
                    ->nullable()
                    ->label('Catatan'),
                Forms\Components\DatePicker::make('dateorder')
                    ->required()
                    ->label('Tanggal Restock'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('harga barang'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama barang')->searchable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(50),
                Tables\Columns\TextColumn::make('dateorder')->label('Date')->date(),
                Tables\Columns\TextColumn::make('amount')->label('Harga Barang')->sortable()->getStateUsing(fn($record) => 'Rp ' . number_format($record->amount, 2, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions'),
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
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
