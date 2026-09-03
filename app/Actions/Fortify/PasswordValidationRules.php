<?php

namespace App\Actions\Fortify;

use App\Support\ReglasContrasena;
use Illuminate\Contracts\Validation\Rule;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return array_merge(['required', 'string'], ReglasContrasena::reglas(), ['confirmed']);
    }
}
