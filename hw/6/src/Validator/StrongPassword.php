<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * ограничение проверяет пароль целиком, поэтому ставится на класс, а не на поле:
 * валидатору нужны ещё имя и почта, чтобы сравнить их с паролем
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class StrongPassword extends Constraint
{
    public string $sameAsIdentity = 'пароль не должен повторять имя или почту';
    public string $tooUniform = 'пароль не должен состоять из одинаковых символов';
    public string $notEnoughDigits = 'в пароле меньше {{ count }} цифр';
    public string $notEnoughLetters = 'в пароле меньше {{ count }} букв';
    public string $notEnoughSpecial = 'в пароле нет ни одного специального символа';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
