<?php
// Copyright 2022 2023 D4.FR
/**
 * Model Comment
 **/
class Modules_Sshfspro_Model_Action extends Modules_Sshfspro_Model_Db
{
   
   // const STATE = 0;

    protected $_data = array(
              'id' =>  null,
              'type_fs' =>  null,
              'host' =>  null,
              'port' => null ,
              'login' => null,
              'password' =>  null ,
              'ssh_key' =>  null,
              'mount' =>  null,
              'path_local' => null,
              'path_remote' =>  null,
            );

    
    
    public function __construct($parameters = array())
    {
        parent::__construct();
        foreach($parameters as $param => $value) {
            $this->_data[$param] = $value;
        }
    }

    public function __get($field)
    {
        if(isset($this->_data[$field])) {
            return $this->_data[$field];
        }

        return null;
    }
}
