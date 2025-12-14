<?php

namespace App\Http\Controllers\v1\Person;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;
use App\Http\Trait\SendEmailTrait;
use App\Repositories\Contracts\Person\IPessoaRepository;
use App\Transformers\Person\PessoaTransformer;

class PessoaController extends Controller
{
    use SendEmailTrait;
    protected IPessoaRepository $pessoaRepository;
    protected PessoaTransformer $pessoaTransformer;

    public function __construct(IPessoaRepository $pessoaRepository, PessoaTransformer $pessoaTransformer)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->pessoaTransformer = $pessoaTransformer;
    }

    public function index(Request $request)
    {
        $pessoas = $this->pessoaRepository->findAll($request->all());
        $transformedPessoas = $this->pessoaTransformer->transformCollection($pessoas);

        return $this->respondJson([
            'message' => 'Lista de pessoas',
            'data' => $transformedPessoas
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $pessoa = $this->pessoaRepository->findByUuid($uuid);

        if (is_null($pessoa)) {
            return $this->respondJson([
                'message' => 'Pessoa não encontrada'
            ], 404);
        }

        $transformedPessoa = $this->pessoaTransformer->transform($pessoa);

        return $this->respondJson([
            'message' => 'Detalhes da pessoa',
            'data' => $transformedPessoa
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pessoa,email',
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'status' => 'required|string|in:ativo,inativo'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $pessoaTransformed = $this->pessoaTransformer->keysTransform($validatedData);

        $pessoa = $this->pessoaRepository->create($pessoaTransformed);

        if (is_null($pessoa)) {
            return $this->respondJson([
                'message' => 'Erro ao criar pessoa'
            ], 500);
        }

        $transformedPessoa = (object)$this->pessoaTransformer->transform($pessoa);

        $username = (object)$transformedPessoa->username;

        $this->sendEmail(
            $username->email,
            'Bem-vindo ao Nosso App!',
            'Obrigado por se registrar em nosso aplicativo.',
            $username->name,
            $_ENV['URL_BASE'] . $_ENV['API_PREFIX'] . '/confirm-email/' . $username->id
        );

        return $this->respondJson([
            'message' => 'Pessoa criada com sucesso',
            'data' => $transformedPessoa
        ], 201);
    }

    public function update(Request $request, string $uuid)
    {
        $pessoa = $this->pessoaRepository->findByUuid($uuid);

        if (is_null($pessoa)) {
            return $this->respondJson([
                'message' => 'Pessoa não encontrada'
            ], 404);
        }

        $data = $request->all();

        $validatedData = $this->validate($data, [
            'name' => 'nullable|string|max:255',
            'email' => "nullable|email|max:255|unique:pessoa,email,{$pessoa->id}",
            'phone' => 'nullable|string|max:20',
            'type_doc' => 'nullable|string|max:50',
            'doc' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'status' => 'required|string|in:ativo,inativo'
        ]);

        if (is_null($validatedData)) {
            return $this->respondJson([
                'message' => 'Dados inválidos',
                'errors' => $this->getErrors()
            ], 422);
        }

        $pessoaTransformed = $this->pessoaTransformer->keysTransform($validatedData);

        $updatedPessoa = $this->pessoaRepository->update($pessoa->id, $pessoaTransformed);

        if (is_null($updatedPessoa)) {
            return $this->respondJson([
                'message' => 'Erro ao atualizar pessoa'
            ], 500);
        }

        $transformedPessoa = (object)$this->pessoaTransformer->transform($updatedPessoa);

        if ($transformedPessoa->email !== $pessoa->email) {
            $username = (object)$transformedPessoa->username;
            $this->sendEmail(
                $transformedPessoa->email,
                'Atualização de Email',
                'Seu email foi atualizado com sucesso.',
                $transformedPessoa->name,
                $_ENV['URL_BASE'] . $_ENV['API_PREFIX'] . '/confirm-email/' . $username->id
            );
        }

        return $this->respondJson([
            'message' => 'Pessoa atualizada com sucesso',
            'data' => $transformedPessoa
        ]);
    }

    public function destroy(Request $request, string $uuid)
    {
        $pessoa = $this->pessoaRepository->findByUuid($uuid);

        if (is_null($pessoa)) {
            return $this->respondJson([
                'message' => 'Pessoa não encontrada'
            ], 404);
        }

        $deleted = $this->pessoaRepository->delete($pessoa->id);

        if (!$deleted) {
            return $this->respondJson([
                'message' => 'Erro ao deletar pessoa'
            ], 500);
        }

        return $this->respondJson([
            'message' => 'Pessoa deletada com sucesso'
        ]);
    }
}
