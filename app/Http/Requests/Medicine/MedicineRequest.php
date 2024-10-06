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
            'expires_at' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation()
    {
        // Calculate the expires_at date
        $expiresAt = now()->addDays($this->days + $this->months * 30 + $this->years * 365);

        // Merge the calculated expires_at into the request data
        $this->merge([
            'expires_at' => $expiresAt,
        ]);
    }

    // Overriding the validated method to exclude days, months, and years
    public function validated($key = null, $default = null)
    {
        $validatedData = parent::validated();

        // Unset the days, months, and years fields so they aren't returned
        unset($validatedData['days'], $validatedData['months'], $validatedData['years']);

        return $validatedData;
    }
}
