<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'code' => 'required|string|max:20|unique:books,code',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'publisher_id' => 'required|exists:publishers,id',
            'publish_place' => 'required|string|max:100',
            'publish_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isbn' => 'nullable|string|max:20',
            'stock' => 'required|integer|min:1',
            'page_count' => 'required|integer|min:1',
            'thickness' => 'nullable|string|max:20',
            'classification_id' => 'required|exists:classifications,id',
            'sub_classification_id' => 'nullable|exists:sub_classifications,id',
            'category_id' => 'required|exists:categories,id',
            'shelf_location' => 'required|string|max:50',
            'description' => 'nullable|string',
            'book_source_id' => 'required|exists:book_sources,id',
            'entry_date' => 'required|date',
            'price' => 'nullable|numeric|min:0',
            'is_textbook' => 'boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
