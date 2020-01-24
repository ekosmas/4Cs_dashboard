<?php
 
require_once('../../config.php'); 
require_once("./lib/preferences_lib.php");


global $DB, $OUTPUT, $PAGE, $USER;
 
// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
 
// Next look for optional variables.
$id = optional_param('id', 0, PARAM_INT);
 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_enhance', $courseid);
}
 
require_login($course);

$PAGE->set_url('/blocks/enhance/nurse_sally.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('nurse_sally', 'block_enhance'));

$settingsnode = $PAGE->settingsnav->add(get_string('enhancesettings', 'block_enhance'));
$sectionsurl = new moodle_url('/blocks/enhance/nurse_sally.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('nurse_sally_nav', 'block_enhance'), $sectionsurl);
$editnode->make_active();

echo $OUTPUT->header();

init_or_add_course_module_preferences();

$userid = $USER->id;
$context = context_course::instance($courseid);
if (!has_capability('block/enhance:editpreferences', $context, NULL, false) &&
    !has_capability('block/enhance:earnpoints', $context, NULL, false)) {
    $DB->insert_record('block_enhance', array ('message' => 'user: '.$userid.' has no capability to view nurse sally'));
   	echo '<h2>You do not have the capability to view Nurse Sully\'s reports! If you are either a teacher or a student, contact your system administrator; otherwise, have a nice day! :) </h2>';
}
else {

	$sql = '
		SELECT SUM(B.points) AS pts
   		FROM block_enhance_points AS A
   		INNER JOIN (
   			SELECT *
   			FROM block_enhance_preferences
   			WHERE type_4cs_map = :type
   		) as B 
   		ON A.event_id = B.event_id AND A.module_id = B.module_id
   		WHERE A.course_id = :courseid';

    $data = array(
    	'courseid' => $courseid, 
    	'type');

    // AGGREGATE CHART
    // ---------------
    $data['type'] = 'CONNECT';
	if (!isset(($connect_pts = $DB->get_record_sql($sql, $data, $strictness=IGNORE_MISSING))->pts)) {
		$connect_pts = 0;
	} else {
		$connect_pts = $connect_pts->pts;
	}

    $data['type'] = 'CONSUME';
	if (!isset(($consume_pts = $DB->get_record_sql($sql, $data, $strictness=IGNORE_MISSING))->pts)) {
		$consume_pts = 0;
	} else {
		$consume_pts = $consume_pts->pts;
	}

    $data['type'] = 'CONTRIBUTE';
	if (!isset(($contribute_pts = $DB->get_record_sql($sql, $data, $strictness=IGNORE_MISSING))->pts)) {
		$contribute_pts = 0;
	} else {
		$contribute_pts = $contribute_pts->pts;
	}

    $data['type'] = 'CREATE';
	if (!isset(($create_pts = $DB->get_record_sql($sql, $data, $strictness=IGNORE_MISSING))->pts)) {
		$create_pts = 0;
	} else {
		$create_pts = $create_pts->pts;
	}

	$serie1 = new core\chart_series('Points', [$connect_pts, $consume_pts, $contribute_pts, $create_pts]);
	$chart_pts = new \core\chart_pie();
	$chart_pts->add_series($serie1);
	$chart_pts->set_labels(['Connect', 'Consume', 'Contribute', 'Create']);

	$bool_chart_empty = false;
	if (!$connect_pts && !$consume_pts && !$contribute_pts && !$create_pts) {
		$bool_chart_empty = true;
	}

	if ($bool_chart_empty) {
		echo 
			'<div style="width: 40%; height: 40%">'.
				'There are no events for activities or resources, for this course'.
				'<hr>'.
			'</div>';
	}
	else {
		echo 
			'<div style="width: 40%; height: 40%">'.
				$OUTPUT->render($chart_pts).
				'<hr>';

		$url_my_report_connect = new moodle_url('/blocks/enhance/nurse_sally_report_connect.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_connect, get_string('show_nurse_sally_report_connect', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_consume = new moodle_url('/blocks/enhance/nurse_sally_report_consume.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_consume, get_string('show_nurse_sally_report_consume', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_contribute = new moodle_url('/blocks/enhance/nurse_sally_report_contribute.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_contribute, get_string('show_nurse_sally_report_contribute', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_create = new moodle_url('/blocks/enhance/nurse_sally_report_create.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_create, get_string('show_nurse_sally_report_create', 'block_enhance'));
		echo 	'<br>'.
			'</div>';
	}

	
}



echo $OUTPUT->footer();

?>