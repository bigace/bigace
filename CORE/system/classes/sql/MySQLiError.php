<?php

import('api.sql.DBError');

class MySQLiError implements DBError
{
    private $msg;
    private $number;

    function MySQLiError($number, $msg) {
        $this->number = $number;
        $this->msg = $msg;
    }

    /**
     * Returns the error number
     * @return int the database specific error number
     */
    function getNumber() {
        return $this->number;
    }

    /**
     * Gets the error message
     * @return mixed the error message
     */
    function getMessage() {
        return $this->msg;
    }
}
