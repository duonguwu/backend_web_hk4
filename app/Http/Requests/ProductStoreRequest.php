<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:258',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'brand' => 'required',
            'category' => 'required',
            'gender' => 'required',
            'weight' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'newPrice' => 'required',
            'trending' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            // 'image.required' => 'Image is required!',
            'description.required' => 'Descritpion is required!',
            'brand.required' => 'Brand is required!',
            'category.required' => 'Category is required!',
            'gender.required' => 'Gender is required!',
            'weight.required' => 'Weight is required!',
            'quantity.required' => 'Quantity is required!',
            'price.required' => 'Price is required!',
            'newPrice.required' => 'New price is required!',
            'Trending.required' => 'Trending is required!',
        ];
    }
}
