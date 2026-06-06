<?php

namespace App\Http\Requests;

use App\Enums\TransactionCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCorrespondenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['creator', 'checker'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(TransactionCategory::class)],
            'sender' => ['required', 'string', 'max:255'],
            'receiver' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'priority' => ['required', Rule::in(['normal', 'urgent'])],
            'file_path' => ['nullable', 'file', 'mimes:pdf,png,jpg,zip', 'max:2097152'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category' => 'correspondence category',
            'sender' => 'sender',
            'receiver' => 'receiver',
            'subject' => 'subject',
            'content' => 'content',
            'priority' => 'priority',
            'file_path' => 'attachment',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Please select a correspondence category.',
            'category.enum' => 'Invalid correspondence category.',
            'sender.required' => 'Please enter the sender name.',
            'receiver.required' => 'Please enter the receiver name.',
            'subject.required' => 'Please enter the correspondence subject.',
            'content.required' => 'Please enter the correspondence content.',
            'priority.required' => 'Please select a priority.',
            'priority.in' => 'Invalid priority.',
            'file_path.mimes' => 'The attachment must be a PDF, PNG, or JPG file.',
            'file_path.max' => 'The attachment must not exceed 2 GB.',
        ];
    }
}
