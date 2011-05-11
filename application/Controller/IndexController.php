<?php

namespace SampleApp\Controller;
use WebFW\Controller;
use WebFW\Registry;
use WebFW\View;

class IndexController extends Controller
{
    public function indexAction($name = null) {
        return new View("hello.php", array("name" => $name));
    }
}
