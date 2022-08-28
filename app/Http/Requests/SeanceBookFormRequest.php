<?php

namespace App\Http\Requests;

use App\Enums\SeanceSeatStatus;
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
     *
     * @OA\Schema (
     *     schema="SeanceBookFormRequest",
     *     required={"email", "phone", "seat_ids"},
     *     @OA\Property (property="email", type="string", example="bakhadyrovf@gmail.com"),
     *     @OA\Property (property="phone", type="string", example="+998909144615"),
     *     @OA\Property (property="seat_ids", type="array", @OA\Items (
     *          type="integer"
     *     ), example="[1,2,3,4]"),
     * )
     *
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'seat_ids' => ['required', 'array', 'max:5'],
            'seat_ids.*' => ['integer', 'distinct', Rule::exists('seance_seat', 'seat_id')
                ->where('status', SeanceSeatStatus::AVAILABLE)
                ->where('seance_id', $this->route('seanceId'))
            ],
            'email' => ['required', 'string', 'email:filter'],
            'phone' => ['required', 'string', 'size:13', 'starts_with:+998']
        ];
    }
}
