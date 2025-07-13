<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOrderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'ID заказа обязателен',
            'order_id.integer'  => 'ID заказа должен быть числом',
            'order_id.exists'   => 'Такой заказ не найден',
        ];
    }
}
