<?php

namespace App\Http\Controllers\v1\Customers;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Customers\IClienteRepository;
use App\Transformers\Customers\ClienteTransformer;

class ClienteController extends Controller
{
    protected IClienteRepository $clienteRepository;
    protected ClienteTransformer $clienteTransformer;

    public function __construct(
        IClienteRepository $clienteRepository,
        ClienteTransformer $clienteTransformer
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->clienteTransformer = $clienteTransformer;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $clientes = $this->clienteRepository->findAll($params);
        $transformedClientes = $this->clienteTransformer->transformCollection($clientes);

        return $this->respondJson([
            'message' => 'Lista de clientes',
            'data' => $transformedClientes
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $transformedCliente = $this->clienteTransformer->transform($cliente);

        return $this->respondJson([
            'message' => 'Detalhes do cliente',
            'data' => $transformedCliente
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'status' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'status' => 'required|string|in:ativo,inativo',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100'
        ]);

        $clienteData = ClienteTransformer::keysTransform($validatedData);
        $newCliente = $this->clienteRepository->create($clienteData);

        if (is_null($newCliente)) {
            return $this->respondJson([
                'message' => 'Erro ao criar cliente'
            ], 500);
        }

        $transformedCliente = $this->clienteTransformer->transform($newCliente);

        return $this->respondJson([
            'message' => 'Cliente criado com sucesso',
            'data' => $transformedCliente
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'status' => 'nullable|string|max:50',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100'
        ]);

        $clienteData = ClienteTransformer::keysTransform($validatedData);
        $updatedCliente = $this->clienteRepository->update($cliente->id, $clienteData);

        if (is_null($updatedCliente)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar cliente'
            ], 500);
        }

        $transformedCliente = $this->clienteTransformer->transform($updatedCliente);

        return $this->respondJson([
            'message' => 'Cliente atualizado com sucesso',
            'data' => $transformedCliente
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $cliente = $this->clienteRepository->findByUuid($uuid);

        if (is_null($cliente)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $deleted = $this->clienteRepository->delete($cliente->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar cliente'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Cliente deletado com sucesso'
        ]);
    }
}
