<?php

namespace App\Controller;

use App\Framework\Entity\BaseController;
use App\Framework\Route\Route;

class PostController extends BaseController
{
    #[Route('/', name: "app_mes_couilles", methods: ['GET'])]
    public function index()
    {
        echo "merde...";
    }

    #[Route("/post/{id}")]
    public function showOne(string $id)
    {
        var_dump($id);
    }
}
