<?php

/**
 * MediaWiki template parsing utilities
 *
 * Provides classes and functions for parsing MediaWiki templates
 * from wikitext, including support for nested templates and
 * parameter extraction.
 *
 * @package MDWiki\NewHtml\WikiParse
 */

namespace MDWiki\NewHtml\Domain\Parser;

/**
 * Represents a parsed MediaWiki template with its name and parameters
 */
class Template
{
    private string $name;
    private string $name_strip;
    private string $templateText;
    /** @var array<string|int, string> */
    private array $parameters;

    /**
     * Constructor for Template
     *
     * @param string $name The template name
     * @param array<string|int, string> $parameters The template parameters
     * @param string $templateText The original template text
     */
    public function __construct(string $name, array $parameters = [], string $templateText = "")
    {
        $this->name = $name;
        $this->name_strip = trim(str_replace('_', ' ', $name));
        $this->parameters = $parameters;
        $this->templateText = $templateText;
    }
    /**
     * Get the original template text
     *
     * @return string The template text
     */
    public function getTemplateText(): string
    {
        return $this->templateText;
    }
    /**
     * Get the template name
     *
     * @return string The template name
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Get the stripped template name (without underscores, trimmed)
     *
     * @return string The stripped template name
     */
    public function getStripName(): string
    {
        return $this->name_strip;
    }
    /**
     * Get all template parameters
     *
     * @return array<string|int, string> The parameters array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Delete a parameter by key
     *
     * @param string $key The parameter key to delete
     * @return void
     */
    public function deleteParameter(string $key): void
    {
        if (array_key_exists($key, $this->parameters)) {
            unset($this->parameters[$key]);
        }
    }

    /**
     * Get a parameter value by key with optional default
     *
     * @param string $key The parameter key
     * @param string $default The default value if key not found
     * @return string The parameter value or default
     */
    public function getParameter(string $key, string $default = ""): string
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * Set the template name
     *
     * @param string $name The new template name
     * @return void
     */
    public function setTempName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set a parameter value
     *
     * @param string $key The parameter key
     * @param string $value The parameter value
     * @return void
     */
    public function setParameter(string $key, string $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Change a parameter name from old to new
     *
     * @param string $old The old parameter name
     * @param string $new The new parameter name
     * @return void
     */
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

    /**
     * Change multiple parameter names at once
     *
     * @param array<string, string> $params_new Mapping of old names to new names
     * @return void
     */
    public function changeParametersNames(array $params_new): void
    {
        $newParameters = [];
        foreach ($this->parameters as $k => $v) {
            $k = isset($params_new[$k]) ? $params_new[$k] : $k;
            $newParameters[$k] = $v;
        }
        $this->parameters = $newParameters;
    }

    /**
     * Convert template to string representation
     *
     * @param bool $newLine Whether to add newlines between parameters
     * @param int $ljust Left justify parameter names to this width
     * @return string The template as a string
     */
    public function toString(bool $newLine = false, int $ljust = 0): string
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

    /**
     * Format template parameters as string
     *
     * @param string $separator Separator between parameters
     * @param int $ljust Left justify parameter names to this width
     * @param bool $newLine Whether to trim parameter values
     * @return string The formatted parameters
     */
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

    /**
     * Convert template to string representation (alternative implementation)
     *
     * @param bool $newLine Whether to add newlines between parameters
     * @param int $ljust Left justify parameter names to this width
     * @return string The template as a string
     */
    public function toString_new(bool $newLine = false, int $ljust = 0): string
    {
        $separator = $newLine ? "\n" : "";
        $templateName = $newLine ? trim($this->name) : $this->name;

        $result = "{{" . $templateName;

        $result .= $this->formatParameters($separator, $ljust, $newLine);

        $result .= $separator . "}}";
        return $result;
    }
}
