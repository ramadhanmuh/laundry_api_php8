<?php

namespace Src\Routes;

use Src\Systems\Router;

class AllRoutes extends Router
{
    static function getList() {
        $list = [];
        // switch (parent::$segments[0]) {
        //     case 'owner':
        //         $route  = new Owner;
        //         $list[] = $route->getList();
        //         break;
        //     case 'manager':
        //         $route  = new Manager;
        //         $list[] = $route->getList();
        //         break;
        //     case 'employee':
        //         $route  = new Employee;
        //         $list[] = $route->getList();
        //         break;
        //     default:
        //         $list[] = Users::$list;
        //         $list[] = Application::$list;
        //         break;
        // }

        return $list;
    }
}
