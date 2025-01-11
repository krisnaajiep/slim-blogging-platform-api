<?php

namespace App\Validators;

use App\Helpers\Validator;

class PostValidator
{
    public static function validate(array $data)
    {
        return Validator::setRules($data, [
            'title' => ['required', 'alpha_num_space', 'min_length:3', 'max_length:255'],
            'content' => ['required', 'max_length:65535'],
            'tags' => ['array_string']
        ]);
    }
}
