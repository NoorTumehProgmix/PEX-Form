<?php


namespace Juzaweb\CMS\Exceptions;

class FileManagerException extends \Exception
{
    /**
     * @param array $errors
     */
    public function __construct($errors)
    {
        parent::__construct(implode("\n", $errors));
    }
}
