<?php
// Copyright 2022 - 2023 D4.FR
/**
 * ActionMapper 
 **/

class Modules_Sshfspro_Model_ActionMapper extends Modules_Sshfspro_Model_Db
{
    public function get($id)
    {
        $sth = $this->_dbh->prepare('SELECT * FROM sshfs_config WHERE id = :id');
        $sth->bindParam('id', $id);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);

        while($row = $sth->fetch()) {
            return new Modules_Sshfspro_Model_Action($row);
        }
    }

    public function getAll($state)
    {
        $sth = $this->_dbh->prepare('SELECT * FROM sshfs_config ');
 
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $objects = array();
        while($row = $sth->fetch()) {
            $request = new Modules_Sshfspro_Model_Action($row);
            $objects[] = $request; 
        }

       
       

        return $objects;
    }

    public function getByRequestId($id, $names = false)
    {
        
        // recherche tout les configuration FS
        
        $sth = $this->_dbh->prepare('SELECT * FROM  sshfs_config WHERE id = :id');
        $sth->bindParam('id', $id);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);

        $objects = array();
        
        while ($row = $sth->fetch()) {
            $comment = new Modules_Sshfspro_Model_Action($row);
            $objects[] = $comment;
        }
                

        
        $objects = array();
        return $objects;
    } 

    public function save(Modules_Sshfspro_Model_Action $request)
    {

        
        if ($request->text === '') {
            return "Error on save: Text of comment is empty.";
        }

        if (is_null($request->id)) {
         
            // Creation configuration FS

           $sth = $this->_dbh->prepare("INSERT INTO sshfs_config(id,type_fs,host,port,login,password,ssh_key,path_local,path_remote) 
           values (null,:type_fs,:host,:port,:login,:password,:ssh_key,:path_local,:path_remote )");
           

         
            $sth->bindParam(':type_fs', $request->type_fs);
            $sth->bindParam(':host', $request->host);
            $sth->bindParam(':port', $request->port);
            $sth->bindParam(':login', $request->login);
            $sth->bindParam(':password', $request->password);
            $sth->bindParam(':ssh_key', $request->ssh_key);
            $sth->bindParam(':path_local', $request->path_local);
            $sth->bindParam(':path_remote', $request->path_remote);
            
            if ($sth === false) {
                $error = $this->_dbh->errorInfo();
                return "Error: code='{$error[0]}', message='{$error[2]}'.";
            }

        $res = $sth->execute(); 
        $res =  $sth->errorInfo();
        $Vl_last_id_id =$this->_dbh->lastInsertId();


            
        if (!$res) {
            $error = $sth->errorInfo();
            return "Error: code='{$error[0]}', message='{$error[2]}'.";
        }
        
        $Vl_return = array("0",$Vl_last_id_id);

        return  $Vl_return;
        
            
        } else {
            
                /*
                
                Update configuration FS.
                
                */
                

        
                $sth = $this->_dbh->prepare("UPDATE  sshfs_config SET host = :host , type_fs = :type_fs , port = :port , login = :login , password = :password , mount = :mount ,path_local = :path_local , path_remote = :path_remote WHERE id = :id ") ;
                
                $sth->bindParam(':id', $request->id);           
                $sth->bindParam(':type_fs', $request->type_fs);
                $sth->bindParam(':host', $request->host);
                $sth->bindParam(':port', $request->port);
                $sth->bindParam(':login', $request->login);
                $sth->bindParam(':password', $request->password);
                $sth->bindParam(':mount', $request->mount);
                $sth->bindParam(':path_local', $request->path_local);
                $sth->bindParam(':path_remote', $request->path_remote);
            
                $res = $sth->execute();

                
            
            if (!$res) {
                $error = $sth->errorInfo();
                return "Error: code='{$error[0]}', message='{$error[2]}'.";
            }

            return 0;
        }
    }

    public function remove($array_list_box)
    {   
    
        // supprimer la configuration FS.
    
        $in = implode(',', $array_list_box) ;
     

        $sth = $this->_dbh->prepare("DELETE from  sshfs_config where id in (" . $in . ")");
        
        
        $res = $sth->execute();
        
        if (!$res) {
            $error = $sth->errorInfo();
            return "Error: code='{$error[0]}', message='{$error[2]}'.";
        }
         
    
    return 0 ;
    
    }

    
}
