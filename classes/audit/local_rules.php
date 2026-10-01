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
 * Deterministic HTML accessibility checks.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\audit;

use DOMElement;
use DOMNode;

/**
 * Run checks that can be derived locally from stored HTML.
 */
final class local_rules {
    /**
     * Audit one HTML fragment.
     *
     * @param string $html HTML fragment.
     * @return finding[]
     */
    public function audit(string $html): array {
        if (trim($html) === '') {
            return [];
        }

        $fragment = new html_fragment($html);
        $findings = [];
        $this->check_images($fragment, $findings);
        $this->check_headings($fragment, $findings);
        $this->check_links($fragment, $findings);
        $this->check_tables($fragment, $findings);
        $this->check_problematic_html($fragment, $findings);
        $this->check_media($fragment, $findings);
        $this->check_iframes($fragment, $findings);
        $this->check_inline_contrast($fragment, $findings);
        return $findings;
    }

    /**
     * Check image alternative text.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_images(html_fragment $fragment, array &$findings): void {
        foreach ($fragment->query('//img') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $element = $this->element_label($node, $index + 1);
            if (!$node->hasAttribute('alt')) {
                $findings[] = new finding(
                    'img_missing_alt',
                    'error',
                    $element,
                    get_string('rule_img_missing_alt_reason', 'local_accessibilityai'),
                    get_string('rule_img_missing_alt_suggestion', 'local_accessibilityai')
                );
                continue;
            }

            if (trim($node->getAttribute('alt')) === '' && !$this->looks_decorative($node)) {
                $findings[] = new finding(
                    'img_empty_alt_suspect',
                    'warning',
                    $element,
                    get_string('rule_img_empty_alt_reason', 'local_accessibilityai'),
                    get_string('rule_img_empty_alt_suggestion', 'local_accessibilityai')
                );
            }
        }
    }

    /**
     * Check heading order and obvious presentation misuse signals.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_headings(html_fragment $fragment, array &$findings): void {
        $previouslevel = null;
        foreach ($fragment->query('//h1|//h2|//h3|//h4|//h5|//h6') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $level = (int)substr(strtolower($node->tagName), 1);
            $text = html_fragment::text($node);
            $element = '<' . strtolower($node->tagName) . '> ' . $this->shorten($text, 80);

            if ($previouslevel !== null && $level > $previouslevel + 1) {
                $findings[] = new finding(
                    'heading_level_skip',
                    'warning',
                    $element,
                    get_string('rule_heading_skip_reason', 'local_accessibilityai', (object)[
                        'from' => $previouslevel,
                        'to' => $level,
                    ]),
                    get_string('rule_heading_skip_suggestion', 'local_accessibilityai')
                );
            }
            $previouslevel = $level;

            if ($text === '') {
                $findings[] = new finding(
                    'heading_empty',
                    'error',
                    $element,
                    get_string('rule_heading_empty_reason', 'local_accessibilityai'),
                    get_string('rule_heading_empty_suggestion', 'local_accessibilityai')
                );
            }

            $class = strtolower($node->getAttribute('class'));
            $style = strtolower($node->getAttribute('style'));
            $visualclass = preg_match('/(?:^|\s)(?:display-\d|text-center|text-uppercase|fw-|font-)/', $class);
            $visualstyle = preg_match('/(?:font-size|font-weight|text-align|color|background)/', $style);
            if ($text !== '' && mb_strlen($text) <= 120 && ($visualclass || $visualstyle)) {
                $findings[] = new finding(
                    'heading_presentation_suspect',
                    'warning',
                    $element,
                    get_string('rule_heading_presentation_reason', 'local_accessibilityai'),
                    get_string('rule_heading_presentation_suggestion', 'local_accessibilityai')
                );
            }
        }
    }

    /**
     * Check links.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_links(html_fragment $fragment, array &$findings): void {
        $generic = [
            'aqui', 'clique aqui', 'click here', 'here', 'saiba mais', 'learn more', 'mais', 'more', 'link',
        ];
        foreach ($fragment->query('//a') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $text = $this->accessible_link_text($node);
            $element = $this->element_label($node, $index + 1);
            if ($text === '') {
                $findings[] = new finding(
                    'link_empty_text',
                    'error',
                    $element,
                    get_string('rule_link_empty_reason', 'local_accessibilityai'),
                    get_string('rule_link_empty_suggestion', 'local_accessibilityai')
                );
                continue;
            }
            if (in_array(mb_strtolower(trim($text)), $generic, true)) {
                $findings[] = new finding(
                    'link_generic_text',
                    'warning',
                    $element . ' — ' . $this->shorten($text, 60),
                    get_string('rule_link_generic_reason', 'local_accessibilityai'),
                    get_string('rule_link_generic_suggestion', 'local_accessibilityai')
                );
            }
        }
    }

    /**
     * Check data table structure.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_tables(html_fragment $fragment, array &$findings): void {
        foreach ($fragment->query('//table') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $role = mb_strtolower(trim($node->getAttribute('role')));
            if (in_array($role, ['presentation', 'none'], true)) {
                continue;
            }
            $element = 'table #' . ($index + 1);
            $headers = $node->getElementsByTagName('th');
            if ($headers->length === 0) {
                $findings[] = new finding(
                    'table_missing_headers',
                    'warning',
                    $element,
                    get_string('rule_table_headers_reason', 'local_accessibilityai'),
                    get_string('rule_table_headers_suggestion', 'local_accessibilityai')
                );
                continue;
            }

            foreach ($headers as $header) {
                if ($header instanceof DOMElement && !$header->hasAttribute('scope') && !$header->hasAttribute('id')) {
                    $findings[] = new finding(
                        'table_header_relationship',
                        'warning',
                        $element,
                        get_string('rule_table_relationship_reason', 'local_accessibilityai'),
                        get_string('rule_table_relationship_suggestion', 'local_accessibilityai')
                    );
                    break;
                }
            }
        }
    }

    /**
     * Check obsolete or problematic HTML patterns.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_problematic_html(html_fragment $fragment, array &$findings): void {
        foreach (['font', 'center', 'marquee', 'blink'] as $tag) {
            foreach ($fragment->query('//' . $tag) as $index => $node) {
                $findings[] = new finding(
                    'obsolete_html_element',
                    'warning',
                    '<' . $tag . '> #' . ($index + 1),
                    get_string('rule_obsolete_html_reason', 'local_accessibilityai', '<' . $tag . '>'),
                    get_string('rule_obsolete_html_suggestion', 'local_accessibilityai')
                );
            }
        }

        foreach ($fragment->query('//*[@tabindex]') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $value = trim($node->getAttribute('tabindex'));
            if (is_numeric($value) && (int)$value > 0) {
                $findings[] = new finding(
                    'positive_tabindex',
                    'warning',
                    $this->element_label($node, $index + 1),
                    get_string('rule_positive_tabindex_reason', 'local_accessibilityai'),
                    get_string('rule_positive_tabindex_suggestion', 'local_accessibilityai')
                );
            }
        }

        $ids = [];
        foreach ($fragment->query('//*[@id]') as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $id = trim($node->getAttribute('id'));
            if ($id === '' || $id === 'local-accessibilityai-root') {
                continue;
            }
            if (isset($ids[$id])) {
                $findings[] = new finding(
                    'duplicate_id',
                    'warning',
                    '#' . $this->shorten($id, 60),
                    get_string('rule_duplicate_id_reason', 'local_accessibilityai'),
                    get_string('rule_duplicate_id_suggestion', 'local_accessibilityai')
                );
                continue;
            }
            $ids[$id] = true;
        }
    }

    /**
     * Check detectable media caption or transcript indicators.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_media(html_fragment $fragment, array &$findings): void {
        $root = $fragment->root();
        $alltext = $root ? mb_strtolower(html_fragment::text($root)) : '';
        $hastranscriptword = preg_match('/\b(transcri(?:ção|cao|pt)|transcript|legenda|caption|subtitle)s?\b/u', $alltext);

        foreach ($fragment->query('//video') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $hastrack = false;
            foreach ($node->getElementsByTagName('track') as $track) {
                if (!$track instanceof DOMElement) {
                    continue;
                }
                $kind = mb_strtolower(trim($track->getAttribute('kind')));
                if (in_array($kind, ['captions', 'subtitles'], true)) {
                    $hastrack = true;
                    break;
                }
            }
            if (!$hastrack && !$hastranscriptword) {
                $findings[] = new finding(
                    'video_caption_not_detected',
                    'warning',
                    'video #' . ($index + 1),
                    get_string('rule_video_caption_reason', 'local_accessibilityai'),
                    get_string('rule_video_caption_suggestion', 'local_accessibilityai')
                );
            }
        }

        foreach ($fragment->query('//audio') as $index => $node) {
            if (!$hastranscriptword) {
                $findings[] = new finding(
                    'audio_transcript_not_detected',
                    'warning',
                    'audio #' . ($index + 1),
                    get_string('rule_audio_transcript_reason', 'local_accessibilityai'),
                    get_string('rule_audio_transcript_suggestion', 'local_accessibilityai')
                );
            }
        }

        foreach ($fragment->query('//iframe[@src]') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $src = mb_strtolower($node->getAttribute('src'));
            if ((str_contains($src, 'youtube.') || str_contains($src, 'youtu.be') || str_contains($src, 'vimeo.'))
                && !$hastranscriptword) {
                $findings[] = new finding(
                    'embedded_media_transcript_not_detected',
                    'warning',
                    'iframe media #' . ($index + 1),
                    get_string('rule_embedded_media_reason', 'local_accessibilityai'),
                    get_string('rule_embedded_media_suggestion', 'local_accessibilityai')
                );
            }
        }
    }

    /**
     * Check iframe titles.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_iframes(html_fragment $fragment, array &$findings): void {
        foreach ($fragment->query('//iframe') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            if (!$node->hasAttribute('title') || trim($node->getAttribute('title')) === '') {
                $findings[] = new finding(
                    'iframe_missing_title',
                    'error',
                    'iframe #' . ($index + 1),
                    get_string('rule_iframe_title_reason', 'local_accessibilityai'),
                    get_string('rule_iframe_title_suggestion', 'local_accessibilityai')
                );
            }
        }
    }

    /**
     * Check simple inline color contrast only when both opaque colors are explicit on the same element.
     *
     * @param html_fragment $fragment Parsed fragment.
     * @param array $findings Findings accumulator.
     * @return void
     */
    private function check_inline_contrast(html_fragment $fragment, array &$findings): void {
        foreach ($fragment->query('//*[@style]') as $index => $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            $styles = $this->parse_style($node->getAttribute('style'));
            if (!isset($styles['color'])) {
                continue;
            }
            $background = $styles['background-color'] ?? ($styles['background'] ?? null);
            if ($background === null) {
                continue;
            }
            $foregroundrgb = $this->parse_color($styles['color']);
            $backgroundrgb = $this->parse_color($background);
            if ($foregroundrgb === null || $backgroundrgb === null) {
                continue;
            }
            $ratio = $this->contrast_ratio($foregroundrgb, $backgroundrgb);
            if ($ratio >= 4.5) {
                continue;
            }
            $findings[] = new finding(
                'inline_contrast_low',
                'warning',
                $this->element_label($node, $index + 1),
                get_string('rule_contrast_reason', 'local_accessibilityai', number_format($ratio, 2, '.', '')),
                get_string('rule_contrast_suggestion', 'local_accessibilityai')
            );
        }
    }

