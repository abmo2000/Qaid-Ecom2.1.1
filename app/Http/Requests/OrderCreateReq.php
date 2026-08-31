<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderCreateReq extends FormRequest
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
            "name" => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:8|max:30',
            'address' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'delivery_option' => 'sometimes|in:proceed,discuss',
            'payment_method' => 'required|string',
            'insta_account' => [Rule::requiredIf(fn() => $this->input('payment_method') === 'instapay') , 'nullable' ,'string' , 'max:50'],
            'payment_proof' => [
                Rule::requiredIf(fn() => $this->input('payment_method') === 'instapay'),
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:4096',
            ],
            'notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:100',
        ];
    }

    protected function prepareForValidation()
    {
       $this->merge(['delivery_option' => is_null($this->input('delivery_option')) ? 'proceed' : $this->input('delivery_option')]);    
    }

    public function messages()
    {
        return [
            'phone.min' => 'Phone number must be at least 8 characters.',
            'phone.max' => 'Phone number must not exceed 30 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'vaildation errors',
            'errors' => $validator->errors(),
        ] , 433));
    }
}
