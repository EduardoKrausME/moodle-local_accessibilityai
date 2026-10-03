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
 * Tests for AI response parsing and sanitization.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai;

use advanced_testcase;
use local_accessibilityai\ai\response_parser;
use UnexpectedValueException;

/**
 * Class ai_response_parser_test
 *
 * @covers \local_accessibilityai\ai\response_parser
 */
final class ai_response_parser_test extends advanced_testcase {
    /**
     * Valid JSON is parsed and unknown item IDs are dropped.
     *
     * @return void
     */
    public function test_valid_response_is_parsed(): void {
        $response = json_encode([
            'suggestions' => [
                [
                    'itemid' => 'page:10',
                    'category' => 'plain_language',
                    'element' => 'text',
                    'reason' => 'Long sentence.',
                    'suggestion' => 'Split it into two sentences.',
                ],
                [
                    'itemid' => 'page:999',
                    'category' => 'other',
                    'reason' => 'Should be ignored.',
                    'suggestion' => 'Ignored.',
                ],
            ],
        ]);
        $result = (new response_parser())->parse($response, ['page:10']);
        $this->assertCount(1, $result);
        $this->assertSame('page:10', $result[0]['itemid']);
    }

    /**
     * Markdown fences are tolerated but HTML and scripts are removed from fields.
     *
     * @return void
     */
    public function test_response_is_sanitized(): void { // phpcs:disable moodle.Strings.ForbiddenStrings.Found
        $response = '```json\n' . json_encode([
                'suggestions' => [[
                    'itemid' => 'page:10',
                    'category' => 'other',
                    'element' => '<b>link:1</b>',
                    'reason' => '<script>alert(1)</script><strong>Reason</strong>',
                    'suggestion' => '<img src=x onerror=alert(1)>Use clearer text.',
                ]],
            ]) . '\n```';
        $result = (new response_parser())->parse($response, ['page:10']);
        $this->assertSame('link:1', $result[0]['element']);
        $this->assertStringNotContainsString('<', $result[0]['reason']);
        $this->assertStringNotContainsString('<', $result[0]['suggestion']);
        $this->assertStringNotContainsString('script', mb_strtolower($result[0]['reason']));
    }

    /**
     * Malformed responses fail closed.
     *
     * @return void
     */
    public function test_invalid_response_throws_exception(): void {
        $this->expectException(UnexpectedValueException::class);
        (new response_parser())->parse('not json', ['page:10']);
    }
}
