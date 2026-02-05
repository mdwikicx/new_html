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
 * Parser for a single MediaWiki template
 */
class ParserTemplate
{
    private string $templateText;
    private string $name;
    /** @var array<string|int, string> */
    private array $parameters;
    private string $pipe = "|";
    private string $pipeR = "-_-";

    /**
     * Constructor for ParserTemplate
     *
     * @param string $templateText The template text to parse
     */
    public function __construct(string $templateText)
    {
        $this->templateText = trim($templateText);
        $this->name = "";
        $this->parameters = [];
        $this->parse();
    }

    /**
     * Parse the template text into name and parameters
     *
     * @return void
     */
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

            // $pipe = $this->pipe;
            // $pipeR = $this->pipeR;
            // $DTemplate = preg_replace_callback("/\{\{(.*?)\}\}/s", function ($m) use ($pipe, $pipeR) {
            //     return str_replace($pipe, $pipeR, $m[0]);
            // }, $DTemplate);
            // $DTemplate = preg_replace_callback("/\[\[(.*?)\]\]/s", function ($m) use ($pipe, $pipeR) {
            //     return str_replace($pipe, $pipeR, $m[0]);
            // }, $DTemplate);

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

    /**
     * Get the parsed Template object
     *
     * @return Template The parsed template
     */
    public function getTemplate(): Template
    {
        return new Template($this->name, $this->parameters, $this->templateText);
    }
}
