<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CabinetRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'raison_sociale' => ['required', 'string', 'max:255'],
            'siret' => [
                'nullable',
                'digits:14',
                // Deux cabinets d'un même laboratoire ne peuvent pas partager
                // un SIRET. Rien n'empêche deux laboratoires différents
                // d'avoir le même cabinet pour client.
                Rule::unique('cabinets', 'siret')
                    ->where('laboratoire_id', $this->user()->laboratoire_id)
                    ->ignore($this->route('cabinet')),
            ],
            'numero_tva' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse_ligne_1' => ['nullable', 'string', 'max:255'],
            'adresse_ligne_2' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['required', 'string', 'size:2'],
            'delai_reglement_jours' => ['required', 'integer', 'min:0', 'max:365'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return __('interface.clients.champs.cabinet');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pays' => $this->input('pays') ?: 'FR',
            'delai_reglement_jours' => $this->input('delai_reglement_jours') ?? 30,
        ]);
    }
}
