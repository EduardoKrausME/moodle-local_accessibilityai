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
 * Assignment description provider.
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
 * Collect Assignment descriptions only, never submissions.
 */
final class assignment_provider implements provider_interface {

    /**
     * Method collect.
     *
     * @param stdClass $course Parameter course.
     * @param course_modinfo $modinfo Parameter modinfo.
     * @return array Return value.
     */
    public function collect(stdClass $course, course_modinfo $modinfo): array {
        global $DB;
        $items = [];
        foreach ($modinfo->get_instances_of('assign') as $cm) {
            $record = $DB->get_record('assign', ['id' => $cm->instance], 'id,name,intro');
            if (!$record || trim(strip_tags((string)$record->intro)) === '') {
                continue;
            }
            $title = format_string($record->name);
            $items[] = new content_item(
                'assign:' . $record->id,
                'assign',
                $title,
                get_string('source_assignment', 'local_accessibilityai') . ': ' . $title,
                (string)$record->intro,
                new moodle_url('/course/modedit.php', ['update' => $cm->id])
            );
        }
        return $items;
    }
}
