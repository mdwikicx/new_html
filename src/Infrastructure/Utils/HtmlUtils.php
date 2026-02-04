<?PHP
/**
 * HTML fixing utilities
 *
 * Provides functions for fixing and cleaning HTML content, including
 * removing error divs, fixing red links, and removing data-parsoid attributes.
 *
 * @package MDWiki\NewHtml\Infrastructure\Utils
 */

namespace MDWiki\NewHtml\Infrastructure\Utils;

/**
 * Remove div elements with class="error" from HTML
 *
 * @param string $html The HTML to process
 * @return string The HTML with error divs removed
 */
function del_div_error(string $html): string
{

    preg_match_all("/<div([^\/>]*?)>(.+?)<\/div>/is", $html, $matches);

    foreach ($matches[1] as $key => $options) {
        // $content = $matches[2][$key];
        $cite_text = $matches[0][$key];
        if (preg_match("/class=[\"']error[\"']/is", $options)) {
            $html = str_replace($cite_text, '', $html);
        }
    }
    return $html;
}

/**
 * Parse HTML attributes from a text string
 *
 * @param string $text The text containing attributes
 * @return array<string, string> Array of attribute name-value pairs
 */
function get_attrs(string $text): array
{
    $text = "<ref $text>";
    $attrfind_tolerant = '/((?<=[\'"\s\/])[^\s\/>][^\s\/=>]*)(\s*=+\s*(\'[^\']*\'|"[^"]*"|(?![\'"])[^>\s]*))?(?:\s|\/(?!>))*/';
    $attrs = [];

    if (preg_match_all($attrfind_tolerant, $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attr_name = strtolower($match[1]);
            $attr_value = isset($match[3]) ? $match[3] : "";
            $attrs[$attr_name] = $attr_value;
        }
    }

    // var_export($attrs);
    return $attrs;
}

/**
 * Fix red links in HTML by removing edit-related attributes
 *
 * @param string $html The HTML to process
 * @return string The HTML with fixed links
 */
function fix_link_red(string $html): string
{

    preg_match_all("/<a([^>]*?)>(.+?)<\/a>/is", $html, $matches);

    $attrs_to_del = ['typeof', 'data-mw-i18n', 'class'];

    foreach ($matches[1] as $key => $options) {
        $content = $matches[2][$key];
        $cite_text = $matches[0][$key];

        // <a rel="mw:ExtLink" href="//en.wikipedia.org/w/index.php?title=Video:Pelvic_binder&amp;veaction=edit" class="external text"><span class="mw-ui-button mw-ui-progressive">Edit with VisualEditor</span></a>

        // if link has Edit with VisualEditor del it
        if (preg_match("/Edit with VisualEditor/is", $cite_text)) {
            $html = str_replace($cite_text, '', $html);
            continue;
        }

        // data-parsoid="{}"

        if (preg_match("/mw:LocalizedAttrs/is", $options)) {

            $attrs = get_attrs($options);

            $href = $attrs['href'] ?? '';

            if (strpos($href, 'action=edit') !== false) {
                $newHref = preg_replace('/\?action=edit.*?/', '', $href);
                $newHref = str_replace('&amp;redlink=1', '', $newHref);
                $newHref = str_replace('&redlink=1', '', $newHref);

                $attrs['href'] = $newHref;


                foreach ($attrs_to_del as $attr) {
                    if (isset($attrs[$attr])) {
                        unset($attrs[$attr]);
                    }
                }
            }

            $new_attrs = implode(' ', array_map(
                function ($key, $value) {
                    return "$key=$value";
                },
                array_keys($attrs),
                array_values($attrs)
            ));

            $new_cite_text = "<a $new_attrs>$content</a>";

            $html = str_replace($cite_text, $new_cite_text, $html);
        }
    }
    return $html;
}

/**
 * Remove data-parsoid attributes from HTML
 *
 * @param string $html The HTML to process
 * @return string The HTML with data-parsoid attributes removed
 */
function remove_data_parsoid(string $html): string
{

    if (empty($html)) return "";

    // replace all ( data-parsoid="{}")
    $html = preg_replace("/( data-parsoid=\"{}\")/is", '', $html);
    $html = preg_replace("/( data-parsoid=\'[^\']+\')/is", '', $html);
    $html = preg_replace("/( data-parsoid=\"[^\"]+\")/is", '', $html);

    preg_match_all("/<a([^>]*?)>(.+?)<\/a>/is", $html, $matches);

    $attrs_to_del = ['data-parsoid'];

    foreach ($matches[1] as $key => $options) {
        $content = $matches[2][$key];
        $cite_text = $matches[0][$key];

        if (preg_match("/data-parsoid/is", $options)) {

            $attrs = get_attrs($options);

            foreach ($attrs_to_del as $attr) {
                if (isset($attrs[$attr])) {
                    unset($attrs[$attr]);
                }
            }

            $new_attrs = implode(' ', array_map(
                function ($key, $value) {
                    return "$key=$value";
                },
                array_keys($attrs),
                array_values($attrs)
            ));

            $new_cite_text = "<a $new_attrs>$content</a>";

            $html = str_replace($cite_text, $new_cite_text, $html);
        }
    }

    return $html;
}
