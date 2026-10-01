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
 * Content provider registry.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\content;

use local_accessibilityai\content\provider\assignment_provider;
use local_accessibilityai\content\provider\book_provider;
use local_accessibilityai\content\provider\forum_provider;
use local_accessibilityai\content\provider\label_provider;
use local_accessibilityai\content\provider\page_provider;
use local_accessibilityai\content\provider\section_summary_provider;

/**
 * Registry for supported Moodle content sources.
 */
final class provider_registry {
    /**
     * Return provider class names.
     *
     * New Moodle content types are added by implementing provider_interface and registering the class here.
     * This keeps extraction independent from deterministic rules and AI analysis.
     *
     * @return string[]
     */
    public static function classes(): array {
        return [
            section_summary_provider::class,
            page_provider::class,
            book_provider::class,
            label_provider::class,
            assignment_provider::class,
            forum_provider::class,
        ];
    }
}
