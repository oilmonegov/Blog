<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can comment
        if (! $this->user()) {
            return false;
        }

        // Check if user can create comments
        return $this->user()->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:5000'],
            'post_id' => ['required', 'integer', Rule::exists('posts', 'id')],
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
            'content.required' => 'The comment content is required.',
            'content.min' => 'The comment must be at least 3 characters.',
            'content.max' => 'The comment may not be greater than 5000 characters.',
            'post_id.required' => 'The post ID is required.',
            'post_id.exists' => 'The selected post does not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Verify the post exists and is published (or user has permission to view it)
            if ($this->filled('post_id')) {
                $post = Post::find($this->post_id);
                if ($post && ! $this->user()->can('view', $post)) {
                    $validator->errors()->add('post_id', 'You cannot comment on this post.');
                }
            }
        });
    }
}

