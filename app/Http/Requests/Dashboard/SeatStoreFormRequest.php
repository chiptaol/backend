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
     *
     * @OA\Schema (
     *     schema="SeatStoreFormRequest",
     *     @OA\Property (property="seats", type="array", @OA\Items (
     *          type="object",
     *          required={"row", "place", "x", "y"},
     *          properties={
     *              @OA\Property (property="is_vip", type="boolean", example=true),
     *              @OA\Property (property="row", type="integer", example=10),
     *              @OA\Property (property="place", type="integer", example=30),
     *              @OA\Property (property="x", type="integer", example=335),
     *              @OA\Property (property="y", type="integer", example=90)
     *          }
     *     ))
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
            'seats' => ['required', 'array'],
            'seats.*.is_vip' => ['nullable', 'boolean'],
            'seats.*.row' => ['required', 'integer', 'max:10'],
            'seats.*.place' => ['required', 'integer', 'max:30'],
            'seats.*.x' => ['required', 'numeric'],
            'seats.*.y' => ['required', 'numeric']
        ];
    }
}
