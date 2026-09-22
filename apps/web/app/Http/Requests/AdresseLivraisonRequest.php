<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdresseLivraisonRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'libelle' => ['required', 'string', 'max:255'],
            'adresse_ligne_1' => ['required', 'string', 'max:255'],
            'adresse_ligne_2' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'ville' => ['required', 'string', 'max:255'],
            'pays' => ['required', 'string', 'size:2'],
            'contact' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'est_principale' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return __('interface.clients.champs.adresse');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pays' => $this->input('pays') ?: 'FR',
            'est_principale' => $this->boolean('est_principale'),
        ]);
    }
}
