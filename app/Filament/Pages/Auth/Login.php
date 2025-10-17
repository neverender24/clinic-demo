<?php

namespace App\Filament\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Component;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class Login extends \Filament\Auth\Pages\Login
{

    public function form(Schema $schema): Schema
    {
        

        return $schema
            ->components([
                $this->getLoginFormComponent()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {

        return TextInput::make('login')
            ->label('Username')
            ->required()
            ->autocomplete(false)
            ->autofocus();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        //    $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'name';

        $login_type = 'username';

        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
    }
}
