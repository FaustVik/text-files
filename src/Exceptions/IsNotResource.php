<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions;

/**
 * Class IsNotResource
 * @package FaustVik\Files\Exceptions
 */
class IsNotResource extends FileException
{
    public function __construct(string $message = '')
    {
        parent::__construct(sprintf("Type is not resource %s", $message));
    }
}
