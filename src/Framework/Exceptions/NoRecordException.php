<?php

namespace Framework\Exceptions;

use Exception;

class NoRecordException extends Exception
{

    public function __construct($table, $id)
    {
        $this->message = "Aucun enregistrement ne corressponds a l\'id $id dans la table $table";
    }
}
