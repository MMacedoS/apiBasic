<?php

namespace App\Http\Controllers\v1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Customers\IClienteRepository;
use App\Repositories\Contracts\Orders\IOrdemProdutoRepository;
use App\Repositories\Contracts\Orders\IOrdemRepository;
use App\Repositories\Contracts\Orders\IOrdemServicoRepository;
use App\Repositories\Contracts\Products\IProdutoRepository;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Transformers\Orders\OrdemTransformer;

class OrdemController extends Controller
{
    private IOrdemRepository $ordemRepository;
    private IOrdemProdutoRepository $ordemProdutoRepository;
    private IOrdemServicoRepository $ordemServicoRepository;
    private IProdutoRepository $produtoRepository;
    private IServiceRepository $serviceRepository;
    private IClienteRepository $clienteRepository;

    public function __construct(
        IOrdemRepository $ordemRepository,
        IOrdemProdutoRepository $ordemProdutoRepository,
        IOrdemServicoRepository $ordemServicoRepository,
        IProdutoRepository $produtoRepository,
        IServiceRepository $serviceRepository,
        IClienteRepository $clienteRepository
    ) {
        $this->ordemRepository = $ordemRepository;
        $this->ordemProdutoRepository = $ordemProdutoRepository;
        $this->ordemServicoRepository = $ordemServicoRepository;
        $this->produtoRepository = $produtoRepository;
        $this->serviceRepository = $serviceRepository;
        $this->clienteRepository = $clienteRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $params = OrdemTransformer::KeysTransform($params);

        $orders = $this->ordemRepository->findAll($params);
        $orders = OrdemTransformer::transformCollection($orders);
        return $this->respondJson([
            'message' => 'Listas das ordens de serviço',
            'data' => $orders
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $transformedOrder = OrdemTransformer::transform($order);

        return $this->respondJson([
            'message' => 'Detalhes da ordem de serviço',
            'data' => $transformedOrder
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'customer_id' => 'required|string',
            'description' => 'nullable|string',
            'technical_report' => 'nullable|string',
            'observations' => 'nullable|string',
            'opened_at' => 'required|date',
            'closed_at' => 'nullable|date|after_or_equal:opened_at',
            'status' => 'required|string|in:aberta,concluida,em_andamento,cancelada,orcamento',
            'services' => 'nullable|array',
            'products' => 'nullable|array',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $dataToCreate = OrdemTransformer::KeysTransform($validatedData);

        $customer = $this->clienteRepository->findByUuid($data['customer_id'] ?? '');

        if (is_null($customer)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $dataToCreate['customer_id'] = $customer->id;

        $createdOrder = $this->ordemRepository->create($dataToCreate);

        if (is_null($createdOrder)) {
            return $this->respondJson([
                'message' => 'Erro ao criar a ordem de serviço'
            ], 500);
        }

        $transformedOrder = OrdemTransformer::transform($createdOrder);

        return $this->respondJson([
            'message' => 'Ordem de serviço criada com sucesso',
            'data' => $transformedOrder
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'status' => 'sometimes|string|in:aberta,concluida,em_andamento,cancelada, orcamento',
            'description' => 'sometimes|nullable|string',
            'technical_report' => 'sometimes|nullable|string',
            'observations' => 'sometimes|nullable|string',
            'opened_at' => 'sometimes|date',
            'closed_at' => 'sometimes|nullable|date|after_or_equal:opened_at',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $dataToUpdate = OrdemTransformer::KeysTransform($validatedData);

        $customer = $this->clienteRepository->findByUuid($data['customer_id'] ?? '');

        if (is_null($customer)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado'
            ], 404);
        }

        $dataToUpdate['customer_id'] = $customer->id;

        $updatedOrder = $this->ordemRepository->update($order->id, $dataToUpdate);

        if (is_null($updatedOrder)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar a ordem de serviço'
            ], 500);
        }

        $transformedOrder = OrdemTransformer::transform($updatedOrder);

        return $this->respondJson([
            'message' => 'Ordem de serviço atualizada com sucesso',
            'data' => $transformedOrder
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $deleted = $this->ordemRepository->delete($order->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar a ordem de serviço'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Ordem de serviço deletada com sucesso'
        ]);
    }

    public function changeStatus(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'status' => 'required|string|in:aberta,concluida,em_andamento,cancelada,orcamento',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $updated = $this->ordemRepository->updateOrdemStatus($order->id, $validatedData['status']);

        if (!$updated) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar o status da ordem de serviço'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Status da ordem de serviço atualizado com sucesso'
        ]);
    }

    public function assignProductToOrder(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'products' => 'required|array'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        if (
            empty($validatedData['products']) ||
            !is_array($validatedData['products']) ||
            count($validatedData['products']) === 0
        ) {
            return $this->respondJson([
                'message' => 'Nenhum produto fornecido para atribuição'
            ], 422);
        }

        foreach ($validatedData['products'] as $productUuid) {
            $product = $this->produtoRepository->findByUuid($productUuid['product']);

            if (is_null($product)) {
                return $this->respondJson([
                    'message' => "Produto com UUID {$productUuid} não encontrado"
                ], 404);
            }

            $assigned = $this->ordemProdutoRepository
                ->assignProdutoToOrdem(
                    $order->id,
                    $product->id,
                    $product->preco,
                    $productUuid['quantity']
                );

            if (!$assigned) {
                return $this->respondJson([
                    'message' => "Erro ao atribuir o produto com UUID {$productUuid} à ordem de serviço"
                ], 500);
            }
        }
        return $this->respondJson([
            'message' => 'Produtos atribuídos à ordem de serviço com sucesso'
        ]);
    }

    public function removeProductFromOrder(Request $request, string $uuid, string $productUuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $product = $this->produtoRepository->findByUuid($productUuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $removed = $this->ordemProdutoRepository->removeProdutoFromOrdem($order->id, $product->id);

        if (!$removed) {
            return $this->respondJson([
                'message' => 'Erro ao remover o produto da ordem de serviço'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Produto removido da ordem de serviço com sucesso'
        ]);
    }

    public function assignServiceToOrder(Request $request, string $uuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'services' => 'required|array',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        if (
            empty($validatedData['services']) ||
            !is_array($validatedData['services']) ||
            count($validatedData['services']) === 0
        ) {
            return $this->respondJson([
                'message' => 'Nenhum serviço fornecido para atribuição'
            ], 422);
        }

        foreach ($validatedData['services'] as $serviceUuid) {
            $service = $this->serviceRepository->findByUuid($serviceUuid);
            if (is_null($service)) {
                return $this->respondJson([
                    'message' => "Serviço com UUID {$serviceUuid} não encontrado"
                ], 404);
            }

            $assigned = $this->ordemServicoRepository
                ->assignServicoToOrdem(
                    $order->id,
                    $service->id,
                    $service->valor
                );

            if (!$assigned) {
                return $this->respondJson([
                    'message' => "Erro ao atribuir o serviço com UUID {$serviceUuid} à ordem de serviço"
                ], 500);
            }
        }
        return $this->respondJson([
            'message' => 'Serviços atribuídos à ordem de serviço com sucesso'
        ]);
    }

    public function removeServiceFromOrder(Request $request, string $uuid, string $serviceUuid)
    {
        $order = $this->ordemRepository->findByUuid($uuid);

        if (is_null($order)) {
            return $this->respondJson([
                'message' => 'Ordem de serviço não encontrada'
            ], 404);
        }

        $service = $this->serviceRepository->findByUuid($serviceUuid);

        if (is_null($service)) {
            return $this->respondJson([
                'message' => 'Serviço não encontrado'
            ], 404);
        }

        $removed = $this->ordemServicoRepository->removeServicoFromOrdem($order->id, $service->id);

        if (!$removed) {
            return $this->respondJson([
                'message' => 'Erro ao remover o serviço da ordem de serviço'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Serviço removido da ordem de serviço com sucesso'
        ]);
    }
}
