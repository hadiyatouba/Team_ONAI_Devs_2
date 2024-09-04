<?php

namespace App\Http\Requests;

use App\Enums\EtatEnum;
use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;
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
    public function rules(): array
    {
        return [
            'surname' => 'required|string|max:255|unique:clients,surname',
            'telephone' => ['required', new TelephoneRule()],
            'addresse' => 'nullable|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,svg|max:40', // Photo obligatoire, max 40 ko, formats autorisés : jpeg, png, svg
            // Validation pour l'utilisateur
            'user.nom' => 'required_with:user|string|max:255',
            'user.prenom' => 'required_with:user|string|max:255',
            'user.login' => 'required_with:user|string|unique:users,login|max:255',
            'user.password' => 'required_with:user|string|min:6|confirmed',
            'user.role_id' => 'required_with:user|exists:roles,id',
            'user.photo' => 'required_with:user|image|mimes:jpeg,png,svg|max:40', // Même validation pour la photo de l'utilisateur
            'user.etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
        ];
/*
        if ($this->filled('user')) {
            $userRules = (new StoreUserRequest())->Rules();
            $rules = array_merge($rules, ['user' => 'array']);
            $rules = array_merge($rules, array_combine(
                array_map(fn($key) => "user.$key", array_keys($userRules)),
                $userRules
            ));
        }
*/
      //  dd($rules);

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}