<?php

namespace Multiple\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class ProductsController extends Controller
{
    public function indexAction()
    {
        $this->view->t = $this->translator;
        $this->assets->addJs("/assets/js/main.js");
        $mongo = new \App\Components\MongoComponent();
        // if got post
        if ($this->request->isPost()) {
            // pass searched data into view
            $this->view->search = $mongo->query("products", ["product name" => ['$regex' => strtolower($this->request->getPost()["search"])]]);
            $this->view->query = $this->request->getPost()["search"];
        }
        // pass data into view
        $this->view->data = $mongo->read("products", []);
    }
    public function detailsAction()
    {
        $this->view->t = $this->translator;
        $id = explode("/", $this->request->getQuery()["_url"])[3];
        if (strlen($id) == 0) {
            header("location:/products");
        }
        $mongo = new \App\Components\MongoComponent();
        $data = $mongo->query("products", [
            '_id' => new \MongoDB\BSON\ObjectID($id)
        ]);
        // pass data into view
        $this->view->data = $data->toArray();
    }

}
