<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ActionGroup;
class DiscountResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $model = Discount::class;
    protected static ?string $navigationGroup = 'Management';
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('type')
                ->options([
                    'percentage' => 'Persentase (%)',
                ])
                ->required()
                ->reactive(),

            TextInput::make('value')
                ->label('Nilai Diskon')
                ->numeric()
                ->nullable()
                ->minValue(1)
                ->maxValue(50)
                ->required()
                ->rule('max:50', 'Nilai diskon tidak boleh melebihi 100%'),

            TextInput::make('quota')
                ->label('Kuota')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->hidden()
                ->required(),

                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->native(false)
                    ->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode Diskon')->sortable()->searchable()->badge()->colors(['success']),
                TextColumn::make('type')->label('Tipe Diskon'),
                TextColumn::make('value')->label('Nilai Diskon')->sortable(),
                TextColumn::make('start_date')
                ->label('Start Date')
                ->date()
                ->sortable()
                ->badge()->colors(['success'])
                ->searchable(),
            TextColumn::make('end_date')
                ->label('End Date')
                ->date()
                ->badge()->colors(['warning'])
                ->sortable()
                ->searchable(),
            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
        ];
    }

    protected static function handleDiscountUsage(array $data): array
    {
        if (!empty($data['discount_code'])) {
            $discount = Discount::where('code', $data['discount_code'])->first();
    
            if (!$discount || !$discount->isValid()) {
                throw new \Exception('Kode diskon tidak valid atau sudah habis.');
            }
            $discount->decrement('quota', 1);
        }
        return $data;
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}