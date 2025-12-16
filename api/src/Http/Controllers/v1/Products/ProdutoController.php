<?php

namespace App\Http\Controllers\v1\Products;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Repositories\Contracts\Products\IProdutoRepository;
use App\Transformers\Products\ProdutoTransformer;

class ProdutoController extends Controller
{
    protected IProdutoRepository $produtoRepository;

    public function __construct(IProdutoRepository $produtoRepository)
    {
        $this->produtoRepository = $produtoRepository;
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $paramsTransformer = ProdutoTransformer::KeysTransform($params);

        $products = $this->produtoRepository->findAll($paramsTransformer);

        $products = ProdutoTransformer::transformCollection($products);

        return $this->respondJson([
            'message' => 'Listas dos produtos',
            'data' => $products
        ]);
    }

    public function indexWithoutPagination(Request $request)
    {
        $params = $request->all();

        $paramsTransformer = ProdutoTransformer::KeysTransform($params);

        $products = $this->produtoRepository->findAll($paramsTransformer);

        $products = ProdutoTransformer::transformCollection($products);

        return $this->respondJson([
            'message' => 'Lista completa dos produtos',
            'data' => $products
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $product = $this->produtoRepository->findByUuid($uuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $transformedProduct = ProdutoTransformer::transform($product);

        return $this->respondJson([
            'message' => 'Detalhes do produto',
            'data' => $transformedProduct
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'required|string|max:255|unique:products,nome',
            'description' => 'nullable|string',
            'price' => 'required|float|min:0',
            'status' => 'required|string|in:ativo,inativo',
            'stock' => 'required|integer|min:0',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $transformedData = ProdutoTransformer::KeysTransform($validatedData);

        $newProduct = $this->produtoRepository->create($transformedData);

        if (is_null($newProduct)) {
            return $this->respondJson([
                'message' => 'Erro ao criar o produto'
            ], 500);
        }

        $transformedProduct = ProdutoTransformer::transform($newProduct);

        return $this->respondJson([
            'message' => 'Produto criado com sucesso',
            'data' => $transformedProduct
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|float|min:0',
            'status' => 'nullable|string|in:ativo,inativo',
            'stock' => 'nullable|integer|min:0',
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $product = $this->produtoRepository->findByUuid($uuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $transformedData = ProdutoTransformer::KeysTransform($validatedData);

        $updatedProduct = $this->produtoRepository->update($product->id, $transformedData);

        if (is_null($updatedProduct)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar o produto'
            ], 500);
        }

        $transformedProduct = ProdutoTransformer::transform($updatedProduct);

        return $this->respondJson([
            'message' => 'Produto atualizado com sucesso',
            'data' => $transformedProduct
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $product = $this->produtoRepository->findByUuid($uuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $deleted = $this->produtoRepository->delete($product->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar o produto'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Produto deletado com sucesso'
        ]);
    }

    public function reduceStock(Request $request, string $uuid, int $quantity)
    {
        $product = $this->produtoRepository->findByUuid($uuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $reduced = $this->produtoRepository->reduceStock($product->id, $quantity);

        if (!$reduced) {
            return $this->respondJson([
                'message' => 'Erro ao reduzir o estoque do produto'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Estoque do produto reduzido com sucesso'
        ]);
    }

    public function increaseStock(Request $request, string $uuid, int $quantity)
    {
        $product = $this->produtoRepository->findByUuid($uuid);

        if (is_null($product)) {
            return $this->respondJson([
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $increased = $this->produtoRepository->increaseStock($product->id, $quantity);

        if (!$increased) {
            return $this->respondJson([
                'message' => 'Erro ao aumentar o estoque do produto'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Estoque do produto aumentado com sucesso'
        ]);
    }
}
