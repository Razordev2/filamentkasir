<?php

namespace App\Filament\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component as FormComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class Login extends SimplePage
{
    use WithRateLimiting;
    use InteractsWithFormActions;
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $layout = 'filament-panels::components.layout.base';

    protected static string $view = 'filament.pages.login';

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'filament.app.auth.login';
    }

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body',
                    __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body',
                    [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    public function action1(): Action
    {
        return Action::make('action1')
            ->extraAttributes([
                'class' => 'w-full'
            ])
            ->label('Action 1')
            ->action(fn(array $data) => dd('Action 1'))
            ->form([
                TextInput::make('action1')
                    ->label('Action 1')
                    ->required()
                    ->autofocus()
                    ->extraAttributes(['tabindex' => 3]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    public function action2()
    {
        return Action::make('action2')
            ->extraAttributes([
                'class' => 'w-full'
            ])
            ->label('Action 2')
            ->action(fn(array $data) => dd('Action 2'))
            ->form([
                TextInput::make('action2')
                    ->label('Action 2')
                    ->required()
                    ->autofocus()
                    ->extraAttributes(['tabindex' => 3]),
            ]);
    }

    public function getFormActionsAlignment(): string
    {
        return Alignment::Center->name;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): FormComponent
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/login.form.email.label'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): FormComponent
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): FormComponent
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
