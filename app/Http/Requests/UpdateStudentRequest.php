<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student')->id;

        return [
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($studentId)],
            'name' => 'required|string|max:100',
            'birth_place' => 'required|string|max:50',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'gender' => 'required|in:male,female',
            'academic_year_id' => 'required|exists:academic_years,id',
            'phone' => 'required|string|max:20',
            'max_loan' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
