<?php

// Copyright 2022 2023 D4.FR

class IndexController extends pm_Controller_Action
{
    public function indexAction()
    {

        $session = new pm_Session();
        $client = $session->getClient();

        if ($client->isAdmin()) {
            $this->_redirect('/admin/index');
        } else {
            echo "No access.";
        }

    }
}
