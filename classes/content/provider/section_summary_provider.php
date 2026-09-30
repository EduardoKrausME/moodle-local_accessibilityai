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
 * Course section summary provider.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\content\provider;

use course_modinfo;
use local_accessibilityai\content\content_item;
use local_accessibilityai\content\provider_interface;
use moodle_url;
use stdClass;

/**
 * Collect course section summaries.
 */
final class section_summary_provider implements provider_interface {
    /** @inheritDoc */
    public function collect(stdClass $course, course_modinfo $modinfo): array {
        $items = [];
        foreach ($modinfo->get_section_info_all() as $section) {
            if (trim(strip_tags((string)$section->summary)) === '') {
                continue;
            }

            $name = get_section_name($course, $section);
            $items[] = new content_item(
                'section:' . $section->id,
                'section',
                $name,
                get_string('source_section', 'local_accessibilityai') . ': ' . $name,
                (string)$section->summary,
                new moodle_url('/course/editsection.php', ['id' => $section->id])
            );
        }
        return $items;
    }
}
