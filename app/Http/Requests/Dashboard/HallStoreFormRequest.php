<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class HallStoreFormRequest extends FormRequest
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
     *     schema="HallStoreFormRequest",
     *     required={"title"},
     *     @OA\Property (property="title", type="string", example="Зал Номер - 6 (VIP)"),
     *     @OA\Property (property="description", type="string"),
     *     @OA\Property (property="is_vip", type="boolean", example=true, default=false)
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
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:200'],
            'is_vip' => ['nullable', 'boolean']
        ];
    }
}
