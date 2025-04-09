<?php
declare(strict_types=1);

namespace Cocoon\Control\Exceptions;

/**
 * Exception levée lorsqu'une règle de validation n'est pas trouvée
 */
class RuleNotFoundException extends ValidationException
{
    /**
     * Constructeur
     *
     * @param string $rule Nom de la règle non trouvée
     * @param int $code Code d'erreur
     * @param \Throwable|null $previous Exception précédente
     */
    public function __construct(
        string $rule,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            ['rule' => "La règle de validation '{$rule}' n'existe pas"],
            "Rule '{$rule}' not found",
            $code,
            $previous
        );
    }
} 