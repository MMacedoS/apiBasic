<?php

namespace App\Http\Controllers\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Users\IUserRepository;

class UserController extends Controller
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $users = $this->userRepository->findAll();
        return $this->respondJson([
            'message' => 'Lista de usuários',
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        return $this->respondJson([
            'message' => 'Usuário criado com sucesso',
            'data' => $data
        ], 201);
    }

    public function show(Request $request, $id)
    {
        return $this->respondJson([
            'message' => 'Detalhes do usuário',
            'data' => ['id' => $id]
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        return $this->respondJson([
            'message' => 'Usuário atualizado com sucesso',
            'data' => array_merge(['id' => $id], $data)
        ]);
    }

    public function destroy(Request $request, $id)
    {
        return $this->respondJson([
            'message' => 'Usuário removido com sucesso',
            'data' => ['id' => $id]
        ]);
    }
}