    /**
     * Detect explicit hints that an empty alt was intentionally decorative.
     *
     * @param DOMElement $node Image node.
     * @return bool
     */
    private function looks_decorative(DOMElement $node): bool {
        $role = mb_strtolower(trim($node->getAttribute('role')));
        $ariahidden = mb_strtolower(trim($node->getAttribute('aria-hidden')));
        $class = mb_strtolower($node->getAttribute('class'));
        return in_array($role, ['presentation', 'none'], true)
            || $ariahidden === 'true'
            || preg_match('/(?:decorative|decoration|spacer|separator)/', $class) === 1;
    }

    /**
     * Resolve link text from visible text and common accessible labels.
     *
     * @param DOMElement $node Link node.
     * @return string
     */
    private function accessible_link_text(DOMElement $node): string {
        $visible = html_fragment::text($node);
        if ($visible !== '' && preg_match('/[\p{L}\p{N}]/u', $visible)) {
            return preg_replace('/\s+/u', ' ', $visible) ?? '';
        }

        $parts = [
            trim($node->getAttribute('aria-label')),
            trim($node->getAttribute('title')),
        ];
        foreach ($node->getElementsByTagName('img') as $image) {
            if ($image instanceof DOMElement && $image->hasAttribute('alt')) {
                $parts[] = trim($image->getAttribute('alt'));
            }
        }
        foreach ($parts as $part) {
            if ($part !== '') {
                return preg_replace('/\s+/u', ' ', $part) ?? '';
            }
        }
        return '';
    }

