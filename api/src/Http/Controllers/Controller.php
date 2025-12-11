<?php

namespace App\Http\Controllers;

use App\Http\Request\Response;

class Controller
{
    public function __construct()
    {
        // Código comum para todos os controladores pode ser adicionado aqui
    }

    public function respondJson(array $data = [], int $statusCode = 200)
    {
        Response::json($data, $statusCode);
    }
}
