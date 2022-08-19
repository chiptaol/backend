<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\CoordinateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CinemaUpdateFormRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:75', Rule::unique('cinemas', 'title')->ignore($this->route('id'))],
            'address' => ['required', 'string', 'max:150'],
            'logo_id' => ['required', 'string', 'exists:file_sources,id', Rule::unique('cinemas', 'logo_id')->ignore($this->route('id'))],
            'reference_point' => ['nullable', 'string', 'max:75'],
            'longitude' => ['required', new CoordinateRule()],
            'latitude' => ['required', new CoordinateRule(), Rule::unique('cinemas', 'latitude')
                ->where('longitude', $this->input('longitude'))
                ->ignore($this->route('id'))
            ],
            'phone' => ['required', 'string', 'starts_with:+998', 'size:13', Rule::unique('cinemas', 'phone')->ignore($this->route('id'))]
        ];
    }
}
