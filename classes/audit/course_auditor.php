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
 * Course audit orchestrator.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_accessibilityai\audit;

use local_accessibilityai\ai\reviewer;
use local_accessibilityai\content\collector;
use local_accessibilityai\content\content_item;
use stdClass;

/**
 * Combine local deterministic checks with bounded semantic AI review.
 */
final class course_auditor {
    /**
     * Audit a course.
     *
     * @param stdClass $course Course record.
     * @return array Mustache-ready report data.
     */
    public function audit(stdClass $course): array {
        $items = (new collector())->collect($course);
        $rules = new local_rules();
        $localfindings = [];

        foreach ($items as $item) {
            $localfindings[$item->id] = $rules->audit($item->html);
        }

        $airesult = (new reviewer())->review($items, $localfindings);
        return $this->build_report($items, $localfindings, $airesult['suggestions'], $airesult['errors']);
    }

    /**
     * Build escaped template data. Mustache performs final HTML escaping.
     *
     * @param content_item[] $items Items.
     * @param array $localfindings Local findings.
     * @param array $aisuggestions AI suggestions.
     * @param string[] $aierrors AI errors.
     * @return array
     */
    private function build_report(array $items, array $localfindings, array $aisuggestions, array $aierrors): array {
        $resultitems = [];
        $errorcount = 0;
        $warningcount = 0;
        $aicount = 0;
        $cleanitems = 0;

        foreach ($items as $item) {
            $errors = [];
            $warnings = [];
            foreach ($localfindings[$item->id] ?? [] as $finding) {
                $row = [
                    'ruleid' => $finding->ruleid,
                    'element' => $finding->element,
                    'reason' => $finding->reason,
                    'suggestion' => $finding->suggestion,
                    'location' => $item->location,
                    'editurl' => $item->editurl->out(false),
                ];
                if ($finding->severity === 'error') {
                    $errors[] = $row;
                    $errorcount++;
                } else {
                    $warnings[] = $row;
                    $warningcount++;
                }
            }

            $airows = [];
            foreach ($aisuggestions[$item->id] ?? [] as $suggestion) {
                $airows[] = [
                    'category' => $suggestion['category'],
                    'element' => $suggestion['element'],
                    'reason' => $suggestion['reason'],
                    'suggestion' => $suggestion['suggestion'],
                    'location' => $item->location,
                    'editurl' => $item->editurl->out(false),
                ];
                $aicount++;
            }

            $hasfindings = (bool)($errors || $warnings || $airows);
            if (!$hasfindings) {
                $cleanitems++;
            }
            $resultitems[] = [
                'title' => $item->title,
                'source' => $item->source,
                'location' => $item->location,
                'editurl' => $item->editurl->out(false),
                'errors' => $errors,
                'haserrors' => (bool)$errors,
                'warnings' => $warnings,
                'haswarnings' => (bool)$warnings,
                'aisuggestions' => $airows,
                'hasaisuggestions' => (bool)$airows,
                'hasfindings' => $hasfindings,
                'nofindings' => !$hasfindings,
            ];
        }

        return [
            'itemcount' => count($items),
            'errorcount' => $errorcount,
            'warningcount' => $warningcount,
            'aicount' => $aicount,
            'cleanitems' => $cleanitems,
            'items' => $resultitems,
            'hasitems' => (bool)$items,
            'noitems' => !$items,
            'aierrors' => array_map(static fn(string $message): array => ['message' => $message], $aierrors),
            'hasaierrors' => (bool)$aierrors,
        ];
    }
}
