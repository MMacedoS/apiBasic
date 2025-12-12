<?php

namespace App\Http\Controllers\v1\Users;

use App\Http\Controllers\Controller;
use App\Http\JWT\JWT;
use App\Http\Request\Request;
use App\Repositories\Contracts\Users\IUserRepository;
use App\Transformers\Users\UserTransformer;

class UserController extends Controller
{
    private IUserRepository $userRepository;
    private UserTransformer $userTransformer;

    public function __construct(
        IUserRepository $userRepository,
        UserTransformer $userTransformer
    ) {
        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;
    }

    public function index(Request $request)
    {
        $users = $this->userRepository->findAll();
        $users = $this->userTransformer->transformCollection($users);

        return $this->respondJson([
            'message' => 'Lista de usuários',
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $userTransformed = $this->userTransformer->keysTransform($validatedData);

        $user = $this->userRepository->create($userTransformed);

        if (is_null($user)) {
            return $this->respondJson([
                'message' => 'Erro ao criar usuário'
            ], 500);
        }

        $user = $this->userTransformer->transform($user);

        return $this->respondJson([
            'message' => 'Usuário criado com sucesso',
            'data' => $user
        ], 201);
    }

    public function show(Request $request, $id)
    {
        return $this->respondJson([
            'message' => 'Detalhes do usuário',
            'data' => ['id' => $id]
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $user = $this->userRepository->findByUuid($uuid);

        if (is_null($user)) {
            return $this->respondJson([
                'message' => 'Usuário não encontrado'
            ], 422);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'access' => 'sometimes|string',
            'status' => 'sometimes|string'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $data = $this->userTransformer->keysTransform($data);

        $updatedUser = $this->userRepository->update($user->id, $validatedData);

        if (is_null($updatedUser)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar usuário'
            ], 500);
        }

        $updatedUser = $this->userTransformer->transform($updatedUser);

        return $this->respondJson([
            'message' => 'Usuário atualizado com sucesso',
            'data' =>  $updatedUser
        ]);
    }

    public function destroy(Request $request, $id)
    {
        return $this->respondJson([
            'message' => 'Usuário removido com sucesso',
            'data' => ['id' => $id]
        ]);
    }

    public function authenticate(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $user = $this->userRepository->authenticate(
            $validatedData['email'],
            $validatedData['password']
        );

        if (is_null($user)) {
            return $this->respondJson([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $user = $this->userTransformer->transform($user);

        $token = JWT::generateToken((array)$user, 3600);

        return $this->respondJson([
            'message' => 'Autenticação bem-sucedida',
            'data' => $token
        ]);
    }

    public function profile(Request $request)
    {
        $userPayload = $request->header('Authorization');

        $userPayload = JWT::validateToken($userPayload);

        if (is_null($userPayload)) {
            return $this->respondJson([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $user = $this->userRepository->findById((int)$userPayload['code']);

        if (is_null($user)) {
            return $this->respondJson([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $userPayload = $this->userTransformer->transform($user);

        return $this->respondJson([
            'message' => 'Perfil do usuário',
            'data' => $userPayload
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');

        if (is_null($token)) {
            return $this->respondJson([
                'message' => 'autorização não fornecida'
            ], 400);
        }

        $token = JWT::invalidateToken($token);

        return $this->respondJson([
            'message' => 'Logout realizado com sucesso',
        ]);
    }
}
