<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions;

/**
 * Class IsNotResourceException.
 */
final class IsNotResourceException extends FileBaseException
{
    public function __construct()
    {
        parent::__construct('Type is not resource');
    }
}
