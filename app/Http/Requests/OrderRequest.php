<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо указать ID пользователя',
            'user_id.integer' => 'ID пользователя должен быть числом',
            'user_id.exists' => 'Указанный пользователь не существует',

            'items.required' => 'Необходимо добавить товары в заказ',
            'items.array' => 'Товары должны быть переданы массивом',
            'items.min' => 'Должен быть хотя бы один товар в заказе',

            'items.*.product_id.required' => 'У каждого товара должен быть ID',
            'items.*.product_id.integer' => 'ID товара должен быть числом',
            'items.*.product_id.exists' => 'Один из товаров не найден',

            'items.*.quantity.required' => 'Укажите количество для каждого товара',
            'items.*.quantity.integer' => 'Количество должно быть целым числом',
            'items.*.quantity.min' => 'Минимальное количество товара: 1'
        ];


    }
}
