<?php

namespace Multiple\Backend\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        if (!$this->session->has("email")) {
            header("location:/login");
        }
        header("location:/admin/products");
    }
}
