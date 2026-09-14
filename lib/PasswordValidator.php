<?php

/**
 * PasswordValidator - проверка сложности пароля с подсказками.
 * Для каждой ошибки возвращает подсказку, как её исправить.
 */
class PasswordValidator
{
    private int  $minLength;
    private bool $requireDigit;

    public function __construct(
        int  $minLength    = 8,
        bool $requireDigit = true
    ) {
        $this->minLength    = $minLength;
        $this->requireDigit = $requireDigit;
    }

    /**
     * Проверить пароль.
     *
     * @param string $password
     * @return array{
     *     valid: bool,
     *     issues: array<int, array{error: string, hint: string}>
     * }
     */
    public function validate(string $password): array
    {
        // Пустой пароль — отдельная ошибка
        if ($password === '') {
            return [
                'valid'  => false,
                'issues' => [[
                    'error' => 'Пароль не указан',
                    'hint'  => 'Введите пароль',
                ]],
            ];
        }

        $issues = [];
        $len    = mb_strlen($password, 'UTF-8');

        // Слишком короткий
        if ($len < $this->minLength) {
            $issues[] = [
                'error' => 'Пароль слишком короткий',
                'hint'  => 'Добавьте ещё ' . ($this->minLength - $len)
                         . ' символ(ов) — минимум ' . $this->minLength,
            ];
        }

        // Валидация на наличие цифр
        if ($this->requireDigit && !preg_match('/\d/u', $password)) {
            $issues[] = [
                'error' => 'Пароль без цифр',
                'hint'  => 'Добавьте хотя-бы одну цифру (0-9)',
            ];
        }

        return [
            'valid'  => empty($issues),
            'issues' => $issues,
        ];
    }

    public function isValid(string $password): bool
    {
        return $this->validate($password)['valid'];
    }
}