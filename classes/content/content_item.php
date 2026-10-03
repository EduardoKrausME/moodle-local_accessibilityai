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
 * Content item value object.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\content;

use moodle_url;

/**
 * A single teacher-authored content fragment that can be audited.
 */
final class content_item {
    /**
     * Constructor.
     *
     * @param string $id Stable local identifier used only during this request.
     * @param string $source Source type.
     * @param string $title Human-readable title.
     * @param string $location Human-readable location.
     * @param string $html Stored HTML fragment.
     * @param moodle_url $editurl Local edit URL controlled by PHP.
     */
    public function __construct(
        /** @var string Stable local identifier used only during this request. */
        public readonly string $id,

        /** @var string Source type. */
        public readonly string $source,

        /** @var string Human-readable title. */
        public readonly string $title,

        /** @var string Human-readable location. */
        public readonly string $location,

        /** @var string Stored HTML fragment. */
        public readonly string $html,

        /** @var moodle_url Local edit URL controlled by PHP. */
        public readonly moodle_url $editurl,
    ) {
    }
}
