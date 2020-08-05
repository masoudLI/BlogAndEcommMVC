<?php

namespace Framework;

use DateTime;
use Framework\Database\AbstractRepository;
use Framework\Database\Table;
use Framework\Validator\ValidationError;
use Framework\Validator\ValidatorError;
use PDO;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    private const MIME_SIZE = 50000;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string[]
     */
    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Vérifie que les champs sont présents dans le tableau
     *
     * @param string[] ...$keys
     * @return Validator
     */
    public function required(...$keys): self
    {
        if (is_array($keys[0])) {
            $keys = $keys[0];
        }
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Vérifie que le champs n'est pas vide
     *
     * @param string[] ...$keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (
            !is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) && $length < $min) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }


    /**
     * Vérifie que l'élément est un slug
     *
     * @param string $key
     * @return Validator
     */
    public function numeric(string $key): self
    {
        $value = $this->getValue($key);
        if (!is_numeric($value)) {
            $this->addError($key, 'numeric');
        }
        return $this;
    }

    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $valueConfirm = $this->getValue($key . '_confirm');
        if ($valueConfirm !== $value) {
            $this->addError($key, 'confirm');
        }
        return $this;
    }

    /**
     * Vérifie si l'email est valid
     * @param string $key
     * @return Validator
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }

    /**
     * Vérifie que l'élément est un slug
     *
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        //$pattern = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }


    /**
     * uploded
     *
     * @param  mixed $key
     * @return self
     */
    public function uploaded(string $key): self
    {
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }


    /**
     * FunctionName
     *
     * @param  mixed $key
     * @param  mixed $extension
     * @return void
     */
    public function extension(string $key, array $extensions): self
    {
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $extensionType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $extensionType !== $type) {
                $this->addError($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }

    /**
     * FunctionName
     *
     * @param  mixed $key
     * @param  mixed $extension
     * @return void
     */
    public function size(string $key): self
    {
        $file = $this->getValue($key);
        $size = $file->getSize();
        if ($size > self::MIME_SIZE) {
            $this->addError($key, 'size');
        }

        return $this;
    }

    /**
     * dateTime
     *
     * @param  mixed $key
     * @param  mixed $format
     * @return self
     */
    public function dateTime(string $key, $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }


    /**
     * time
     *
     * @param  mixed $key
     * @return self
     */
    public function time(string $key): self
    {
        $value = $this->getValue($key);
        if (\DateTime::createFromFormat('H:i:s', $value) === false) {
            $this->addError($key, 'time');
            return false;
        }
        return $this;
    }

    /**
     * beforeTime
     *
     * @param  mixed $startField
     * @param  mixed $endField
     * @return self
     */
    public function beforeTime(string $startField, string $endField): self
    {
        $valueStart = $this->getValue($startField);
        $valueEnd = $this->getValue($endField);
        if ($this->time($startField) && $this->time($endField)) {
            $start = \DateTime::createFromFormat('H:i:s', $valueStart);
            $end = \DateTime::createFromFormat('H:i:s', $valueEnd);
            if ($start->getTimestamp() > $end->getTimestamp()) {
                $this->addError($valueStart, 'beforeTime', [$valueStart, $valueEnd]);
                return $this;
            }
            return $this;
        }
        return $this;
    }


    /**
     * existe
     *
     * @param  mixed $key
     * @param  mixed $table
     * @param  mixed $pdo
     * @return self
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }


    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Récupère les erreurs
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Ajoute une erreur
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidatorError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}
