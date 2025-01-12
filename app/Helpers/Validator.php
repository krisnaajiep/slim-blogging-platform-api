<?php

namespace App\Helpers;

/**
 * This class provides a set of validation rules for validating input data.
 * It supports various validation rules such as required, alpha, alpha_num, 
 * alpha_num_space, array_string, num, lowercase, min_length, max_length, 
 * numeric, email, match, phone_number, and date.
 * It also provides methods to set validation errors, check for validation 
 * errors, and retrieve validated data.
 */
class Validator
{
    /**
     * Array to store validation errors and validated data
     *
     * @var array $validation_errors Array to store validation errors
     * @var array $validated_data Array to store validated data
     */
    public static array $validation_errors = [], $validated_data = [];

    /**
    /**
     * Validates the given input based on the specified rules.
     *
     * @param array $input The input data to be validated.
     * @param array $rules The validation rules to apply.
     * @return object Returns an instance of the Validator class.
     */
    public static function setRules(array $data, array $rules): object
    {

        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;

            foreach ($ruleset as $rule) {
                if (!isset(self::$validation_errors[$field])) {
                    $validated_value = self::validate($value, $rule, $field, $data);
                }
            }

            self::$validated_data[$field] = $validated_value ?? null;
        }

        return new Validator();
    }

    /**
     * Validates a single value against a specific rule.
     *
     * @param mixed $value The value to be validated.
     * @param string $rule The validation rule to apply.
     * @param string $field The name of the field being validated.
     * @param array $data The entire input data array for cross-field validation.
     * @return void
     */
    public static function validate($value, string $rule, string $field, array $data)
    {
        if ($rule === "required" && empty($value)) {
            self::setValidationError($field, $field . " field is required.");
        }

        if (!empty($value)) {
            if ($rule === "alpha" && !preg_match("/^[a-zA-Z\s'-]+$/", $value)) {
                self::setValidationError($field, $field . " input may only contain letters, spaces, apostrof (') and hyphens (-).");
            }

            if ($rule === "alpha_num") {
                if (!preg_match("/^[a-zA-Z0-9._-]+$/", $value)) {
                    self::setValidationError($field, $field . " input may only contain letters, numbers, periods (.), underscores (_), and hyphens (-).");
                }
            }

            if ($rule === "alpha_num_space") {
                if (!preg_match("/^[a-zA-Z0-9. _-]+$/", $value)) {
                    self::setValidationError($field, $field . " input may only contain letters, spaces, numbers, periods (.), underscores (_), and hyphens (-).");
                }
            }

            if ($rule === "array_string") {
                if (!is_array($value)) {
                    self::setValidationError($field, $field . ' input must be a valid array.');
                } else {
                    foreach ($value as $val) {
                        if (!is_string($val)) {
                            self::setValidationError($field, $field . " input array must contain only strings.");
                        }

                        if (empty($val)) {
                            self::setValidationError($field, $field . " input array must not contain empty strings.");
                        }
                    }
                }
            }

            if ($rule === "num" && !is_numeric($value)) {
                self::setValidationError($field, $field . " input must be numeric");
            }

            if ($rule === "lowercase" && $value !== strtolower($value)) {
                self::setValidationError($field, $field . " input letters must be lowercase.");
            }

            if (strpos($rule, "min_length") !== false && strpos($rule, ":") !== false) {
                $rule = explode(":", $rule)[1];

                if (strlen($value) < (int)$rule) {
                    self::setValidationError($field, $field . " input must be at least {$rule} characters long.");
                }
            }

            if (strpos($rule, "max_length") !== false && strpos($rule, ":") !== false) {
                $rule = explode(":", $rule)[1];

                if (strlen($value) > (int)$rule) {
                    self::setValidationError($field, $field . " input must not exceed {$rule} characters.");
                }
            }

            if ($rule === "numeric" && !is_numeric($value)) {
                self::setValidationError($field, $field . " input must be numeric.");
            }

            if ($rule === "email") {
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);

                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    self::setValidationError($field, $field . " input must be a valid email address.");
                }
            }

            if (strpos($rule, "match") !== false && strpos($rule, ":") !== false) {
                $match = explode(":", $rule);
                if ($value !== $data[$match[1]]) {
                    self::setValidationError($field, $field . " doesn't match.");
                }
            }

            if ($rule === "phone_number" && !preg_match("/^08[0-9]{10,12}$/", $value)) {
                self::setValidationError($field, $field . " input must be a valid phone number.");
            }

            if ($rule === "date") {
                if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
                    self::setValidationError($field, $field . " input format must be Y-m-d.");
                    return;
                }

                $parts = explode("-", $value);
                if (!checkdate($parts[1], $parts[2], $parts[0])) {
                    self::setValidationError($field, $field . " input must be a valid date.");
                }
            }
        }

        return $value;
    }

    /**
     * Sets a validation error message for a specific field.
     *
     * @param string $field The name of the field that has the validation error.
     * @param string $message The validation error message to be set.
     * @return void
     */
    public static function setValidationError(string $field, string $message): void
    {
        self::$validation_errors[$field] = $message;
    }


    /**
     * Check if there are any validation errors.
     *
     * This method returns a boolean indicating whether there are any
     * validation errors present in the static property $validation_errors.
     *
     * @return bool True if there are validation errors, false otherwise.
     */
    public function hasValidationErrors(): bool
    {
        return !empty(self::$validation_errors);
    }

    /**
     * Checks if there is a validation error for a specific field.
     *
     * @param string $field The name of the field to check for validation errors.
     * @return bool Returns true if there is a validation error for the field, false otherwise.
     */
    public static function hasValidationError(string $field): bool
    {
        return isset(self::$validation_errors[$field]);
    }

    /**
     * Retrieves the validation errors and resets the validation errors array.
     *
     * @return array An array of validation errors.
     */
    public function getValidationErrors(): array
    {
        $validation_errors = self::$validation_errors ?? "";
        self::$validation_errors = [];

        return $validation_errors;
    }

    /**
     * Retrieve the validated data if there are no validation errors.
     *
     * This method checks if there are any validation errors. If there are no errors,
     * it returns the validated data. Otherwise, it returns an empty array.
     *
     * @return array The validated data or an empty array if there are validation errors.
     */
    public function validated(): array
    {
        return empty(self::$validation_errors) ? self::$validated_data : [];
    }
}
