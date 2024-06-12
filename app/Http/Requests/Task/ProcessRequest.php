<?php

namespace App\Http\Requests\Task;

use App\Models\Management\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payload.title' => ['required', 'max:100','unique:tasks,title,'.request()->payload["id"]],
            'payload.content' => ['required'],
            'payload.status' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'payload.title.required' => "Title is required, Please fill out the required field!",
            'payload.title.max' => "Title may not be greater than 100 characters..",
            'payload.title.unique' => "This title has already been taken",
            'payload.content.required' => "Content is required, Please fill out the required field!",
            'payload.status.required' => "Status is required, Please fill out the required field!",
        ];
    }
}