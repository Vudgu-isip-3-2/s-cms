<?php
/**
 * Валидатор паролей для задачи #91
 */
class PasswordValidator {
    
    private static $minLength = 8;
    private static $commonPasswords = ['123456', 'password', 'qwerty', '111111', 'admin'];

    /**
     * Проверка пароля
     * @param string $password
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validate(string $password): array {
        $errors = [];

        if (empty(trim($password))) {
            return ['valid' => false, 'errors' => ['Пароль не может быть пустым']];
        }

        if (strlen($password) < self::$minLength) {
            $errors[] = "Минимальная длина: " . self::$minLength . " символов";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Нужна хотя бы одна заглавная буква";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Нужна хотя бы одна строчная буква";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Нужна хотя бы одна цифра";
        }

        if (!preg_match('/[@$!%*?&#^]/', $password)) {
            $errors[] = "Нужен хотя бы один спецсимвол (@$!%*?&#^)";
        }

        if (in_array(strtolower($password), self::$commonPasswords)) {
            $errors[] = "Пароль слишком простой и распространённый";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}