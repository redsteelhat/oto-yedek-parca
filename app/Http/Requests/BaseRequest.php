<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute alanı zorunludur.',
            'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
            'max' => ':attribute en fazla :max karakter olabilir.',
            'min' => ':attribute en az :min karakter olmalıdır.',
            'numeric' => ':attribute sayısal bir değer olmalıdır.',
            'integer' => ':attribute tam sayı olmalıdır.',
            'exists' => 'Seçilen :attribute geçersiz.',
            'unique' => ':attribute zaten kullanılıyor.',
            'image' => ':attribute bir resim dosyası olmalıdır.',
            'mimes' => ':attribute şu formatlardan biri olmalıdır: :values.',
            'file' => ':attribute bir dosya olmalıdır.',
        ];
    }
}

