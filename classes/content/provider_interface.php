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
 * Content provider interface.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\content;

use course_modinfo;
use stdClass;

/**
 * Contract for teacher-authored Moodle content providers.
 */
interface provider_interface {
    /**
     * Collect auditable content from one course.
     *
     * Providers must not return student submissions or user-generated learner content.
     *
     * @param stdClass $course Course record.
     * @param course_modinfo $modinfo Course module information.
     * @return content_item[]
     */
    public function collect(stdClass $course, course_modinfo $modinfo): array;
}
