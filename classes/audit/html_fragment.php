<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Safe DOM wrapper for Moodle HTML fragments.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\audit;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Parse an HTML fragment without executing or rendering it.
 */
final class html_fragment {
    /** @var DOMDocument */
    private DOMDocument $document;

    /** @var DOMXPath */
    private DOMXPath $xpath;

    /**
     * Constructor.
     *
     * @param string $html HTML fragment.
     */
    public function __construct(string $html) {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $wrapped = '<?xml encoding="UTF-8"><div id="local-accessibilityai-root">' . $html . '</div>';
        $this->document->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $this->xpath = new DOMXPath($this->document);
    }

    /**
     * Query DOM nodes.
     *
     * @param string $query XPath expression.
     * @return \DOMNodeList
     */
    public function query(string $query): \DOMNodeList {
        return $this->xpath->query($query);
    }

    /**
     * Return normalized text for a node.
     *
     * @param \DOMNode $node Node.
     * @return string
     */
    public static function text(\DOMNode $node): string {
        return preg_replace('/\s+/u', ' ', trim((string)$node->textContent)) ?? '';
    }

    /**
     * Return the root element.
     *
     * @return DOMElement|null
     */
    public function root(): ?DOMElement {
        $nodes = $this->query('//*[@id="local-accessibilityai-root"]');
        return $nodes->item(0) instanceof DOMElement ? $nodes->item(0) : null;
    }
}
