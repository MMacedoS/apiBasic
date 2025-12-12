<?php

namespace App\Utils;

use App\Repositories\Entities\Users\UserRepository;

trait Validators
{
    protected array $data = [];
    protected array $errors = [];

    public static function uuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid) === 1;
    }

    public function validate(array $data, array $rules)
    {
        $this->data = $data;
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors) ? $data : null;
    }

    protected function applyRule($field, $rule)
    {
        if (strpos($rule, ':') !== false) {

            [$ruleName, $parameter] = explode(':', $rule, 2);
            return $this->$ruleName($field, $parameter);
        }
        return $this->$rule($field);
    }

    protected function required($field)
    {
        if (empty($this->data) || empty($this->data[$field])) {
            $this->errors[$field][] = "O campo $field é obrigatório.";
        }
    }

    protected function min($field, $min)
    {
        if (!isset($this->data[$field]) || strlen((string)$this->data[$field]) < $min) {
            $this->errors[$field][] = "O campo $field deve ter no mínimo $min caracteres.";
        }
    }

    protected function max($field, $max)
    {
        if (isset($this->data[$field]) && strlen((string)$this->data[$field]) > $max) {
            $this->errors[$field][] = "Este campo deve ter no máximo $max caracteres.";
        }
    }

    protected function email($field)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode esta vazio.";
            return;
        }

        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "O campo $field deve ser um endereço de email válido.";
            return;
        }
    }

    protected function integer($field)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode esta vazio.";
            return;
        }

        if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = "O campo $field deve ser um número inteiro.";
            return;
        }
    }

    protected function unique($field, $tableField)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }

        [$table, $column] = explode(',', $tableField);
        $repository = $this->prepareRepository($table);
        if ($repository === null) {
            $this->errors[$field][] = "Repositório para a tabela $table não encontrado.";
            return;
        }
        $exists = $repository->existsByField($column, $this->data[$field]);
        if ($exists) {
            $this->errors[$field][] = "O valor do campo $field já está em uso.";
            return;
        }
    }

    private function string($field)
    {
        if (!isset($this->data[$field]) || !is_string($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser uma string.";
            return;
        }
    }

    private function boolean($field)
    {
        if (!isset($this->data[$field]) || !is_bool($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um valor booleano.";
            return;
        }
    }

    private function date($field)
    {
        if (!isset($this->data[$field]) || strtotime($this->data[$field]) === false) {
            $this->errors[$field][] = "O campo $field deve ser uma data válida.";
            return;
        }
    }

    private function array($field)
    {
        if (!isset($this->data[$field]) || !is_array($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um array.";
            return;
        }
    }

    private function json($field)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }
        json_decode($this->data[$field]);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[$field][] = "O campo $field deve ser um JSON válido.";
            return;
        }
    }

    private function url($field)
    {
        if (!isset($this->data[$field]) || !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field][] = "O campo $field deve ser uma URL válida.";
            return;
        }
    }

    private function ip($field)
    {
        if (!isset($this->data[$field]) || !filter_var($this->data[$field], FILTER_VALIDATE_IP)) {
            $this->errors[$field][] = "O campo $field deve ser um endereço IP válido.";
            return;
        }
    }

    private function slug($field)
    {
        if (!isset($this->data[$field]) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um slug válido.";
            return;
        }
    }

    private function alpha($field)
    {
        if (!isset($this->data[$field]) || !preg_match('/^[a-zA-Z]+$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve conter apenas letras.";
            return;
        }
    }

    private function alpha_num($field)
    {
        if (!isset($this->data[$field]) || !preg_match('/^[a-zA-Z0-9]+$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve conter apenas letras e números.";
            return;
        }
    }

    private function date_format($field, $format)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }
        $date = \DateTime::createFromFormat($format, $this->data[$field]);
        if (!$date || $date->format($format) !== $this->data[$field]) {
            $this->errors[$field][] = "O campo $field deve estar no formato $format.";
            return;
        }
    }

    private function exists($field, $tableField)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }

        [$table, $column] = explode(',', $tableField);
        $repository = $this->prepareRepository($table);
        if ($repository === null) {
            $this->errors[$field][] = "Repositório para a tabela $table não encontrado.";
            return;
        }
        $exists = $repository->existsByField($column, $this->data[$field]);
        if (!$exists) {
            $this->errors[$field][] = "O valor do campo $field não existe na tabela $table.";
            return;
        }
    }

    private function prepareRepository($table)
    {
        if ($table === 'users') {
            return UserRepository::getInstance();
        }

        return null;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
