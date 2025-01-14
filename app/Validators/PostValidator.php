<?php

namespace App\Validators;

use KrisnaAjieP\PHPValidator\Validator;

/**
 * Class PostValidator
 *
 * This class provides validation for post data. It ensures that the title, content, and tags
 * of a post meet the specified criteria before the post can be processed further.
 *
 * @package App\Validators
 */
class PostValidator
{
    /**
     * Validates the given data for creating or updating a blog post.
     *
     * @param array $data The data to be validated.
     * @return App\Helpers\Validator The validator instance with the applied rules.
     */
    public static function validate(array $data)
    {
        return Validator::setRules($data, [
            'title' => ['required', 'alpha_num_space', 'min_length:3', 'max_length:255'],
            'content' => ['required', 'max_length:65535'],
            'tags' => ['array_string']
        ]);
    }
}
