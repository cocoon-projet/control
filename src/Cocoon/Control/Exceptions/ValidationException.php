<?php
declare(strict_types=1);

namespace Cocoon\Control\Exceptions;

/**
 * Exception levée lors d'une erreur de validation
 */
class ValidationException extends \Exception
{
    /**
     * Messages d'erreur de validation
     */
    private array $errors = [];

    /**
     * Constructeur
     *
     * @param array<string, string> $errors Messages d'erreur
     * @param string $message Message d'erreur principal
     * @param int $code Code d'erreur
     * @param \Throwable|null $previous Exception précédente
     */
    public function __construct(
        array $errors,
        string $message = 'Validation failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Retourne les messages d'erreur de validation
     *
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
