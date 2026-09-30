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
 * Parse and sanitize structured AI responses.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\ai;

use UnexpectedValueException;

/**
 * Strict parser for AI JSON suggestions.
 */
final class response_parser {
    /**
     * Parse AI JSON.
     *
     * @param string $response Raw model response.
     * @param string[] $alloweditemids Item IDs contained in the request batch.
     * @return array[] Sanitized suggestions.
     */
    public function parse(string $response, array $alloweditemids): array {
        $json = $this->extract_json($response);
        $decoded = json_decode($json, true);
        if (!is_array($decoded) || !isset($decoded['suggestions']) || !is_array($decoded['suggestions'])) {
            throw new UnexpectedValueException('AI response does not contain a valid suggestions array.');
        }

        $allowed = array_fill_keys($alloweditemids, true);
        $categories = [
            'alt_text', 'link_context', 'heading_clarity', 'plain_language',
            'alternative_description', 'text_simplification', 'other',
        ];
        $result = [];

        foreach (array_slice($decoded['suggestions'], 0, 100) as $suggestion) {
            if (!is_array($suggestion)) {
                continue;
            }
            $itemid = isset($suggestion['itemid']) ? (string)$suggestion['itemid'] : '';
            if ($itemid === '' || !isset($allowed[$itemid])) {
                continue;
            }
            $category = isset($suggestion['category']) ? (string)$suggestion['category'] : 'other';
            if (!in_array($category, $categories, true)) {
                $category = 'other';
            }
            $reason = $this->sanitize((string)($suggestion['reason'] ?? ''), 1200);
            $advice = $this->sanitize((string)($suggestion['suggestion'] ?? ''), 1600);
            if ($reason === '' || $advice === '') {
                continue;
            }
            $result[] = [
                'itemid' => $itemid,
                'category' => $category,
                'element' => $this->sanitize((string)($suggestion['element'] ?? ''), 300),
                'reason' => $reason,
                'suggestion' => $advice,
            ];
        }

        return $result;
    }

    /**
     * Extract JSON from a plain or fenced model response.
     *
     * @param string $response Response.
     * @return string
     */
    private function extract_json(string $response): string {
        $response = trim($response);
        $response = preg_replace('/^```(?:json)?\s*/i', '', $response) ?? $response;
        $response = preg_replace('/\s*```$/', '', $response) ?? $response;
        $first = strpos($response, '{');
        $last = strrpos($response, '}');
        if ($first === false || $last === false || $last < $first) {
            throw new UnexpectedValueException('AI response does not contain JSON.');
        }
        return substr($response, $first, $last - $first + 1);
    }

    /**
     * Strip markup and control characters from AI-provided text.
     *
     * @param string $value Value.
     * @param int $max Max length.
     * @return string
     */
    private function sanitize(string $value, int $max): string {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';
        $value = clean_param($value, PARAM_TEXT);
        return mb_strlen($value) <= $max ? $value : mb_substr($value, 0, $max - 1) . '…';
    }
}
