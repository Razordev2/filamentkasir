<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'Profil Saya';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'avatar' => Auth::user()->avatar,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Forms\Components\FileUpload::make('avatar')
                    ->label('Foto Profil')
                    ->image()
                    ->directory('avatars')
                    ->disk('public')
                    ->imageEditor()
                    ->previewable()
                    ->preserveFilenames(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();

        if (!$user) {
            dd('User belum login!');
        }

        $data = $this->form->getState();

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['avatar']) && is_string($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }

        Notification::make()
            ->title('Profil berhasil diperbarui!')
            ->success()
            ->send();
    }
}
