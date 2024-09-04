<?php

namespace App\Http\Requests;

use App\Enums\EtatEnum;
use App\Enums\StateEnum;
use App\Enums\UserRole;
use App\Rules\CustumPasswordRule;
use App\Rules\PasswordRules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function Rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users,login|max:255',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'required|image|mimes:jpeg,png,svg|max:40', // Photo obligatoire, max 40 ko, formats autorisés : jpeg, png, svg
            'etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function validationMessages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être ADMIN ou BOUTIQUIER ou CLIENT',
            'email.required' => "L'email est obligatoire.",
            'email.email' => "L'email doit être une adresse email valide.",
            'email.unique' => "Cet email est déjà utilisé.",
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => "Cet login est déjà utilisé.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}
