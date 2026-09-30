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
 * Course accessibility audit report.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('id', PARAM_INT);
$course = get_course($courseid);
require_login($course);

$context = context_course::instance($course->id);
require_capability('local/accessibilityai:audit', $context);

$url = new moodle_url('/local/accessibilityai/index.php', ['id' => $course->id]);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url($url);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('pluginname', 'local_accessibilityai'));
$PAGE->set_heading(format_string($course->fullname));

$run = optional_param('run', 0, PARAM_BOOL);
$report = null;

if ($run) {
    require_sesskey();
    $auditor = new \local_accessibilityai\audit\course_auditor();
    $report = $auditor->audit($course);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_accessibilityai'));
echo $OUTPUT->notification(get_string('certificationnotice', 'local_accessibilityai'), 'info');

echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => $url->out(false),
    'class' => 'mb-4',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $course->id]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'run', 'value' => 1]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::tag(
    'button',
    get_string('runaudit', 'local_accessibilityai'),
    ['type' => 'submit', 'class' => 'btn btn-primary']
);
echo html_writer::end_tag('form');

if ($report !== null) {
    echo $OUTPUT->render_from_template('local_accessibilityai/report', $report);
}

echo $OUTPUT->footer();
