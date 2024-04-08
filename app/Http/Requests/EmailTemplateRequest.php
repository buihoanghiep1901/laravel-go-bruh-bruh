<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends FormRequest
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
        $id = $this->id ?? NULL;
        $uniqueEmailTemplate = Rule::unique('email_templates');
        if ($id) {
            $uniqueEmailTemplate->ignore($id);
        }

        return [
            'title' => ['required',$uniqueEmailTemplate],
            'created_by' => ['required'],
        ];
    }
}
