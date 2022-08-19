<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SeatStoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'seats' => ['required', 'array'],
            'seats.*.is_vip' => ['nullable', 'boolean'],
            'seats.*.row' => ['required', 'integer', 'max:10'],
            'seats.*.place' => ['required', 'integer', 'max:30'],
            'seats.*.x' => ['required', 'integer'],
            'seats.*.y' => ['required', 'integer']
        ];
    }
}
