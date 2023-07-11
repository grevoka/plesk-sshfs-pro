<?php

// Copyright 2022 - 2023 D4.FR

abstract class Modules_Sshfspro_Model_Db
{
    protected $_dbh = null;

    const DATABASE_FILE = 'sshfs.db';

    public function __construct()
    {
        $this->_dbh = new PDO('sqlite:' . pm_Context::getVarDir() . DIRECTORY_SEPARATOR . self::DATABASE_FILE);
    }
}
