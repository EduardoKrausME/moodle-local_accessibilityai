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
 * Build a minimized semantic payload for AI review.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\ai;

use DOMElement;
use local_accessibilityai\audit\html_fragment;
use local_accessibilityai\content\content_item;

/**
 * Convert HTML into semantic features needed by AI, without sending edit URLs or unrelated Moodle data.
 */
final class payload_builder {
    /**
     * Maximum plain text characters per item.
     */
    private const MAX_TEXT = 7000;

    /**
     * Build one item payload.
     *
     * @param content_item $item Content item.
     * @param array $localruleids Deterministic rule IDs already found.
     * @return array
     */
    public function build(content_item $item, array $localruleids): array {
        $fragment = new html_fragment($item->html);
        $root = $fragment->root();
        $plain = $root ? html_fragment::text($root) : strip_tags($item->html);

        $payload = [
            'itemid' => $item->id,
            'source' => $item->source,
            'title' => $this->limit($item->title, 300),
            'location' => $this->limit($item->location, 500),
            'plain_text' => $this->limit($plain, self::MAX_TEXT),
            'headings' => [],
            'links' => [],
            'images' => [],
            'local_findings' => array_values(array_unique($localruleids)),
        ];

        foreach ($fragment->query('//h1|//h2|//h3|//h4|//h5|//h6') as $index => $node) {
            if (!$node instanceof DOMElement || count($payload['headings']) >= 40) {
                continue;
            }
            $payload['headings'][] = [
                'element' => 'heading:' . ($index + 1),
                'level' => (int)substr($node->tagName, 1),
                'text' => $this->limit(html_fragment::text($node), 300),
            ];
        }

        foreach ($fragment->query('//a') as $index => $node) {
            if (!$node instanceof DOMElement || count($payload['links']) >= 40) {
                continue;
            }
            $payload['links'][] = [
                'element' => 'link:' . ($index + 1),
                'text' => $this->limit($this->link_text($node), 300),
                'target' => $this->safe_target($node->getAttribute('href')),
                'context' => $this->limit($this->nearby_text($node), 450),
            ];
        }

        foreach ($fragment->query('//img') as $index => $node) {
            if (!$node instanceof DOMElement || count($payload['images']) >= 30) {
                continue;
            }
            $srcpath = parse_url($node->getAttribute('src'), PHP_URL_PATH);
            $payload['images'][] = [
                'element' => 'img:' . ($index + 1),
                'alt' => $node->hasAttribute('alt') ? $this->limit($node->getAttribute('alt'), 500) : null,
                'title' => $this->limit($node->getAttribute('title'), 300),
                'filename' => is_string($srcpath) ? $this->limit(basename($srcpath), 150) : '',
                'context' => $this->limit($this->nearby_text($node), 450),
            ];
        }

        return $payload;
    }

    /**
     * Return human-visible or accessible text for a link.
     *
     * @param DOMElement $node Link element.
     * @return string
     */
    private function link_text(DOMElement $node): string {
        $values = [html_fragment::text($node), $node->getAttribute('aria-label'), $node->getAttribute('title')];
        foreach ($values as $value) {
            $value = trim($value);
            if ($value !== '') {
                return $value;
            }
        }
        foreach ($node->getElementsByTagName('img') as $image) {
            if ($image instanceof DOMElement && trim($image->getAttribute('alt')) !== '') {
                return trim($image->getAttribute('alt'));
            }
        }
        return '';
    }

    /**
     * Return nearby textual context without raw HTML.
     *
     * @param DOMElement $node Node.
     * @return string
     */
    private function nearby_text(DOMElement $node): string {
        $parent = $node->parentNode;
        if ($parent) {
            return html_fragment::text($parent);
        }
        return '';
    }

    /**
     * Keep only URL host/path information. Query strings and fragments are omitted.
     *
     * @param string $href Href.
     * @return string
     */
    private function safe_target(string $href): string {
        $href = trim($href);
        if ($href === '') {
            return '';
        }
        if (str_starts_with($href, '#')) {
            return '#fragment';
        }
        $parts = parse_url($href);
        if ($parts === false) {
            return '';
        }
        $host = isset($parts['host']) ? $parts['host'] : '';
        $path = isset($parts['path']) ? $parts['path'] : '';
        return $this->limit(($host !== '' ? $host : '') . $path, 300);
    }

    /**
     * Limit text length and normalize whitespace.
     *
     * @param string $value Input.
     * @param int $max Maximum characters.
     * @return string
     */
    private function limit(string $value, int $max): string {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';
        return mb_strlen($value) <= $max ? $value : mb_substr($value, 0, $max - 1) . '…';
    }
}
