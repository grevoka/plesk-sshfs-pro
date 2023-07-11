<?php
// Copyright 2022 2023 D4.FR
class Modules_Sshfspro_Controller extends pm_Controller_Action
{

    protected $_requestMapper = null;

    public function init()
    {
        parent::init();

        // Menu / Ongle
        
        $this->view->tabs = array(
            array(
                'title' => pm_Locale::lmsg('manage_fs_mount_point'),
                'action' => 'index'
            ),
           // array(
           //     'title' => pm_Locale::lmsg('about'),
           //     'action' => 'info',
           // )
        );
        
        
        $session = new pm_Session();
        
        $this->client = $session->getClient();

        $this->view->pageTitle = "SSHFS PRO";

        $this->_requestMapper = new Modules_Sshfspro_Model_ActionMapper();
    }
    

    protected function _createAndGetRequestList($requests)
    {
        // Create list configuration FS for Index
      
        
        $data = array();
        foreach ($requests as $request) {
            $columns = array();
            if($request->mount == 1)
            {
              $columns['id'] = "<input type=\"checkbox\" value=\"$request->id\" name=\"chboxList[]\"  disabled=\"disable\">";
            }
            else
            {
              $columns['id'] = "<input type=\"checkbox\" value=\"$request->id\" name=\"chboxList[]\" >";
            }
            
            
            if($request->mount == 1)
            {
             $action ="green";
            }
            
            if($request->mount == 0)
            {
              $action ="red";
            }
            
            if($request->type_fs == "sftp-fs" || $request->type_fs == "webdav" )
            {
            
              $request->path_remote  = " - " ;
            
            }
            
            
            $Vl_picto_action  = "<img src=\"".pm_Context::getBaseUrl()."images/$action.png\">";
            
            $columns['mount'] = $Vl_picto_action ;

            $columns['host'] = $request->host;
            $columns['login'] = $request->login;
            $columns['path_local'] = $request->path_local;
            $columns['path_remote'] = $request->path_remote;
            $columns['type_fs'] = $request->type_fs;
            $columns['created_at'] = $request->created_at;
            
            
            $columns['details'] = "<a href=\"" . $this->_helper->url(
                'details', $this->getRequest()->getControllerName(), null, array('id' => $request->id))
                ."\" >".pm_Locale::lmsg('edit')."</a>";
                 
            $data[] = $columns;
        }
        
        
        $columns = array();
        $columns['id'] = array('title' => '','noEscape' => true);
        $columns['mount'] = array('title' => ''.pm_Locale::lmsg('mount_state').'', 'noEscape' => true);
        $columns['host'] = array('title' => ''.pm_Locale::lmsg('host').'', 'noEscape' => true,'sortable' => false);
        $columns['login'] = array('title' => ''.pm_Locale::lmsg('login').'', 'noEscape' => true);
        $columns['path_local'] = array('title' => ''.pm_Locale::lmsg('path_local').'');
        $columns['path_remote'] = array('title' => ''.pm_Locale::lmsg('path_remote').'');
        $columns['type_fs'] = array('title' => ''.pm_Locale::lmsg('type_fs').'');
        $columns['details'] = array('title' => '', 'noEscape' => true);


        // Create list helper and pass data and column to it
        $list = new pm_View_List_Simple($this->view, $this->_request);
        $list->setData($data);
        $list->setColumns($columns);
        
        return $list;
    }
    
     public static function mount($params){
     
     // Create Mount point for all FS
     
     $id	  = $params["id"]  ;     
     $host        = $params["host"]  ;
     $port        = $params["port"]  ;
     $user        = $params["login"]  ;
     $remote_path = $params["path_remote"]  ;
     $passwd      = $params["password"]  ;
     $type_fs	  = $params["type_fs"];
     
     $passwd = pm_Crypt::decrypt( $passwd );
    

    
    if($type_fs == "sshfs")
    {
    // SSHFS 
    
      if( empty( $port ) ){
        // default port
        
        $port = "22";

      }

    $cmd = "mkdir -p /mnt/sshfs/nd$id |  echo '{$passwd}' | sshfs  $user@{$host}:{$remote_path} /mnt/sshfs/nd$id/ -p  $port -o reconnect -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o nonempty -o allow_other -o password_stdin ";
    
    }

    if($type_fs == "sftp-fs")
    {
    // FTPFS

      if( empty( $port ) ){
        // default port
        
        $port = "21";

      }
    
    $cmd = "mkdir -p /mnt/sshfs/nd$id |  /usr/bin/curlftpfs {$host} /mnt/sshfs/nd$id   -o allow_root  -o ssl,no_verify_peer,no_verify_hostname,user={$user}:{$passwd}";
    
    }
    
    if($type_fs == "webdav")
    {

    // WEBDAV
    
    // Le securité a été realisé dans l'espace sudouser seul mount.davfs est autorisé.
    
    $cmd = "mkdir -p /mnt/sshfs/nd$id |  echo '$passwd' | sudo  mount.davfs  -ousername=$user  $host/remote.php/dav/files/$user/ /mnt/sshfs/nd$id" ;

    }
    
    
    return self::command( $cmd );
    
  }
  
    public static function unmount($Vl_id_fs){
    
    return self::command( "sudo fusermount -u /mnt/sshfs/nd$Vl_id_fs/" );
  
  }
  
  
  public static function getMountpointStatus($Vl_id_fs){
    
    $cmd        = self::command( 'df' );
    $search_str = strpos( $cmd, '/mnt/sshfs/nd'.$Vl_id_fs.'' );
    
    
    return $search_str !== false;
    
  }
  
  
    public static function command( $cmd ){
    $response = '';
    $handle   = popen( "$cmd 2>&1", 'r' );
    
    if( $handle ){
      while( feof( $handle ) === false ){
        $response .= fgets( $handle );
      }
      
      pclose( $handle );
    } else {
      
      return ['error' => "Error exec command."];
      
    }
    
    
    return $response;
  }
}
