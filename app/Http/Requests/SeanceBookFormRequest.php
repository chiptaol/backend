<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeanceBookFormRequest extends FormRequest
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
            'seat_ids' => ['required', 'array', 'max:5'],
            'seat_ids.*' => ['integer', Rule::exists('seance_seat', 'id')
                ->where('is_available', true)
                ->where('seance_id', $this->route('seanceId'))
            ],
            'email' => ['required', 'string', 'email:filter'],
            'phone' => ['required', 'string', 'size:13', 'starts_with:+998']
        ];
    }
}
