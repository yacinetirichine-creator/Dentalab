<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PraticienRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'civilite' => ['nullable', 'string', 'max:10'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'numero_rpps' => ['nullable', 'digits:11'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return __('interface.clients.champs.praticien');
    }
}
