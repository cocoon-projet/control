<?php
declare(strict_types=1);

namespace Cocoon\Control\Exceptions;

/**
 * Exception levée lorsqu'un fichier de langue n'est pas trouvé
 */
class LanguageFileNotFoundException extends ValidationException
{
    /**
     * Constructeur
     *
     * @param string $file Chemin du fichier de langue
     * @param int $code Code d'erreur
     * @param \Throwable|null $previous Exception précédente
     */
    public function __construct(
        string $file,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            ['file' => "Le fichier de langue '{$file}' n'existe pas"],
            "Language file '{$file}' not found",
            $code,
            $previous
        );
    }
} 