<?php

namespace Multiple\Backend\Controllers;

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->session->has("email")) {
            header("location:/admin");
        }
        // if got post
        if ($this->request->isPost()) {
            $postData = $this->request->getPost();
            // check login creds
            $mongo = new \App\Components\MongoComponent();
            $resp = $mongo->read(
                'users',
                [
                    'username' => $postData["email"],
                    'password' => $postData["password"],
                ]
            );
            if (count($resp->toArray()) == 1) {
                echo "set session";
                $this->session->set("email", $postData["email"]);
                header("location:/admin");
            } else {
                echo "login details are incorrect";
                die;
            }
        }
    }
}
