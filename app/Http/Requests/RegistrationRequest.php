<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Guard $auth): bool
    {
        $not_logged_in = ! $auth->check();

        return $not_logged_in;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->user->rules();
    }
}
