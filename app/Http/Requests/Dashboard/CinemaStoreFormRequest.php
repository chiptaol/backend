<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\CoordinateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CinemaStoreFormRequest extends FormRequest
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
     *
     * @OA\Schema (
     *     schema="CinemaStoreFormRequest",
     *     required={"title", "address", "longitude", "latitude"},
     *     @OA\Property (property="title", type="string", example="Magic Cinema"),
     *     @OA\Property (property="address", type="string", example="Some-address"),
     *     @OA\Property (property="reference_point", type="string", example="Cafe castillo olymp"),
     *     @OA\Property (property="longitude", type="numeric", example=69.245077),
     *     @OA\Property (property="latitude", type="numeric", example=41.326226)
     * )
     *
     *
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:75', 'unique:cinemas'],
            'address' => ['required', 'string', 'max:150'],
            'reference_point' => ['nullable', 'string', 'max:75'],
            'longitude' => ['required', new CoordinateRule()],
            'latitude' => ['required', new CoordinateRule(), Rule::unique('cinemas', 'latitude')
                ->where('longitude', $this->input('longitude'))
            ]
        ];
    }

    public function messages()
    {
        return [
            'latitude.unique' => trans('Cinema with this coordinates already exists.')
        ];
    }
}
