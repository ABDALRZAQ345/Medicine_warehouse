<?php

namespace App\Http\Requests\Medicine;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scientific_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['required', 'string', 'max:70'],
            'type' => ['required', 'string', 'max:70'],
            'manufacturer_id' => ['required', 'exists:manufacturers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:1'],
            'days' => ['required', 'integer'],
            'months' => ['required', 'integer'],
            'years' => ['required', 'integer'],
            'discount' => ['required', 'integer', 'min:0', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:3072'],
        ];
    }
}
