<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isStudent() || $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['nullable', 'string', 'in:card,paypal,bank_transfer'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_method.in' => 'The selected payment method is invalid.',
            'coupon_code.max' => 'The coupon code is too long.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $course = $this->route('course');
            $user = $this->user();

            // Check if already enrolled
            if ($user->isEnrolledIn($course)) {
                $validator->errors()->add(
                    'enrollment',
                    'You are already enrolled in this course.'
                );
            }

            // Check if course is published
            if (!$course->isPublished()) {
                $validator->errors()->add(
                    'course',
                    'This course is not available for enrollment.'
                );
            }
        });
    }
}