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
 * English language strings.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Accessibility AI audit';
$string['navigationlink'] = 'Accessibility audit';
$string['accessibilityai:audit'] = 'Run accessibility audits on course content';
$string['runaudit'] = 'Run accessibility audit';
$string['certificationnotice'] = 'This report is an assistance tool, not an accessibility certification and not a complete WCAG audit. Findings and AI suggestions require human review.';
$string['reportsummary'] = 'Audit summary';
$string['itemschecked'] = 'Items checked';
$string['errors'] = 'Errors';
$string['warnings'] = 'Warnings';
$string['aisuggestions'] = 'AI suggestions';
$string['itemswithoutfindings'] = 'Items without detected findings';
$string['editcontent'] = 'Edit content';
$string['element'] = 'Element';
$string['reason'] = 'Reason';
$string['suggestion'] = 'Suggestion';
$string['location'] = 'Location';
$string['rule'] = 'Rule';
$string['category'] = 'Category';
$string['nofindings'] = 'No deterministic findings or AI suggestions were returned for this item. This does not mean that the item is WCAG compliant or fully accessible.';
$string['noitems'] = 'No supported teacher-authored content was found in this course.';
$string['aiwarningtitle'] = 'AI review was incomplete';
$string['bridgeclassmissing'] = 'The required local_ai_bridge API class is unavailable. Local deterministic checks were still completed.';
$string['aibatchfailed'] = 'AI review batch {$a} could not be completed because the AI bridge, tenant, purpose, route or provider was unavailable. Local deterministic checks were still completed.';
$string['privacy:metadata'] = 'local_accessibilityai does not persist user or course content. When a teacher runs an audit, minimized teacher-authored course content may be sent through local_ai_bridge according to that plugin and provider configuration. Student submissions are not analysed by this version.';
$string['source_section'] = 'Course section';
$string['source_page'] = 'Page';
$string['source_bookintro'] = 'Book description';
$string['source_bookchapter'] = 'Book chapter';
$string['source_label'] = 'Text and media area';
$string['source_assignment'] = 'Assignment description';
$string['source_forum'] = 'Forum description';
$string['rule_img_missing_alt_reason'] = 'The image element has no alt attribute, so a text alternative cannot be determined from the HTML.';
$string['rule_img_missing_alt_suggestion'] = 'Add meaningful alt text for informative images, or alt="" when the image is intentionally decorative.';
$string['rule_img_empty_alt_reason'] = 'The image has alt="", but no local HTML signal indicates that it is intentionally decorative.';
$string['rule_img_empty_alt_suggestion'] = 'Verify whether the image is decorative. If it carries information, provide concise alternative text; otherwise keep alt="" and make the decorative intent clear where appropriate.';
$string['rule_heading_skip_reason'] = 'The heading sequence jumps from h{$a->from} to h{$a->to}, which can make the document outline harder to navigate.';
$string['rule_heading_skip_suggestion'] = 'Use heading levels to represent hierarchy and avoid skipping levels solely for visual size.';
$string['rule_heading_empty_reason'] = 'An empty heading creates a meaningless navigation stop for assistive technology.';
$string['rule_heading_empty_suggestion'] = 'Remove the empty heading or provide text that describes the section it introduces.';
$string['rule_heading_presentation_reason'] = 'This heading contains visual styling signals that may indicate the heading element is being used mainly for appearance. This is a heuristic and requires review.';
$string['rule_heading_presentation_suggestion'] = 'Confirm that the element is a real structural heading. If the intent is only visual styling, use a non-heading element and CSS instead.';
$string['rule_link_empty_reason'] = 'The link has no meaningful visible or accessible text that can be detected locally.';
$string['rule_link_empty_suggestion'] = 'Give the link descriptive text or an accessible label that communicates its destination or action.';
$string['rule_link_generic_reason'] = 'Generic link text such as “click here” or “more” depends on surrounding context and is less useful when links are listed independently.';
$string['rule_link_generic_suggestion'] = 'Use link text that describes the destination or action without requiring the surrounding sentence.';
$string['rule_table_headers_reason'] = 'The table has no header cells. If it contains tabular data, row/column relationships may be unclear to assistive technology.';
$string['rule_table_headers_suggestion'] = 'For data tables, add appropriate th cells and relationships. If the table is only for layout, avoid table layout when possible or explicitly mark it as presentational.';
$string['rule_table_relationship_reason'] = 'The table contains header cells but no explicit scope or id relationship was detected for at least one header.';
$string['rule_table_relationship_suggestion'] = 'Use scope="col" or scope="row" for simple tables, or id/headers relationships when the structure is more complex.';
$string['rule_obsolete_html_reason'] = 'The element {$a} is obsolete or problematic for semantic, maintainable markup.';
$string['rule_obsolete_html_suggestion'] = 'Replace presentational HTML with semantic elements and CSS.';
$string['rule_positive_tabindex_reason'] = 'A positive tabindex forces a custom keyboard order that can diverge from the visual and document order.';
$string['rule_positive_tabindex_suggestion'] = 'Prefer natural DOM order and tabindex="0" or tabindex="-1" only when their specific keyboard behaviours are required.';
$string['rule_duplicate_id_reason'] = 'The same HTML id appears more than once in this content fragment, which can break label and ARIA relationships.';
$string['rule_duplicate_id_suggestion'] = 'Make every id unique within the rendered page and update any references to it.';
$string['rule_video_caption_reason'] = 'A video was detected, but no captions/subtitles track or nearby caption/transcript indicator was detectable in the stored HTML.';
$string['rule_video_caption_suggestion'] = 'Verify that the video provides accurate captions and, when appropriate, a transcript. Embedded player capabilities may exist even when they are not visible in stored HTML.';
$string['rule_audio_transcript_reason'] = 'Audio was detected, but no nearby transcript indicator was detectable in the stored content.';
$string['rule_audio_transcript_suggestion'] = 'Provide or link to an accurate transcript and verify that it covers the meaningful audio content.';
$string['rule_embedded_media_reason'] = 'Embedded video media was detected, but no nearby caption/transcript indicator was detectable in the stored content.';
$string['rule_embedded_media_suggestion'] = 'Verify captions in the external player and provide a transcript when appropriate. This check cannot inspect the remote player itself.';
$string['rule_iframe_title_reason'] = 'The iframe has no non-empty title attribute, so its purpose may not be announced clearly.';
$string['rule_iframe_title_suggestion'] = 'Add a short title that describes the embedded content or purpose.';
$string['rule_contrast_reason'] = 'The explicit inline foreground/background colors produce an approximate contrast ratio of {$a}:1. This local calculation does not account for inherited CSS, theme rules, images, transparency, or final rendering.';
$string['rule_contrast_suggestion'] = 'Review the final rendered colors and adjust them if necessary. Use a browser-based accessibility tool for authoritative rendered-style contrast checks.';
