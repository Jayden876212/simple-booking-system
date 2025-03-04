<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    protected $user;
    protected $auth;

    public function __construct(User $user, Guard $auth)
    {
        $this->auth = $auth;
        $this->user = $user;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
