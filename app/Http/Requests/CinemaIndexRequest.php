<?php

namespace App\Http\Requests;

use App\Rules\CoordinateRule;
use Illuminate\Foundation\Http\FormRequest;

class CinemaIndexRequest extends FormRequest
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
            'longitude' => ['nullable', new CoordinateRule()],
            'latitude' => ['nullable', new CoordinateRule()]
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'longitude' => \Illuminate\Support\Str::substr($this->input('longitude'), 0, 9),
            'latitude' => \Illuminate\Support\Str::substr($this->input('latitude'), 0, 9)
        ]);
    }
}
