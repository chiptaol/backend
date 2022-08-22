<?php

namespace App\Http\Requests\Dashboard;

use App\Exceptions\BusinessException;
use App\Models\Hall;
use App\Models\Seance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeanceStoreFormRequest extends FormRequest
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
     *     schema="SeanceStoreFormRequest",
     *     required={"movie_id", "seances"},
     *     @OA\Property (property="movie_id", type="integer", example=731491),
     *     @OA\Property (property="seances", type="array", @OA\Items (
     *          type="object",
     *          required={"movie_format", "start_date_time", "hall_id"},
     *          properties={
     *              @OA\Property (property="movie_format_id", type="integer", example=1),
     *              @OA\Property (property="start_date_time", type="string", example="2022-08-20 20:30", description="format: `Y-m-d H:i`"),
     *              @OA\Property (property="hall_id", type="integer", example=1),
     *              @OA\Property (property="standard_seat_price", type="integer", example="3500000", description="required if `vip_seat_price` is null"),
     *              @OA\Property (property="vip_seat_price", type="integer", example="5000000", description="required if `standard_seat_price` is nul")
     *          }
     *     ))
     *
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
            'movie_id' => ['required', 'integer'],
            'seances' => ['required', 'array'],
            'seances.*.movie_format_id' => ['required', 'integer', 'exists:formats,id'],
            'seances.*.start_date_time' => ['required', 'date', 'date_format:Y-m-d H:i', 'after_or_equal:tomorrow'],
            'seances.*.vip_seat_price' => ['nullable', 'integer'],
            'seances.*.standard_seat_price' => ['nullable', 'integer',],
            'seances.*.hall_id' => ['required', Rule::exists(Hall::class, 'id')->where('cinema_id', $this->route('cinemaId'))]
        ];
    }
}
