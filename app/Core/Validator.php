<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value, string $label = ''): self
    {
        $label = $label ?: $field;
        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->errors[$field][] = "{$label} es obligatorio.";
        }
        return $this;
    }

    public function email(string $field, mixed $value, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} debe ser un correo válido.";
        }
        return $this;
    }

    public function minLength(string $field, mixed $value, int $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && mb_strlen((string)$value) < $min) {
            $this->errors[$field][] = "{$label} debe tener al menos {$min} caracteres.";
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $max, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && mb_strlen((string)$value) > $max) {
            $this->errors[$field][] = "{$label} no debe exceder {$max} caracteres.";
        }
        return $this;
    }

    public function numeric(string $field, mixed $value, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = "{$label} debe ser un valor numérico.";
        }
        return $this;
    }

    public function min(string $field, mixed $value, float $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (is_numeric($value) && (float)$value < $min) {
            $this->errors[$field][] = "{$label} debe ser al menos {$min}.";
        }
        return $this;
    }

    public function max(string $field, mixed $value, float $max, string $label = ''): self
    {
        $label = $label ?: $field;
        if (is_numeric($value) && (float)$value > $max) {
            $this->errors[$field][] = "{$label} no debe exceder {$max}.";
        }
        return $this;
    }

    public function inArray(string $field, mixed $value, array $allowed, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && !in_array($value, $allowed, true)) {
            $this->errors[$field][] = "{$label} contiene un valor no permitido.";
        }
        return $this;
    }

    public function matches(string $field, mixed $value, mixed $matchValue, string $label = ''): self
    {
        $label = $label ?: $field;
        if ($value !== $matchValue) {
            $this->errors[$field][] = "{$label} no coincide.";
        }
        return $this;
    }

    public function slug(string $field, mixed $value, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($value) && !preg_match('/^[a-z0-9\-]+$/', (string)$value)) {
            $this->errors[$field][] = "{$label} solo permite letras minúsculas, números y guiones.";
        }
        return $this;
    }

    public function image(string $field, array $file, string $label = ''): self
    {
        $label = $label ?: $field;
        if (empty($file['tmp_name'])) {
            return $this;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
        if (!in_array($mime, $allowed, true)) {
            $this->errors[$field][] = "{$label} debe ser una imagen válida (JPEG, PNG, WEBP, AVIF).";
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $this->errors[$field][] = "{$label} no debe exceder " . (UPLOAD_MAX_SIZE / 1024 / 1024) . "MB.";
        }
        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        $first = reset($this->errors);
        return $first ? $first[0] : null;
    }

    public function clear(): void
    {
        $this->errors = [];
    }

    public function validate(array $rules, array $data): bool
    {
        $this->clear();
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            $label = $fieldRules['label'] ?? $field;
            if (isset($fieldRules['required'])) {
                $this->required($field, $value, $label);
            }
            if (isset($fieldRules['email'])) {
                $this->email($field, $value, $label);
            }
            if (isset($fieldRules['min_length'])) {
                $this->minLength($field, $value, (int)$fieldRules['min_length'], $label);
            }
            if (isset($fieldRules['max_length'])) {
                $this->maxLength($field, $value, (int)$fieldRules['max_length'], $label);
            }
            if (isset($fieldRules['numeric'])) {
                $this->numeric($field, $value, $label);
            }
            if (isset($fieldRules['min'])) {
                $this->min($field, $value, (float)$fieldRules['min'], $label);
            }
            if (isset($fieldRules['max'])) {
                $this->max($field, $value, (float)$fieldRules['max'], $label);
            }
            if (isset($fieldRules['slug'])) {
                $this->slug($field, $value, $label);
            }
        }
        return !$this->hasErrors();
    }
}
