<?php

namespace Framework\Exceptions;

use Exception;

class NoRecordException extends Exception
{

    public function __construct(?string $table = null, ?int $id = null)
    {
        $this->message = "Aucun enregistrement ne corressponds a l\'id $id dans la table $table";
    }
}
