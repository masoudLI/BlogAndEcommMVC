<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    public function field($context, string $key, $value, string $label, array $options = [], array $attributes = []): ?string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorsHtml($context, $key);
        $value = $this->convertValue($value);
        $class = "form-group";
        $attributes = array_merge([
            'class' => 'form-control ' . ($options['class'] ?? ''),
            'id' => $key,
            'name' => $key
        ], $attributes);

        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif ($type === 'date') {
            $input = $this->date($value, $attributes);
        }
        else {
            $input = $this->input($value, $attributes);
        }
        return "<div class={$class}>
            <label for={$key}>{$label}</label>
            {$input}
            {$error}
        </div>";
    }

    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            $value->format('Y/m/d H:i:s');
        }
        return (string) $value;
    }

    
    /**
     * input
     *
     * @param  mixed $value
     * @param  mixed $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input " . $this->getHtmlFormArray($attributes). " value=\"$value\">";
    }

    
    /**
     * textarea
     *
     * @param  mixed $value
     * @param  mixed $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFormArray($attributes). ">$value</textarea>";
    }

    /**
     * Génère un <input>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function date(?string $value, array $attributes): string
    {
        return "<input class=\"\" " . $this->getHtmlFormArray($attributes) . " value=\"{$value}\">";
    }

    
    /**
     * getErrorsHtml
     *
     * @param  mixed $context
     * @param  mixed $key
     * @return string
     */
    private function getErrorsHtml($context, $key)
    {
        $errors = $context['errors'][$key] ?? false;
        if ($errors) {
            return "<small class=\"form-text text-muted help-block\">{$errors}</small>";
        }
        return "";
    }
    
    /**
     * getHtmlFormArray
     *
     * @param  mixed $attributes
     * @return void
     */
    public function getHtmlFormArray(array $attributes)
    {
        /* $htmlParts = [];
        foreach ($attributes as $key => $value) {
            dd($attributes);
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        } */

        return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }
}
