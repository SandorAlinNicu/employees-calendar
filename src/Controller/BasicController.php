<?php
/**
 * Created by PhpStorm.
 * User: Kali
 * Date: 05.09.2019
 * Time: 13:16
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class BasicController extends AbstractController
{
    public $menus;

    public function __construct(Security $security)
    {
        $user = $security->getUser();

        $this->menus =
            [
                'leftMenu' => array(
                    array(
                        'path' => 'homepage',
                        'label' => 'Home'
                    ),
                    array(
                        'path' => 'holiday-request',
                        'label' => 'Request Holiday'
                    ),
                    array(
                        'path' => 'calendar',
                        'label' => 'Calendar',
                    ),
                    array(
                        'path' => 'users',
                        'label' => 'Users',
                        'roles' => ['ROLE_ADMIN']
                    ),
                    array(
                        'path' => 'departments',
                        'label' => 'Departments',
                        'roles' => ['ROLE_ADMIN']
                    ),
                    array(
                        'path' => 'requests',
                        'label' => 'Requests',
                        'roles' => ['ROLE_ADMIN', 'ROLE_MANAGER']
                    )
                ),
                'rightMenu' => array(
                    array(
                        'path' => 'app_register',
                        'label' => 'Sign Up',
                        'icon' => ['glyphicon', 'glyphicon-user'],
                        'roles' => ['ANONYMOUS'],
                    ),
                    array(
                        'path' => 'app_login',
                        'label' => 'Login',
                        'icon' => ['glyphicon', 'glyphicon-log-in'],
                        'roles' => ['ANONYMOUS'],
                    ),
                    array(
                        'path' => 'app_logout',
                        'label' => 'Logout',
                        'roles' => ['ROLE_USER'],
                        'icon' => ['glyphicon', 'glyphicon-log-out']
                    ),
                ),
            ];
        foreach ($this->menus as $name => &$menu) {
            $menu = array_filter($menu, function ($menuItem) use ($user) {
                if (isset($menuItem['roles'])) {
                    if ($user) {
                        return (count(array_intersect($menuItem['roles'], $user->getRoles())));
                    }
                    elseif(in_array('ANONYMOUS', $menuItem['roles'])) {
                        return true;
                    }
                    return false;
                }
                return true;
            });
        }

    }

    public function render(string $view, array $parameters = [], Response $response = null): Response {
        $parameters = $parameters + $this->menus;
        return parent::render($view, $parameters, $response);
    }

}