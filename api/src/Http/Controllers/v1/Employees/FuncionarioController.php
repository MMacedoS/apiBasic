<?php

namespace App\Http\Controllers\v1\Employees;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Entities\Employees\FuncionarioRepository;
use App\Transformers\Employees\FuncionarioTransformer;

class FuncionarioController extends Controller
{
    protected FuncionarioRepository $funcionarioRepository;
    protected FuncionarioTransformer $funcionarioTransformer;

    public function __construct(
        FuncionarioRepository $funcionarioRepository,
        FuncionarioTransformer $funcionarioTransformer
    ) {
        $this->funcionarioRepository = $funcionarioRepository;
        $this->funcionarioTransformer = $funcionarioTransformer;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $funcionarios = $this->funcionarioRepository->findAll($params);

        $transformedFuncionarios = $this->funcionarioTransformer::transformCollection($funcionarios);

        return $this->respondJson([
            'message' => 'Lista de funcionários',
            'data' => $transformedFuncionarios
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $funcionario = $this->funcionarioRepository->findByUuid($uuid);

        if (is_null($funcionario)) {
            return $this->respondJson([
                'message' => 'Funcionário não encontrado'
            ], 404);
        }

        $transformedFuncionario = $this->funcionarioTransformer::transform($funcionario);

        return $this->respondJson([
            'message' => 'Detalhes do funcionário',
            'data' => $transformedFuncionario
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'job' => 'required|string|max:255',
            'amount' => 'required|float',
            'departure' => 'required|string|max:255',
            'date_admission' => 'required|date',
            'date_dismissal' => 'nullable|date|after_or_equal:date_admission',
            'status' => 'required|string|in:ativo,inativo',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Erro de validação',
                'errors' => $this->errors
            ], 422);
        }

        $transformedData = $this->funcionarioTransformer::keysTransform($validatedData);

        $funcionario = $this->funcionarioRepository->create($transformedData);

        if (is_null($funcionario)) {
            return $this->respondJson([
                'message' => 'Erro ao criar funcionário'
            ], 500);
        }

        $transformedFuncionario = $this->funcionarioTransformer::transform($funcionario);

        return $this->respondJson([
            'message' => 'Funcionário criado com sucesso',
            'data' => $transformedFuncionario
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $funcionario = $this->funcionarioRepository->findByUuid($uuid);

        if (is_null($funcionario)) {
            return $this->respondJson([
                'message' => 'Funcionário não encontrado'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'job' => 'sometimes|string|max:255',
            'amount' => 'sometimes|float',
            'departure' => 'sometimes|string|max:255',
            'date_admission' => 'sometimes|date',
            'date_dismissal' => 'sometimes|nullable|date|after_or_equal:date_admission',
            'status' => 'sometimes|string|in:ativo,inativo',
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|max:255",
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Erro de validação',
                'errors' => $this->errors
            ], 422);
        }

        $transformedData = $this->funcionarioTransformer::keysTransform($validatedData);

        $updatedFuncionario = $this->funcionarioRepository->update($funcionario->id, $transformedData);

        if (is_null($updatedFuncionario)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar funcionário'
            ], 500);
        }

        $transformedFuncionario = $this->funcionarioTransformer::transform($updatedFuncionario);

        return $this->respondJson([
            'message' => 'Funcionário atualizado com sucesso',
            'data' => $transformedFuncionario
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $funcionario = $this->funcionarioRepository->findByUuid($uuid);

        if (is_null($funcionario)) {
            return $this->respondJson([
                'message' => 'Funcionário não encontrado'
            ], 404);
        }

        $deleted = $this->funcionarioRepository->delete($funcionario->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar funcionário'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Funcionário deletado com sucesso'
        ]);
    }
}
