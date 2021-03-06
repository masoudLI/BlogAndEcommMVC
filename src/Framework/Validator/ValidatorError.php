<?php

namespace Framework\Validator;

class ValidatorError
{
    private $key;

    private $rule;

    private $messages = [
        'required' => 'Le champs %s est requis',
        'empty' => 'Le champs %s ne peut être vide',
        'slug' => 'Le champs %s n\'est pas un slug valide',
        'minLength' => 'Le champs %s doit contenir plus de %d caractères',
        'maxLength' => 'Le champs %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champs %s doit contenir entre %d et %d caractères',
        'datetime' => 'Le champs %s doit être une date valide (%s)',
        'time' => 'Le champs %S ne semble pas valide',
        'beforeTime' => 'Le champs %s doit être inférieur au temps de %s',
        'exists' => 'Le champs %s n\'existe pas sur dans la table %s',
        'unique' => 'Le champs %s doit étre unique',
        'filetype' => 'Le champs %s n\'est pas au format valide (%s)',
        'uploaded' => 'Vous devez uploader un fichier',
        'email' => 'Vous devez entrer un mail valide',
        'confirm' => 'vous devez confirmez le champ %s',
        'numeric' => 'le champ doit etre %s',
        'size' => 'Image est trop grand'
    ];


    private $attributes;

    public function __construct(string $key, string $rule, array $attributes)
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }


    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return \call_user_func_array('sprintf', $params);
    }
}
