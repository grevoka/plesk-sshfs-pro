<?php

// Copyright 2022 - 2023 D4.FR


if (method_exists('pm_ApiCli', 'callSbin')) {
        

        // Find File system are mounted to umount
        // search type FUSE and FUSE.SSHFS
        $Vl_json_fuse = shell_exec('findmnt -t fuse -J');
        $Vl_json_fuse_sshfs = shell_exec('findmnt -t fuse.sshfs -J') ;
        $Vl_output_fuse = json_decode($Vl_json_fuse,true) ;
        $Vl_output_fuse_sshfs = json_decode($Vl_json_fuse_sshfs,true) ;
        $Vl_valeur_mount_array_fuse = $Vl_output_fuse['filesystems'] ;
        $Vl_valeur_mount_array_fuse_sshfs = $Vl_output_fuse_sshfs['filesystems'] ;
        
        if(!empty($Vl_valeur_mount_array_fuse) && !empty($Vl_valeur_mount_array_fuse_sshfs) )
        {
        $Vl_valeur_mount_array_mix = array_merge($Vl_valeur_mount_array_fuse,$Vl_valeur_mount_array_fuse_sshfs);
        }

        // If resultat is unique by FUSE findmnt

        if(!empty($Vl_valeur_mount_array_fuse))
        {
         
        $Vl_valeur_mount_array_mix  = $Vl_valeur_mount_array_fuse ;

        }
         
        if(!empty($Vl_valeur_mount_array_fuse_sshfs))
        {

        $Vl_valeur_mount_array_mix  = $Vl_valeur_mount_array_fuse_sshfs ;

        }


        // Count number of resultat
        $Vl_count_array = count($Vl_valeur_mount_array_mix) ;

        // Search and find file system from SSHFS
        for ($i=0; $i<$Vl_count_array; $i++) {

                $Vl_string_mount = $Vl_valeur_mount_array_mix[$i]["target"] ;

                $Vl_find = "sshfs" ;

                if (strpos($Vl_string_mount, $Vl_find) !== false) {

                echo 'File system are configured by SSHFS';
                echo 'Force umount FS' ;

                $result = shell_exec("sudo fusermount -u $Vl_string_mount");
                echo "sudo fusermount -u $Vl_string_mount \n" ;
                $result = shell_exec("rm -rf $Vl_string_mount");   
                echo "sudo 'rm -rf'".$Vl_string_mount."";

                } else {

                echo 'File system are not configure by SSHFS';

                }  

        }
        



        $result = pm_ApiCli::callSbin('uninstall');
}




