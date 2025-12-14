<?php

namespace App\Http\Controllers\v1\Services;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Transformers\Services\ServiceTransformer;

class ServiceController extends Controller
{
    private IServiceRepository $serviceRepository;
    private ServiceTransformer $serviceTransformer;

    public function __construct(
        IServiceRepository $serviceRepository,
        ServiceTransformer $serviceTransformer
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->serviceTransformer = $serviceTransformer;
    }

    public function index(Request $request)
    {
        $services = $this->serviceRepository->findAll();
        $services = $this->serviceTransformer->transformCollection($services);

        return $this->respondJson([
            'message' => 'Lista de serviços',
            'data' => $services
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $service = $this->serviceRepository->findByUuid($uuid);

        if (is_null($service)) {
            return $this->respondJson([
                'message' => 'Serviço não encontrado'
            ], 404);
        }

        $service = $this->serviceTransformer->transform($service);

        return $this->respondJson([
            'message' => 'Detalhes do serviço',
            'data' => $service
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|float|min:0',
            'category' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|in:ativo,inativo'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $serviceTransformed = $this->serviceTransformer->keysTransform($validatedData);

        $service = $this->serviceRepository->create($serviceTransformed);

        if (is_null($service)) {
            return $this->respondJson([
                'message' => 'Erro ao criar serviço'
            ], 500);
        }

        $service = $this->serviceTransformer->transform($service);

        return $this->respondJson([
            'message' => 'Serviço criado com sucesso',
            'data' => $service
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $service = $this->serviceRepository->findByUuid($uuid);

        if (is_null($service)) {
            return $this->respondJson([
                'message' => 'Serviço não encontrado'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'value' => 'sometimes|float|min:0',
            'category' => 'sometimes|string|max:100',
            'duration' => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:ativo,inativo'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $dataTransformed = $this->serviceTransformer->keysTransform($validatedData);

        $updatedService = $this->serviceRepository->update($service->id, $dataTransformed);

        if (is_null($updatedService)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar serviço'
            ], 500);
        }

        $updatedService = $this->serviceTransformer->transform($updatedService);

        return $this->respondJson([
            'message' => 'Serviço atualizado com sucesso',
            'data' =>  $updatedService
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $service = $this->serviceRepository->findByUuid($uuid);

        if (is_null($service)) {
            return $this->respondJson([
                'message' => 'Serviço não encontrado'
            ], 404);
        }

        $deleted = $this->serviceRepository->delete($service->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar serviço'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Serviço deletado com sucesso'
        ]);
    }
}
