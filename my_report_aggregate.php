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

$PAGE->set_url('/blocks/enhance/my_report_aggregate.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('my_report_aggregate', 'block_enhance'));

$settingsnode = $PAGE->settingsnav->add(get_string('enhancesettings', 'block_enhance'));
$sectionsurl = new moodle_url('/blocks/enhance/my_report_aggregate.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('my_report_aggregate_nav', 'block_enhance'), $sectionsurl);
$editnode->make_active();

echo $OUTPUT->header();

init_or_add_course_module_preferences();


$userid = $USER->id;
$context = context_course::instance($courseid);
if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
    $DB->insert_record('block_enhance', array ('message' => 'user: '.$userid.' has no capability to earn/view points'));
   	echo '<h2>You do not have the capability to earn/view points! If you are a student contact your system administrator; otherwise, have a nice day! :) </h2>';
}
else {

	// POINTS CHART
	// ------------

   	$sql = '
		SELECT SUM(B.points) AS pts
   		FROM block_enhance_points AS A
   		INNER JOIN (
   			SELECT *
   			FROM block_enhance_preferences
   			WHERE 
	   	   		course_id = :courseid1 AND
	   			type_4cs_map = :type AND
   				type = :tval
   		) as B 
   		ON A.event_id = B.event_id AND A.module_id = B.module_id
   		WHERE 
   			A.user_id = :userid AND
   			A.course_id = :courseid2';

    $data = array(
    	'courseid1' => $courseid, 
    	'tval', 
    	'userid' => $userid,  
    	'courseid2' => $courseid, 
    	'type');

    // MANDATORY AGGREGATE CHART
    // -------------------------
    $data['tval'] = 'MANDATORY';

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
	$mandatory_chart_pts = new \core\chart_pie();
	$mandatory_chart_pts->add_series($serie1);
	$mandatory_chart_pts->set_labels(['Connect', 'Consume', 'Contribute', 'Create']);

	$bool_mandatory_chart_empty = false;
	if (!$connect_pts && !$consume_pts && !$contribute_pts && !$create_pts) {
		$bool_mandatory_chart_empty = true;
	}


    // OPTIONAL AGGREGATE CHART
    // ------------------------
    $data['tval'] = 'OPTIONAL';

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
	$optional_chart_pts = new \core\chart_pie();
	$optional_chart_pts->add_series($serie1);
	$optional_chart_pts->set_labels(['Connect', 'Consume', 'Contribute', 'Create']);

	$bool_optional_chart_empty = false;
	if (!$connect_pts && !$consume_pts && !$contribute_pts && !$create_pts) {
		$bool_optional_chart_empty = true;
	}


	if ($bool_mandatory_chart_empty) {
		echo 
			'<div style="width: 40%; height: 40%">'.
				'<h2 style = "text-align: center">Mandatory</h2>
				 <p style = "text-align: center">Aggregate</p>'.
				'<p style = "text-align: center">You have no mandatory events for activities or resources.</p>'.
				'<hr>';
	}
	else {
		echo 
			'<div style="width: 40%; height: 40%">'.
				'<h2 style = "text-align: center">Mandatory</h2>
				 <p style = "text-align: center">Aggregate</p>'.
				$OUTPUT->render($mandatory_chart_pts).
				'<hr>';
	}

	if ($bool_optional_chart_empty) {
		echo 
				'<h2 style = "text-align: center">Optional</h2>
				 <p style = "text-align: center">Aggregate</p>'.
				 '<p style = "text-align: center">You have no optional events for activities or resources.</p>'.
				 '<hr>';
	}
	else {
		echo 
				'<h2 style = "text-align: center">Optional</h2>
				 <p style = "text-align: center">Aggregate</p>'.
				$OUTPUT->render($optional_chart_pts).
				'<hr>';
	}		

	if (!$bool_mandatory_chart_empty || !$bool_optional_chart_empty) {
		
		$url_my_report_connect = new moodle_url('/blocks/enhance/my_report_connect.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_connect, get_string('show_my_report_connect', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_consume = new moodle_url('/blocks/enhance/my_report_consume.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_consume, get_string('show_my_report_consume', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_contribute = new moodle_url('/blocks/enhance/my_report_contribute.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_contribute, get_string('show_my_report_contribute', 'block_enhance'));
		echo 	'<br>';

		$url_my_report_create = new moodle_url('/blocks/enhance/my_report_create.php', array('blockid' => $blockid, 'courseid' => $courseid));
		echo html_writer::link($url_my_report_create, get_string('show_my_report_create', 'block_enhance'));
		echo 	'<br>'.
			'</div>';
	}
	else {
		echo 
			'</div>';
	}
}



echo $OUTPUT->footer();

?>