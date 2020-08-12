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
        $class = "form-group";
        $value = $this->convertValue($value);
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
        } elseif ($options['options'] ?? []) {
            $input = $this->select($value, $options['options'], $attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } elseif ($type === 'password') {
            $input = $this->password($value, $attributes);
        } elseif ($type === 'date') {
            $input = $this->date($value, $attributes);
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } else {
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
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
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
        return "<input type=\"text\" ". $this->getHtmlFormArray($attributes) . " value=\"$value\">";
    }

    /**
     * input
     *
     * @param  string $value
     * @param  array $attributes
     * @return string
     */
    private function password(?string $value, array $attributes): string
    {
        return "<input type=\"password\" " . $this->getHtmlFormArray($attributes) . ">";
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
        return "<textarea " . $this->getHtmlFormArray($attributes) . ">$value</textarea>";
    }


    /**
     * Génère un <input type="checkbox">
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function checkbox(?string $value, array $attributes): string
    {
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0"/>';
        if ($value) {
            $attributes['checked'] = true;
        }
        return $html . "<input type=\"checkbox\" " . $this->getHtmlFormArray($attributes) . " value=\"1\">";
    }

    /**
     * Génère un <input>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function date(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFormArray($attributes) . " value=\"{$value}\">";
    }


    private function file(array $attributes): string
    {
        return "<input type=\"file\" " . $this->getHtmlFormArray($attributes) . ">";
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
     * getErrorsHtml
     *
     * @param  mixed $context
     * @param  mixed $key
     * @return string
     */
    private function select(?string $value, array $options, $attributes)
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $value === $key];
            return $html . '<option ' . $this->getHtmlFormArray($params) . '>' . $options[$key] .'</option>';
        }, "");
        return "<select " . $this->getHtmlFormArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * getHtmlFormArray
     *
     * @param  mixed $attributes
     * @return void
     */
    public function getHtmlFormArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
        /* return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes)); */
    }
}
