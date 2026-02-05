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
 * Parser for multiple MediaWiki templates in text
 */
class ParserTemplates
{
    private string $text;
    /** @var array<int, Template> */
    private array $templates;

    /**
     * Constructor for ParserTemplates
     *
     * @param string $text The text containing templates to parse
     */
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->templates = [];
        $this->parse();
    }
    /**
     * Find all sub-templates in a string using regex recursion
     *
     * @param string $string The string to search
     * @return array<int, array<int, string>> Array of matches
     */
    private function find_sub_templates(string $string): array
    {
        preg_match_all("/\{{2}((?>[^\{\}]+)|(?R))*\}{2}/xm", $string, $matches);
        return $matches;
    }

    /**
     * Parse sub-templates from text
     *
     * @param string $text The text to parse
     * @return void
     */
    private function parse_sub(string $text): void
    {
        $text_templates = $this->find_sub_templates($text);
        foreach ($text_templates[0] as $text_template) {
            $_parser = new ParserTemplate($text_template);
            $this->templates[] = $_parser->getTemplate();
        }
        // echo "lenth this->templates:" . count($this->templates) . "\n";
    }

    /**
     * Parse all templates from the text
     *
     * @return void
     */
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

    /**
     * Get all parsed templates
     *
     * @return array<int, Template> Array of Template objects
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }
}

/**
 * Helper function to get all templates from text
 *
 * @param string $text The text to parse
 * @return array<int, Template> Array of Template objects
 */
function getTemplates(string $text): array
{
    if (empty($text)) {
        return [];
    }
    $parser = new ParserTemplates($text);
    $temps = $parser->getTemplates();
    return $temps;
}
