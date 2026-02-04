
# Overview
This script is used to generate `segmentedContent` to be used by [ContentTranslation tool](https://github.com/mdwikicx/cx-1).

# How it's working
This script processes `mdwiki.org` article and generates different formats of content based on the provided parameters. By default it will output `segmentedContent` in JSON format.

- `printetxt` parameter can be set to `wikitext`, `html`, or `seg`. to output different formats of content.

# Steps:
1. **Wikitext Generation**: Fetch the wikitext and revision ID of the given title from [mdwiki.org REST API](https://mdwiki.org/w/rest.php/v1/page/title)

2. **HTML Generation**: Generate HTML from the wikitext using [enwiki rest.php](https://en.wikipedia.org/w/rest.php/v1/transform/wikitext/to/html/title)

3. **Segmented Content Generation**: Generate segmented content, using [HtmltoSegments tool](https://ncc2c.toolforge.org/HtmltoSegments)

4. **JSON Data Preparation**: Prepares a JSON object with the source language, title, revision ID, segmented content, and categories.

5. **Error Handling**: If no content is found, it sets the HTTP response code to 404 and adds an error message to the JSON data.

## Output:
- **JSON Output**: Encodes the JSON data with appropriate options and outputs it. (default)
