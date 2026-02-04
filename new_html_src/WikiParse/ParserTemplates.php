<?php

namespace WikiParse\Template;

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
    public function getParameter(string $key, string $default = ""): string
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

class ParserTemplate
{
    private string $templateText;
    private string $name;
    private array $parameters;
    private string $pipe = "|";
    private string $pipeR = "-_-";
    public function __construct(string $templateText)
    {
        $this->templateText = trim($templateText);
        $this->name = "";
        $this->parameters = [];
        $this->parse();
    }
    public function parse(): void
    {
        $this->name = "";
        $this->parameters = [];
        if (preg_match("/^\{\{(.*?)(\}\})$/s", $this->templateText, $matchesR)) {
            $DTemplate = $matchesR[1];
            $matches = [];
            preg_match_all("/\{\{(.*?)\}\}/", $DTemplate, $matches);
            foreach ($matches[1] as $matche) {
                $DTemplate = str_replace($matche, str_replace($this->pipe, $this->pipeR, $matche), $DTemplate);
            }
            $matches = [];
            preg_match_all("/\[\[(.*?)\]\]/", $DTemplate, $matches);
            foreach ($matches[1] as $matche) {
                $DTemplate = str_replace($matche, str_replace($this->pipe, $this->pipeR, $matche), $DTemplate);
            }

            $params = explode("|", $DTemplate);
            $pipeR = $this->pipeR;
            $pipe = $this->pipe;
            $params = array_map(function ($string) use ($pipeR, $pipe) {
                return str_replace($pipeR, $pipe, $string);
            }, $params);
            $data = [];
            $this->name = $params[0];
            for ($i = 1; $i < count($params); $i++) {
                $param = $params[$i];
                if (strpos($param, "=") !== false) {
                    $parts = explode("=", $param, 2);
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    $data[$key] = $value;
                } else {
                    $data[$i] = $param;
                }
            }
            $this->parameters = $data;
        }
    }
    public function getTemplate(): Template
    {
        return new Template($this->name, $this->parameters, $this->templateText);
    }
}

class ParserTemplates
{
    private string $text;
    private array $templates;
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->templates = [];
        $this->parse();
    }
    private function find_sub_templates($string)
    {
        preg_match_all("/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/xm", $string, $matches);

        return $matches;
    }
    private function parse_sub($text): void
    {
        $text_templates = $this->find_sub_templates($text);
        foreach ($text_templates[0] as $text_template) {
            $_parser = new ParserTemplate($text_template);
            $this->templates[] = $_parser->getTemplate();
        }
        // echo "lenth this->templates:" . count($this->templates) . "\n";
    }
    public function parse(): void
    {
        $text_templates = $this->find_sub_templates($this->text);
        foreach ($text_templates[0] as $text_template) {
            $_parser = new ParserTemplate($text_template);
            $this->templates[] = $_parser->getTemplate();
            $text_template2 = trim($text_template);
            // remove first 2 litters and 2 last
            $text_template2 = substr($text_template2, 2, -2);
            $this->parse_sub($text_template2);
        }
        // echo "lenth this->templates:" . count($this->templates) . "\n";
    }
    public function getTemplates(): array
    {
        return $this->templates;
    }
}

function getTemplates($text)
{
    if (empty($text)) {
        return [];
    }
    $parser = new ParserTemplates($text);
    $temps = $parser->getTemplates();
    return $temps;
}
