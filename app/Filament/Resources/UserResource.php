<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Navigation\NavigationItem;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Spatie\Permission\Models\Role;
use App\Filament\Resources\UserResource\Pages\ListUsers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Management Account'; 
    protected static ?string $navigationLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($context) => $context === 'create') 
                    ->maxLength(255),

                Select::make('roles')
                    ->label('Role')
                    ->options(Role::pluck('name', 'name')->toArray()) 
                    ->searchable()
                    ->multiple() 
                    ->preload()
                    ->required()
                    ->saveRelationshipsUsing(fn (User $record, array $state) => $record->syncRoles($state)), // Simpan Role
            ]);
    }
    public static function getNavigationItems(): array
    {
        if (auth()->user()->hasRole('super_admin')) {
            return [
                NavigationItem::make()
                    ->label('Management Users')
                    ->icon('heroicon-o-users')
                    ->url(static::getUrl('index'))
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ];
        }
    
        return [];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')->label('Role')->sortable()->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}

