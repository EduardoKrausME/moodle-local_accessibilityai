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
 * Tests for deterministic HTML rules.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai;

use advanced_testcase;
use local_accessibilityai\audit\local_rules;

/**
 * Deterministic rule tests using HTML fixtures.
 *
 * @covers \local_accessibilityai\audit\local_rules
 */
final class local_rules_test extends advanced_testcase {
    /**
     * Verify each fixture raises the expected rule.
     *
     * @dataProvider fixture_provider
     * @param string $fixture Fixture filename.
     * @param string $ruleid Expected rule ID.
     * @return void
     */
    public function test_fixture_detects_rule(string $fixture, string $ruleid): void {
        $html = file_get_contents(__DIR__ . '/fixtures/' . $fixture);
        $this->assertIsString($html);
        $findings = (new local_rules())->audit($html);
        $ids = array_map(static fn($finding): string => $finding->ruleid, $findings);
        $this->assertContains($ruleid, $ids, 'Fixture did not produce expected rule: ' . $ruleid);
    }

    /**
     * Fixture data provider.
     *
     * @return array
     */
    public static function fixture_provider(): array {
        return [
            'missing alt' => ['img-missing-alt.html', 'img_missing_alt'],
            'suspicious empty alt' => ['img-empty-alt.html', 'img_empty_alt_suspect'],
            'heading skip' => ['heading-skip.html', 'heading_level_skip'],
            'empty heading' => ['heading-empty.html', 'heading_empty'],
            'presentation heading' => ['heading-presentation.html', 'heading_presentation_suspect'],
            'empty link' => ['link-empty.html', 'link_empty_text'],
            'generic link' => ['link-click-here.html', 'link_generic_text'],
            'table headers' => ['table-no-headers.html', 'table_missing_headers'],
            'table relationship' => ['table-no-scope.html', 'table_header_relationship'],
            'obsolete html' => ['obsolete-html.html', 'obsolete_html_element'],
            'positive tabindex' => ['positive-tabindex.html', 'positive_tabindex'],
            'duplicate id' => ['duplicate-id.html', 'duplicate_id'],
            'video captions' => ['video-no-caption.html', 'video_caption_not_detected'],
            'audio transcript' => ['audio-no-transcript.html', 'audio_transcript_not_detected'],
            'embedded media transcript' => ['iframe-video-no-transcript.html', 'embedded_media_transcript_not_detected'],
            'iframe title' => ['iframe-no-title.html', 'iframe_missing_title'],
            'inline contrast' => ['inline-low-contrast.html', 'inline_contrast_low'],
        ];
    }

    /**
     * Decorative images should not be warned merely because alt is empty.
     *
     * @return void
     */
    public function test_explicit_decorative_image_allows_empty_alt(): void {
        $findings = (new local_rules())->audit('<img src="divider.png" alt="" role="presentation">');
        $ids = array_map(static fn($finding): string => $finding->ruleid, $findings);
        $this->assertNotContains('img_empty_alt_suspect', $ids);
    }

    /**
     * Presentational tables should not be treated as data tables.
     *
     * @return void
     */
    public function test_presentational_table_is_not_checked_for_headers(): void {
        $findings = (new local_rules())->audit('<table role="presentation"><tr><td>Layout</td></tr></table>');
        $ids = array_map(static fn($finding): string => $finding->ruleid, $findings);
        $this->assertNotContains('table_missing_headers', $ids);
    }
}
