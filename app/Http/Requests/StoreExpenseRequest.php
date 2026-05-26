<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'spent_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Le montant est obligatoire.',
            'amount.integer' => 'Le montant doit être un nombre entier.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'spent_at.required' => 'La date est obligatoire.',
            'spent_at.date' => 'La date n\'est pas valide.',
            'note.max' => 'La note ne peut pas dépasser 1000 caractères.',
        ];
    }
}