    /**
     * Create a non-HTML element label safe for report output.
     *
     * @param DOMElement $node Element.
     * @param int $index Index.
     * @return string
     */
    private function element_label(DOMElement $node, int $index): string {
        $tag = mb_strtolower($node->tagName);
        $label = $tag . ' #' . $index;
        if ($tag === 'img' && $node->hasAttribute('src')) {
            $path = parse_url($node->getAttribute('src'), PHP_URL_PATH);
            if (is_string($path) && $path !== '') {
                $label .= ' (' . $this->shorten(basename($path), 60) . ')';
            }
        }
        if ($tag === 'a') {
            $text = $this->accessible_link_text($node);
            if ($text !== '') {
                $label .= ' (' . $this->shorten($text, 60) . ')';
            }
        }
        return $label;
    }

    /**
     * Parse an inline style attribute.
     *
     * @param string $style Inline CSS.
     * @return array
     */
    private function parse_style(string $style): array {
        $result = [];
        foreach (explode(';', $style) as $declaration) {
            if (!str_contains($declaration, ':')) {
                continue;
            }
            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            if ($property !== '' && $value !== '') {
                $result[mb_strtolower($property)] = mb_strtolower($value);
            }
        }
        return $result;
    }

    /**
     * Parse a simple opaque CSS color.
     *
     * @param string $color CSS color.
     * @return array|null RGB tuple.
     */
    private function parse_color(string $color): ?array {
        $color = trim($color);
        $named = [
            'black' => [0, 0, 0], 'white' => [255, 255, 255], 'red' => [255, 0, 0],
            'green' => [0, 128, 0], 'blue' => [0, 0, 255], 'gray' => [128, 128, 128],
            'grey' => [128, 128, 128], 'yellow' => [255, 255, 0],
        ];
        if (isset($named[$color])) {
            return $named[$color];
        }
        if (preg_match('/^#([0-9a-f]{3})$/i', $color, $matches)) {
            return array_map(static fn(string $char): int => hexdec($char . $char), str_split($matches[1]));
        }
        if (preg_match('/^#([0-9a-f]{6})$/i', $color, $matches)) {
            return [
                hexdec(substr($matches[1], 0, 2)),
                hexdec(substr($matches[1], 2, 2)),
                hexdec(substr($matches[1], 4, 2)),
            ];
        }
        if (preg_match('/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', $color, $matches)) {
            $rgb = [(int)$matches[1], (int)$matches[2], (int)$matches[3]];
            foreach ($rgb as $value) {
                if ($value < 0 || $value > 255) {
                    return null;
                }
            }
            return $rgb;
        }
        return null;
    }

    /**
     * Calculate WCAG contrast ratio for two sRGB colors.
     *
     * @param array $first RGB tuple.
     * @param array $second RGB tuple.
     * @return float
     */
    private function contrast_ratio(array $first, array $second): float {
        $l1 = $this->relative_luminance($first);
        $l2 = $this->relative_luminance($second);
        return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
    }

    /**
     * Calculate relative luminance.
     *
     * @param array $rgb RGB tuple.
     * @return float
     */
    private function relative_luminance(array $rgb): float {
        $channels = array_map(static function (int $value): float {
            $channel = $value / 255;
            return $channel <= 0.04045 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
        }, $rgb);
        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    /**
     * Shorten a string for element labels.
     *
     * @param string $value Value.
     * @param int $limit Limit.
     * @return string
     */
    private function shorten(string $value, int $limit): string {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';
        if (mb_strlen($value) <= $limit) {
            return $value;
        }
        return rtrim(mb_substr($value, 0, $limit - 1)) . '…';
    }
}
