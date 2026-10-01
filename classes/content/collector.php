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
 * Course content collector.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\content;

use stdClass;

/**
 * Collect all supported teacher-authored content from a course.
 */
final class collector {
    /**
     * Collect supported items.
     *
     * @param stdClass $course Course record.
     * @return content_item[]
     */
    public function collect(stdClass $course): array {
        $modinfo = get_fast_modinfo($course);
        $items = [];

        foreach (provider_registry::classes() as $classname) {
            /** @var provider_interface $provider */
            $provider = new $classname();
            foreach ($provider->collect($course, $modinfo) as $item) {
                $items[] = $item;
            }
        }

        usort($items, static function (content_item $a, content_item $b): int {
            return [$a->source, $a->location, $a->id] <=> [$b->source, $b->location, $b->id];
        });

        return $items;
    }
}
