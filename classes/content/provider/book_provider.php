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
 * Book content provider.
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
 * Collect Book introductions and chapters.
 */
final class book_provider implements provider_interface {

    public function collect(stdClass $course, course_modinfo $modinfo): array {
        global $DB;
        $items = [];
        foreach ($modinfo->get_instances_of('book') as $cm) {
            $book = $DB->get_record('book', ['id' => $cm->instance], 'id,name,intro');
            if (!$book) {
                continue;
            }

            $bookname = format_string($book->name);
            if (trim(strip_tags((string)$book->intro)) !== '') {
                $items[] = new content_item(
                    'bookintro:' . $book->id,
                    'book',
                    $bookname,
                    get_string('source_bookintro', 'local_accessibilityai') . ': ' . $bookname,
                    (string)$book->intro,
                    new moodle_url('/course/modedit.php', ['update' => $cm->id])
                );
            }

            $chapters = $DB->get_records('book_chapters', ['bookid' => $book->id], 'pagenum ASC');
            foreach ($chapters as $chapter) {
                if (trim(strip_tags((string)$chapter->content)) === '') {
                    continue;
                }
                $chaptertitle = format_string($chapter->title);
                $items[] = new content_item(
                    'bookchapter:' . $chapter->id,
                    'book',
                    $chaptertitle,
                    get_string('source_bookchapter', 'local_accessibilityai') . ': ' . $bookname . ' / ' . $chaptertitle,
                    (string)$chapter->content,
                    new moodle_url('/mod/book/edit.php', ['cmid' => $cm->id, 'id' => $chapter->id])
                );
            }
        }
        return $items;
    }
}
