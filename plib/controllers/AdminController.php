<?php

// Copyright 2022 - 2023 D4FR

class AdminController extends Modules_Sshfspro_Controller
{
    public function indexAction()
    {

        // Create index page 
        // list setup mount
        // action add / delete


        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
 
            
            if(isset($post['action_fs_add']))
            {
            
            
            $Vl_type_fs = $post['type_fs'];
            
            $this->_redirect('/admin/addfs/'.$Vl_type_fs.''); 
            
            exit ; 
            
            }
            if(isset($post['action_fs_delete']))
            {
            
                        
            $params = $post ;

            $this->_requestMapper->remove(
                isset($post['chboxList']) ? $post['chboxList'] : array() ,
                Modules_Sshfspro_Model_Action
            );
            
            
                     
            }

            
            
        }
        
       

        $requests = $this->_requestMapper->getAll(Modules_Sshfspro_Model_Action);
        $this->view->list = $this->_createAndGetRequestList($requests);
    }
    
    

  

    public function infoAction()
    {
    
        //

    }


    public function addfsAction()
    {
        $this->view->uplevelLink = $this->_helper->url('index', 'admin');

        $form = new pm_Form_Simple();
        
        
        $Vl_uri = $_SERVER['REQUEST_URI'];
        $Vl_explode_fs = explode("/",$Vl_uri);
        $Vl_fs_value = $Vl_explode_fs['6'];
        
        
        $this->view->pageTitle = "SSHFS PRO  ";
        $this->view->typeFs =  $Vl_fs_value ;
                 
        $form->addElement('hidden', 'type_fs', array(
            'label' =>  pm_Locale::lmsg('type_fs'),
        
            'value' => $Vl_fs_value,
            'required' => true,
            'rows' => '255',
        ));
        
        
        $form->addElement('text', 'host', array(
            'label' =>  pm_Locale::lmsg('host'),
        
            'value' => $Vl_host_fs,
            'required' => true,
            'rows' => '255',
        ));
        
        $form->addElement('text', 'port', array(
           'label' => pm_Locale::lmsg('port'),
           'value' => $Vl_port_fs,
           'required' => true,
           'rows' => '6',
           'size'=>4, 
           'maxlength'=>4,
           'class'=>'numeric',
       ));
       
       $form->addElement('text', 'login', array(
           'label' =>  pm_Locale::lmsg('login'),
           'value' => $Vl_login_fs,
           'required' => true,
           'rows' => '64',
       ));
       
        
       
        $form->addElement('text', 'password', array(
           'label' =>  pm_Locale::lmsg('password'),
           'value' =>  $Vl_password_fs,
           'required' => true,
           'rows' => '64',
           ));
       
          
        
        $form->addElement('hidden', 'mount', array(
           'label' => 'mount',
           'value' => $Vl_mount,
           'required' => false,
           'rows' => '255',
       ));

       
        $form->addElement('hidden', 'path_local', array(
           'label' =>  pm_Locale::lmsg('path_local'),
           'value' => $Vl_path_local_fs,
           'required' => false,
           'rows' => '255',
       ));
       
       if ($Vl_fs_value == "sshfs" )
       {
         $form->addElement('text', 'path_remote', array(
           'label' =>  pm_Locale::lmsg('path_remote'),
           'value' => $Vl_path_remote_fs,
           'required' => true,
           'rows' => '255',
       ));

       }
       else
       {
        $form->addElement('hidden', 'path_remote', array(
            'label' =>  pm_Locale::lmsg('path_remote'),
            'value' => "-",
            'required' => true,
            'rows' => '255',
        ));
 
       } 
        
        
        
        $form->addControlButtons(array(
            'cancelLink' => pm_Context::getBaseUrl(),
        ));
        
        
        $form->addControlButtons(array(
            'cancelHidden' => true,
        ));
        

        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $formValues = $form->getValues();


           $Vl_id_fs =     $Vl_id_fs;
           $Vl_type_fs =    $formValues['type_fs'];
           $Vl_host_fs =   $formValues['host'];
           $Vl_port_fs =   $formValues['port'];
           $Vl_login_fs =   $formValues['login'];
           $Vl_password_fs =   $formValues['password'];
           $Vl_ssh_key_fs =   $formValues['ssh_key'];
           $Vl_mount =   $formValues['mount'];
           $Vl_path_local_fs =   $formValues['path_local'];
           $Vl_path_remote_fs =  $formValues['path_remote'];
           $Vl_created_at_fs = date('Y-m-d H:m:i');

            $params = array(
              'id' =>  $Vl_id_fs,
              'type_fs' => $Vl_type_fs,
              'host' =>  $Vl_host_fs,
              'port' => $Vl_port_fs ,
              'login' => $Vl_login_fs,
              'password' =>  $Vl_password_fs ,
              'mount' =>  $Vl_mount ,
              'ssh_key' =>  'NC',
              'path_local' => $Vl_path_local_fs,
              'path_remote' =>  $Vl_path_remote_fs,
              'created_at' =>  $Vl_created_at_fs,
            );


            

           $request = new Modules_Sshfspro_Model_Action($params);
                
           // save data configuration FS SQLITE

           $res = $this->_requestMapper->save($request); //
           
           // Last Id configuration FS SQLITE to create redirection
           // to configuration FS.

           $Vl_last_id_fs_created =  $res[1];

            
            if ($res[0] == 0 ) {
                
                $this->_status->addMessage('info', 'FS created');

                
                $this->_helper->json( ['redirect' => pm_Context::getBaseUrl()."index.php/admin/details/id/$Vl_last_id_fs_created" ] );

                
 
                
            } else {
                $this->_status->addMessage('error', $res);
            }
        }

        $this->view->form = $form;
    }

    public function detailsAction()
    {
    

        // Edit configuration FS if umount
        // 
    
        $this->view->pageTitle = "SSHFS PRO ";

        $this->view->uplevelLink = $this->_helper->url('index', 'admin');


        $id = $this->getRequest()->getParam('id');

        if (is_null($id)) {
            $this->_status->addMessage('error', 'Request id is undefined.');
            $this->_redirect('/admin/index');
        }

        
       $this->view->request = $this->_requestMapper->get($id);
        

       $Vl_id_fs =  $this->view->request->id;
       $Vl_id_user_fs =  $this->view->request->id_user;
       $Vl_type_fs =   $this->view->request->type_fs;
       $Vl_host_fs =   $this->view->request->host;
       $Vl_port_fs =   $this->view->request->port;
       $Vl_login_fs =   $this->view->request->login;
       $Vl_password_fs =   $this->view->request->password;
       $Vl_mount =   $this->view->request->mount;
       $Vl_ssh_key_fs =   $this->view->request->ssh_key;
       $Vl_path_local_fs =   $this->view->request->path_local;
       $Vl_path_remote_fs =   $this->view->request->path_remote;
       $Vl_created_at_fs =   $this->view->request->created_at;
        

        $this->view->pageTitle = "SSHFS PRO  ";


        $detailMapper = new Modules_Sshfspro_Model_ActionMapper();

        // Form
        $form = new pm_Form_Simple();
          
         
         
        $this->view->typeFs = $Vl_type_fs ;  
   
        $Vl_mount_s =  Modules_Sshfspro_Controller::getMountpointStatus($id) ;


        if($Vl_mount_s == 1 )       
        {
        
             $form->addElement('text', 'type_fs', array(
            'label' =>  pm_Locale::lmsg('type_fs'),
            'value' => $Vl_type_fs,
            'required' => true,
            'rows' => '255',  
        ));

        
        }
        else
        {
        
        
            $form->addElement('hidden', 'type_fs', array(
           'label' => pm_Locale::lmsg('type_fs'),
           'value' => $Vl_type_fs,
           'required' => true,
           'rows' => '64',
       ));


        }
        
      
       $form->addElement( 'checkbox', 'ssh_enable', [
            'label' => 'ssh_enable' ,
            'value' => $Vl_mount_s ? 0 : 1 ,
            
       ] );
       
        
        $form->addElement('text', 'host', array(
            'label' => pm_Locale::lmsg('host'),
            'value' => $Vl_host_fs,
            'required' => true,
            'rows' => '255',
            
        ));
        
        $form->addElement('text', 'port', array(
           'label' => pm_Locale::lmsg('port'),
           'value' => $Vl_port_fs,
           'required' => true,
           'rows' => '6',
           'size'=>4, 
           'maxlength'=>4,
           'class'=>'numeric',
       ));


       
       $form->addElement('text', 'login', array(
           'label' => pm_Locale::lmsg('login'),
           'value' => $Vl_login_fs,
           'required' => true,
           'rows' => '64',
       ));
        
        $Vl_password_fs           = pm_Crypt::decrypt( $Vl_password_fs );
           
        $form->addElement( 0 ? 'hidden' : 'password', 'password', [

        'label'          => pm_Locale::lmsg( 'password' ),
        'value'          => $Vl_password_fs,
        'class'          => 'f-middle-size',
        'required'       => false,
        'renderPassword' => true,
        ] );
    
      
       $form->addElement('hidden', 'mount', array(
           'label' => 'mount',
           'value' => $Vl_mount ? 0 : 1 ,
           'required' => true,
           'rows' => '255',
       ));
       
       
        $Vl_path_local_fs = "/mnt/nd$id";
            
        $form->addElement('text', 'path_local', array(
           'label' => pm_Locale::lmsg('path_local'),
           'value' => $Vl_path_local_fs,
           'required' => true,
           'rows' => '255',
       ));

       if ($Vl_type_fs == "sshfs" )
       {
            $form->addElement('text', 'path_remote', array(
            'label' => pm_Locale::lmsg('path_remote'),
            'value' => $Vl_path_remote_fs,
            'required' => true,
            'rows' => '255',
             ));
       }
       else
       {
        $form->addElement('hidden', 'path_remote', array(
            'label' => pm_Locale::lmsg('path_remote'),
            'value' => $Vl_path_remote_fs,
            'required' => true,
            'rows' => '255',
             ));
       }



        if(!$Vl_mount_s)
        {
        $form->addControlButtons( [
                                       'sendTitle'        => pm_Locale::lmsg( 'save' ),
                                       'sendHidden'       => false,
                                       'cancelTitle'      => $Vl_mount_s ? pm_Locale::lmsg( 'disable' ) : pm_Locale::lmsg( 'enable' ),
                                       'cancelLink'       => '',
                                       'cancelHidden'     => false,
                                       'withSeparator'    => true,
                                       'hideLegend'       => true,
                                       'presubmitHandler' => '',
                                     ] );
        }
        
        if($Vl_mount_s == 1)
        {
        
         $form->addControlButtons( [
                                       'sendTitle'        => pm_Locale::lmsg( 'save' ),
                                       'sendHidden'       => true,
                                       'cancelTitle'      => $Vl_mount_s ? pm_Locale::lmsg( 'disable' ) : pm_Locale::lmsg( 'enable' ),
                                       'cancelLink'       => '',
                                       'cancelHidden'     => false,
                                       'withSeparator'    => true,
                                       'hideLegend'       => true,
                                       'presubmitHandler' => '',
                                     ] );

        
        }

        $this->view->form = $form;

       
       if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
       
       
       
       $formValues = $form->getValues();
       
       
       $Vl_id_fs =     $Vl_id_fs;       
       $Vl_type_fs =    $formValues['type_fs'];
       $Vl_host_fs =   $formValues['host'];
       $Vl_port_fs =   $formValues['port'];
       $Vl_login_fs =   $formValues['login'];
       $Vl_password_fs =   $formValues['password'];
       $Vl_ssh_key_fs =   $formValues['ssh_key'];
       $Vl_mount =   $formValues['mount'];
       $Vl_path_local_fs =   $formValues['path_local'];
       $Vl_path_remote_fs =  $formValues['path_remote'];
       $Vl_created_at_fs =   $formValues['created_at'];

       
            $params = array(
              'id' =>  $Vl_id_fs,
              'type_fs' => $Vl_type_fs,
              'host' =>  $Vl_host_fs,
              'port' => $Vl_port_fs ,
              'login' => $Vl_login_fs,
              'password' =>  $Vl_password_fs ,
              'ssh_key' =>  '2121',
              'mount' =>  $Vl_mount_update ,
              'path_local' => $Vl_path_local_fs,
              'path_remote' =>  $Vl_path_remote_fs,
              'created_at' => '2022-11-12 00:00:00',
            );
           
            $config_fs = new Modules_Sshfspro_Model_Action($params);
                    
            $res = $detailMapper->save($config_fs);
                        
            $Vl_mount_stats =  Modules_Sshfspro_Controller::getMountpointStatus($Vl_id_fs) ;

            $Vl_ssh_enable = $form->getValue( 'ssh_enable' )  ; 
            
           if( $form->getValue( 'ssh_enable' ) == 1 )
            {
            

            $Vl_password_fs = pm_Crypt::encrypt($Vl_password_fs) ;

               
              
              if(!$Vl_mount_stats)
              {
              

              
              $params = array(
              'id' =>  $Vl_id_fs,
              'type_fs' => $Vl_type_fs,
              'host' =>  $Vl_host_fs,
              'port' => $Vl_port_fs ,
              'login' => $Vl_login_fs,
              'password' =>  $Vl_password_fs ,
              'ssh_key' =>  '2121',
              'mount' =>  1 ,
              'path_local' => $Vl_path_local_fs,
              'path_remote' =>  $Vl_path_remote_fs,
              'created_at' => '2022-11-12 00:00:00',
            );


              
              $config_fs = new Modules_Sshfspro_Model_Action($params);
              
              $mount = Modules_Sshfspro_Controller::mount($params);
             
              $Vl_mount_stats =  Modules_Sshfspro_Controller::getMountpointStatus($Vl_id_fs) ;
              
              if($Vl_mount_stats == 1)
              {
               $res = $detailMapper->save($config_fs);
               $this->_status->addMessage('info', pm_Locale::lmsg('mount'));
              }

              }
            
            
            }
            else
            {
              if($Vl_mount_stats == 1)
              {
            
              $Vl_password_fs = pm_Crypt::encrypt($Vl_password_fs) ;
                       
              $params = array(
              'id' =>  $Vl_id_fs,
              'type_fs' => $Vl_type_fs,
              'host' =>  $Vl_host_fs,
              'port' => $Vl_port_fs ,
              'login' => $Vl_login_fs,
              'password' =>  $Vl_password_fs ,
              'ssh_key' =>  '2121',
              'mount' =>  0 ,
              'path_local' => $Vl_path_local_fs,
              'path_remote' =>  $Vl_path_remote_fs,
              'created_at' => '2022-11-12 00:00:00',
              );



              $config_fs = new Modules_Sshfspro_Model_Action($params);

            
              $mount = Modules_Sshfspro_Controller::unmount($Vl_id_fs); 
           
              $res = $detailMapper->save($config_fs) ; 
             
              $this->_status->addMessage('info', pm_Locale::lmsg('umount'));

            
              }
           
            }
            
                     
            
            if ($res === 0 ) {
            
                $this->_status->addMessage('info', pm_Locale::lmsg('fs_created'));
            
            } else {
                $this->_status->addMessage('error', $res);
            }
            
            $this->_helper->json(array('redirect' => $id));
        }
        
        
    }
}

