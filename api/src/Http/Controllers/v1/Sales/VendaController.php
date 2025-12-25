<?php

namespace App\Http\Controllers\v1\Sales;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Sales\IItemsVendaRepository;
use App\Repositories\Contracts\Sales\IVendaRepository;
use App\Repositories\Entities\Customers\ClienteRepository;
use App\Transformers\Sales\VendaTransformer;

class VendaController extends Controller
{
    private IVendaRepository $vendaRepository;
    private IItemsVendaRepository $itemsVendaRepository;

    public function __construct(
        IVendaRepository $vendaRepository,
        IItemsVendaRepository $itemsVendaRepository
    ) {
        $this->vendaRepository = $vendaRepository;
        $this->itemsVendaRepository = $itemsVendaRepository;
    }

    public function index(Request $request)
    {
        $sales = $this->vendaRepository->findAll($request->all());

        return $this->respondJson(
            [
                'message' => 'Lista de vendas recuperada com sucesso.',
                'data' => $sales,
            ]
        );
    }

    public function show(Request $request, string $uuid)
    {
        $sale = $this->vendaRepository->findByUuid($uuid);

        if (!$sale) {
            return $this->respondJson(
                [
                    'message' => 'Venda não encontrada.',
                ],
                404
            );
        }

        $sale = VendaTransformer::transform($sale);

        return $this->respondJson(
            [
                'message' => 'Venda recuperada com sucesso.',
                'data' => $sale,
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validateData = $this->validate($data, [
            'customer_id' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|float|min:0',
        ]);

        if (is_null($validateData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $validateTransformer = VendaTransformer::transformKeysToSnakeCase($validateData);

        $customer = ClienteRepository::getInstance()->findByUuid($validateTransformer['customer_id']);
        if (is_null($customer)) {
            return $this->respondJson([
                'message' => 'Cliente não encontrado.',
            ], 422);
        }

        $validateTransformer['customer_id'] = $customer->id;

        $sale = $this->vendaRepository->create($validateTransformer);

        if (!$sale) {
            return $this->respondJson(
                [
                    'message' => 'Erro ao criar a venda. verifique os dados, estoque e tente novamente.',
                ],
                500
            );
        }

        $saleTransformer = VendaTransformer::transform($sale);

        return $this->respondJson(
            [
                'message' => 'Venda criada com sucesso.',
                'data' => $saleTransformer,
            ],
            201
        );
    }

    public function update(Request $request, string $uuid)
    {
        $data = $request->all();

        $sale = $this->vendaRepository->findByUuid($uuid);
        if (!$sale) {
            return $this->respondJson(
                [
                    'message' => 'Venda não encontrada.',
                ],
                404
            );
        }

        $validateData = $this->validate($data, [
            'customer_id' => 'uuid',
            'items' => 'array|min:1',
            'items.*.product_id' => 'uuid',
            'items.*.quantity' => 'integer|min:1',
            'items.*.unit_price' => 'float|min:0',
        ]);

        if (is_null($validateData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $validateTransformer = VendaTransformer::transformKeysToSnakeCase($validateData);

        if (isset($validateTransformer['customer_id'])) {
            $customer = ClienteRepository::getInstance()->findByUuid($validateTransformer['customer_id']);
            if (is_null($customer)) {
                return $this->respondJson([
                    'message' => 'Cliente não encontrado.',
                ], 422);
            }
            $validateTransformer['customer_id'] = $customer->id;
        }

        $updatedSale = $this->vendaRepository->update($sale->id, $validateTransformer);

        if (!$updatedSale) {
            return $this->respondJson(
                [
                    'message' => 'Erro ao atualizar a venda. verifique os dados, estoque e tente novamente.',
                ],
                500
            );
        }

        $updatedSaleTransformer = VendaTransformer::transform($updatedSale);
        return $this->respondJson(
            [
                'message' => 'Venda atualizada com sucesso.',
                'data' => $updatedSaleTransformer,
            ]
        );
    }

    public function destroy(Request $request, string $uuid)
    {
        $sale = $this->vendaRepository->findByUuid($uuid);
        if (!$sale) {
            return $this->respondJson(
                [
                    'message' => 'Venda não encontrada.',
                ],
                404
            );
        }

        $deleted = $this->vendaRepository->delete($sale->id);
        if (!$deleted) {
            return $this->respondJson(
                [
                    'message' => 'Erro ao deletar a venda.',
                ],
                500
            );
        }

        return $this->respondJson(
            [
                'message' => 'Venda deletada com sucesso.',
            ]
        );
    }
}
