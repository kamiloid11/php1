<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StrongPasswordValidator extends ConstraintValidator
{
    private const MIN_LENGTH = 8;
    private const MIN_DIGITS = 2;
    private const MIN_LETTERS = 3;

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof StrongPassword) {
            throw new UnexpectedTypeException($constraint, StrongPassword::class);
        }

        if (!$value instanceof User) {
            return;
        }

        $password = (string)$value->getPlainPassword();

        //про пустой и слишком короткий пароль уже сказали NotBlank и Length на поле,
        //разбирать его состав дальше смысла нет: получился бы список из пяти замечаний сразу
        if (mb_strlen($password) < self::MIN_LENGTH) {
            return;
        }

        if ($this->matchesIdentity($password, $value)) {
            $this->addViolation($constraint->sameAsIdentity);
        }

        if (count(array_unique(mb_str_split($password))) === 1) {
            $this->addViolation($constraint->tooUniform);
        }

        if (preg_match_all('/\d/u', $password) < self::MIN_DIGITS) {
            $this->addViolation($constraint->notEnoughDigits, self::MIN_DIGITS);
        }

        if (preg_match_all('/\p{L}/u', $password) < self::MIN_LETTERS) {
            $this->addViolation($constraint->notEnoughLetters, self::MIN_LETTERS);
        }

        if (preg_match_all('/[^\p{L}\d]/u', $password) < 1) {
            $this->addViolation($constraint->notEnoughSpecial);
        }
    }

    private function matchesIdentity(string $password, User $user): bool
    {
        $password = mb_strtolower($password);

        foreach ([$user->getName(), $user->getEmail()] as $identity) {
            if ($identity !== null && $identity !== '' && mb_strtolower($identity) === $password) {
                return true;
            }
        }

        return false;
    }

    /**
     * ограничение висит на классе, поэтому сообщение вручную привязывается к полю пароля,
     * иначе оно вывелось бы над формой без привязки к строке
     */
    private function addViolation(string $message, ?int $count = null): void
    {
        $violation = $this->context->buildViolation($message)->atPath('plainPassword');

        if ($count !== null) {
            $violation->setParameter('{{ count }}', (string)$count);
        }

        $violation->addViolation();
    }
}
