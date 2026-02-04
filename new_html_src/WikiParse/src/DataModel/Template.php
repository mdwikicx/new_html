<?php

namespace WikiConnect\ParseWiki\DataModel;

class Template
{
    private string $template;
    private string $name;
    private string $name_strip;
    private string $templateText;
    private array $parameters;
    public function __construct(string $name, array $parameters = [], string $templateText = "")
    {
        $this->name = $name;
        $this->name_strip = trim(str_replace('_', ' ', $name));
        $this->parameters = $parameters;
        $this->templateText = $templateText;
    }
    public function getTemplateText(): string
    {
        return $this->templateText;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getStripName(): string
    {
        return $this->name_strip;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
    public function deleteParameter(string $key): void
    {
        if (array_key_exists($key, $this->parameters)) {
            unset($this->parameters[$key]);
        }
    }
    public function getParameter(string $key, string $default = null): string
    {
        return $this->parameters[$key] ?? $default;
    }
    public function setTempName(string $name): void
    {
        $this->name = $name;
    }
    public function setParameter(string $key, string $value): void
    {
        $this->parameters[$key] = $value;
    }
    public function changeParameterName(string $old, string $new): void
    {
        $newParameters = [];
        foreach ($this->parameters as $k => $v) {
            if ($k === $old) {
                $k = $new;
            };
            $newParameters[$k] = $v;
        }
        $this->parameters = $newParameters;
    }

    public function changeParametersNames(array $params_new): void
    {
        $newParameters = [];
        foreach ($this->parameters as $k => $v) {
            $k = isset($params_new[$k]) ? $params_new[$k] : $k;
            $newParameters[$k] = $v;
        }
        $this->parameters = $newParameters;
    }

    public function toString(bool $newLine = false, $ljust = 0): string
    {
        $separator = $newLine ? "\n" : "";
        $templateName = $newLine ? trim($this->name) : $this->name;

        $result = "{{" . $templateName;
        $index = 1;
        foreach ($this->parameters as $key => $value) {
            $formattedValue = $newLine ? trim($value) : $value;

            if ($index == $key) {
                $result .= "|" . $formattedValue;
            } else {
                $formattedKey = $ljust > 0 ? str_pad($key, $ljust, " ") : $key;
                // $result .= $separator . "|" . $formattedKey . " = " . $formattedValue;
                $result .= $separator . "|" . $formattedKey . "=" . $formattedValue;
            }
            $index++;
        }
        $result .= $separator . "}}";
        return $result;
    }
    private function formatParameters(string $separator, int $ljust, bool $newLine): string
    {
        $result = "";
        $index = 1;
        foreach ($this->parameters as $key => $value) {
            $formattedValue = $newLine ? trim($value) : $value;

            if ($index == $key) {
                $result .= "|" . $formattedValue;
            } else {
                $formattedKey = $ljust > 0 ? str_pad($key, $ljust, " ") : $key;
                // $result .= $separator . "|" . $formattedKey . " = " . $formattedValue;
                $result .= $separator . "|" . $formattedKey . "=" . $formattedValue;
            }
            $index++;
        }

        return $result;
    }

    public function toString_new(bool $newLine = false, $ljust = 0): string
    {
        $separator = $newLine ? "\n" : "";
        $templateName = $newLine ? trim($this->name) : $this->name;

        $result = "{{" . $templateName;

        $result .= $this->formatParameters($separator, $ljust, $newLine);

        $result .= $separator . "}}";
        return $result;
    }
}
