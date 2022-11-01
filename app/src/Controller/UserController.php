<?php

namespace App\Controller;

use App\Framework\Entity\BaseController;
use App\Framework\Route\Route;

class UserController extends BaseController
{
    #[Route('/user', name: "app_user")]
    public function index()
    {
        // TODO
    }
}
