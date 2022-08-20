<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'movie_id' => ['required', 'integer'],
            'start_date_time' => ['required', 'date', 'date_format:Y-m-d H:i'],
            'vip_seat_price' => ['nullable', 'integer'],
            'standard_seat_price' => ['required', 'integer']
        ];
    }
}
