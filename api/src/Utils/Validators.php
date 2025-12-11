<?php

namespace App\Utils;

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

        return empty($this->errors);
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
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = "O campo $field deve ter no mínimo $min caracteres.";
        }
    }

    protected function max($field, $max)
    {
        if (strlen($this->data[$field]) > $max) {
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

    public function getErrors()
    {
        return $this->errors;
    }
}
