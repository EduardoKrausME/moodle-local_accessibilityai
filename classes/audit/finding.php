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
 * Accessibility finding value object.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\audit;

/**
 * A deterministic accessibility finding.
 */
final class finding {
    /**
     * Constructor.
     *
     * @param string $ruleid Rule identifier.
     * @param string $severity error or warning.
     * @param string $element Safe element description.
     * @param string $reason Finding reason.
     * @param string $suggestion Human-facing remediation guidance.
     */
    public function __construct(
        /** @var string */
        public readonly string $ruleid,

        /** @var string */
        public readonly string $severity,

        /** @var string */
        public readonly string $element,

        /** @var string */
        public readonly string $reason,

        /** @var string */
        public readonly string $suggestion,
    ) {
    }
}
