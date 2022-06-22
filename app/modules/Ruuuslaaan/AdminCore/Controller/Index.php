<?php
declare(strict_types=1);

namespace Ruuuslaaan\AdminCore\Controller;

use Template;

class Index
{
    public function beforeroute($f3){
        $x = 0;
    }
    public function afterroute($f3){
        echo Template::instance()->render('layout.htm');
    }

    public function index($f3): void
    {
        $f3->set('inc','index.htm');
    }
}
