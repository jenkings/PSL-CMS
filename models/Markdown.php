<?php

class Markdown
{
    /**
     * Convert Markdown to HTML
     *
     * @param string $markdownString The Markdown string to be converted
     * @return string The HTML result
     */
    public function convertToHtml($markdownString){
        // Basic parsing for bold (surrounding text with **)
        $markdownString = $this->parseBold($markdownString);

        // Basic parsing for italic (surrounding text with *)
        $markdownString = $this->parseItalic($markdownString);

        // Table parsing
        $markdownString = $this->parseTables($markdownString);

        // Horizontal line parsing (--- or *** or ___)
        $markdownString = $this->horizontalLinesParsing($markdownString);

        // Basic parsing for headers (lines starting with #)
        $markdownString = $this->parseHeaders($markdownString);

        // Basic parsing for ordered lists
        $markdownString = $this->parseOrderedLists($markdownString);

        // Basic parsing for unordered lists
        $markdownString = $this->parseUnorderedLists($markdownString);

        // Code block parsing (lines surrounded with ``` or indented with 4 spaces)
        $markdownString = $this->parseCodeBlocks($markdownString);

        // Blockquote parsing (lines starting with >)
        $markdownString = $this->parseBlockquote($markdownString);

        // Link parsing ([text](url))
        $markdownString = $this->parseLinks($markdownString);

        // Image parsing (![alt text](url))
        $markdownString = $this->parseImages($markdownString);

        return $markdownString;
    }

    private function parseHeaders($input)
    {
        return preg_replace_callback('/^(#+)(.*)/m', function ($matches) {
            $level = strlen($matches[1]);
            return "<h$level>{$matches[2]}</h$level>";
        }, $input);
    }

    private function parseBold($input)
    {
        return preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $input);
    }

    private function parseItalic($input)
    {
        return preg_replace('/\*(.+[^*])\*/', '<em>$1</em>', $input);
    }


    function parseOrderedLists($input){
        $html = preg_replace_callback('/^\s*(\d+)\.\s*(.*)(\n\s*\d+\.\s*.*)*/m', function ($matches) {
            $listItems = array_map('trim', explode("\n", $matches[0]));
            $listItems = array_map(function ($item) {
                return preg_replace('/^\d+\.\s*/', '', $item); // Remove the number and dot at the beginning
            }, $listItems);

            return '<ol><li>' . implode('</li><li>', $listItems) . '</li></ol>';
        }, $input);

        return $html;
    }

    function parseUnorderedLists($input){
    // Match contiguous lines starting with *, -, or + as list items
    $html = preg_replace_callback('/^\s*([-*+])\s*(.*)(\n\s*[-*+]\s*.*)*/m', function ($matches) {
        $listItems = array_map('trim', explode("\n", $matches[0]));
        $listItems = array_map(function ($item) {
            return preg_replace('/^[-*+]\s*/', '', $item); // Remove the *, -, or + at the beginning
        }, $listItems);

        return '<ul><li>' . implode('</li><li>', $listItems) . '</li></ul>';
    }, $input);

    return $html;
    }


    private function parseCodeBlocks($input)
    {
        $input = preg_replace_callback('/```(.+?)```/s', function ($matches) {
            return "<pre><code>{$matches[1]}</code></pre>";
        }, $input);
        $input = preg_replace_callback('/^\s{4}(.+)$/m', function ($matches) {
            return "<pre><code>{$matches[1]}</code></pre>";
        }, $input);
        return $input;
    }

    private function parseBlockquote($input)
    {
        return preg_replace_callback('/^\s*>\s*(.*)/m', function ($matches) {
            return "<blockquote>{$matches[1]}</blockquote>";
        }, $input);
    }

    private function parseLinks($input)
    {
        return preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $input);
    }

    private function parseImages($input)
    {
        return preg_replace('/!\[(.*?)\]\((.*?)\)/', '<img src="$2" alt="$1">', $input);
    }

    private function horizontalLinesParsing($input){
        return preg_replace('/^(.*?)(---|___|\*\*\*)(.*?)$/m', '$1<hr>$3', $input);
    }

private function parseTables($input)
{
    $tables = [];
    $currentTable = '';
    $inTable = false;

    $lines = explode("\n", $input);

    foreach ($lines as $line) {
        if (preg_match('/^\s*\|(.+)\|\s*$/', $line, $matches)) {
            $tableRow = trim($matches[1]);
            $columns = explode('|', $tableRow);
            $columns = array_map('trim', $columns);

            // Skip lines with only dashes or empty columns
            if (preg_match('/^\s*-{3,}\s*$/', $columns[0])) {
                $inTable = true;
                continue;
            }

            $tableHtml = '';

            if (!$inTable) {
                $tableHtml .= '<table class="table table-bordered">';
            }

            $tableHtml .= '<tr>';
            foreach ($columns as $column) {
                $cellTag = $inTable ? 'td' : 'th';
                $tableHtml .= "<$cellTag>$column</$cellTag>";
            }
            $tableHtml .= '</tr>';

            $currentTable .= $tableHtml;
        } else {
            if ($inTable) {
                $currentTable .= '</table>';
                $tables[] = $currentTable;
                $currentTable = '';
                $inTable = false;
            }
            $tables[] = $line;
        }
    }

    // Check if there's a remaining table
    if ($inTable && !empty($currentTable)) {
        $currentTable .= '</table>';
        $tables[] = $currentTable;
    }

    return implode("\n", $tables);
}






}