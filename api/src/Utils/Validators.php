<?php

namespace App\Utils;

use App\Repositories\Entities\Person\PessoaRepository;
use App\Repositories\Entities\Users\UserRepository;

trait Validators
{
    protected array $data = [];
    protected array $errors = [];
    protected bool $opcional = false;

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

    private function sometimes($field)
    {
        $this->opcional = true;
    }

    protected function min($field, $min)
    {
        if (!isset($this->data[$field])) {
            return;
        }

        if ($this->opcional && !isset($this->data[$field])) {
            if (strlen((string)$this->data[$field]) < $min) {
                $this->errors[$field][] = "O campo $field deve ter no mínimo $min caracteres.";
            }

            return;
        }

        if (!isset($this->data[$field]) || strlen((string)$this->data[$field]) < $min) {
            $this->errors[$field][] = "O campo $field deve ter no mínimo $min caracteres.";
        }
    }

    protected function max($field, $max)
    {
        if (!isset($this->data[$field])) {
            return;
        }

        if ($this->opcional && !isset($this->data[$field])) {
            if (strlen((string)$this->data[$field]) > $max) {
                $this->errors[$field][] = "Este campo deve ter no máximo $max caracteres.";
            }
            return;
        }

        if (isset($this->data[$field]) && strlen((string)$this->data[$field]) > $max) {
            $this->errors[$field][] = "Este campo deve ter no máximo $max caracteres.";
        }
        $this->opcional = false;
    }

    protected function email($field)
    {
        if (!isset($this->data[$field])) {
            return;
        }

        if (!$this->opcional && !isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode esta vazio.";
            return;
        }

        if (!isset($this->data[$field]) || !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "O campo $field deve ser um endereço de email válido.";
            return;
        }
    }

    protected function integer($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->errors[$field][] = "O campo $field deve ser um número inteiro.";
            }
            return;
        }

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
            return;
        }

        if (!$this->opcional && !isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }

        $parts = explode(',', $tableField);
        $table = $parts[0] ?? null;
        $column = $parts[1] ?? null;
        $exceptId = $parts[2] ?? null;

        $repository = $this->prepareRepository($table);
        if ($repository === null) {
            $this->errors[$field][] = "Repositório para a tabela $table não encontrado.";
            return;
        }
        $exists = $repository
            ->existsByField(
                $column,
                $this->data[$field]
            );
        if ($exists) {
            if (!empty($exceptId)) {
                $item = $repository->findById($exceptId);
                if (!is_null($item)) {
                    return;
                }
            }
            $this->errors[$field][] = "O valor do campo $field já está em uso.";
            return;
        }
    }

    public function after_or_equal($field, $otherField)
    {
        if (!isset($this->data[$field])) {
            return;
        }

        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !isset($this->data[$otherField])) {
            $this->errors[$field][] = "O campo $field ou $otherField não pode estar vazio.";
            return;
        }

        if (strtotime($this->data[$field]) < strtotime($this->data[$otherField])) {
            $this->errors[$field][] = "O campo $field deve ser uma data igual ou posterior a $otherField.";
            return;
        }
    }

    private function float($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "O campo $field não pode estar vazio.";
            return;
        }

        if (!filter_var($this->data[$field], FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field][] = "O campo $field deve ser um número decimal.";
            return;
        }
    }

    private function string($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !is_string($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser uma string.";
            return;
        }
    }

    private function boolean($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !is_bool($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um valor booleano.";
            return;
        }
    }

    private function date($field)
    {
        if (!isset($this->data[$field])) {
            return;
        }

        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || strtotime($this->data[$field]) === false) {
            $this->errors[$field][] = "O campo $field deve ser uma data válida.";
            return;
        }
    }

    private function in($field, $values)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        $allowedValues = explode(',', $values);
        if (!isset($this->data[$field]) || !in_array($this->data[$field], $allowedValues)) {
            $this->errors[$field][] = "O campo $field deve ser um dos seguintes valores: " . implode(', ', $allowedValues) . ".";
            return;
        }
    }

    private function not_in($field, $values)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        $disallowedValues = explode(',', $values);
        if (!isset($this->data[$field]) || in_array($this->data[$field], $disallowedValues)) {
            $this->errors[$field][] = "O campo $field não pode ser um dos seguintes valores: " . implode(', ', $disallowedValues) . ".";
            return;
        }
    }

    private function nullable($field)
    {
        if (!isset($this->data[$field]) || is_null($this->data[$field])) {
            return;
        }
    }

    private function array($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !is_array($this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um array.";
            return;
        }
    }

    private function json($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

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
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field][] = "O campo $field deve ser uma URL válida.";
            return;
        }
    }

    private function ip($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !filter_var($this->data[$field], FILTER_VALIDATE_IP)) {
            $this->errors[$field][] = "O campo $field deve ser um endereço IP válido.";
            return;
        }
    }

    private function slug($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve ser um slug válido.";
            return;
        }
    }

    private function alpha($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !preg_match('/^[a-zA-Z]+$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve conter apenas letras.";
            return;
        }
    }

    private function alpha_num($field)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

        if (!isset($this->data[$field]) || !preg_match('/^[a-zA-Z0-9]+$/', $this->data[$field])) {
            $this->errors[$field][] = "O campo $field deve conter apenas letras e números.";
            return;
        }
    }

    private function date_format($field, $format)
    {
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

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
        if ($this->opcional && !isset($this->data[$field])) {
            return;
        }

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

        if ($table === 'persons') {
            return PessoaRepository::getInstance();
        }

        return null;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
