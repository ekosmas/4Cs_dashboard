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

$PAGE->set_url('/blocks/enhance/nurse_sally_report_connect.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('nurse_sally_report_connect', 'block_enhance'));

$settingsnode = $PAGE->settingsnav->add(get_string('enhancesettings', 'block_enhance'));
$sectionsurl = new moodle_url('/blocks/enhance/nurse_sally_report_connect.php', array('id' => $id, 'courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('nurse_sally_report_connect_nav', 'block_enhance'), $sectionsurl);
$editnode->make_active();

echo $OUTPUT->header();

init_or_add_course_module_preferences();

$userid = $USER->id;
$context = context_course::instance($courseid);
if (!has_capability('block/enhance:editpreferences', $context, NULL, false) &&
    !has_capability('block/enhance:earnpoints', $context, NULL, false)) {
    $DB->insert_record('block_enhance', array ('message' => 'user: '.$userid.' has no capability to view nurse sally'));
   	echo '<h2>You do not have the capability to view Nurse Sully\'s reports! If you are either a teacher or a student, contact your system administrator; otherwise, have a nice day! :) </h2>';
}else {

	// CONNECT POINTS CHART
	// --------------------

   	$sql = '
		SELECT SUM(B.points) AS pts
   		FROM block_enhance_points AS A
   		INNER JOIN (
   			SELECT *
   			FROM block_enhance_preferences
   			WHERE 
	   			event_id IN (
	   				SELECT id
	   				FROM block_enhance_events
	   				WHERE
	   					event_name = :event AND
	   					component = :component_val
	   			) AND 
	   			course_id = :courseid1 AND
	   			type_4cs_map = :type
   		) as B 
   		ON A.event_id = B.event_id AND A.module_id = B.module_id
   		WHERE A.course_id = :courseid2';

    $data = array(
    	'courseid1' => $courseid, 
    	'courseid2' => $courseid,
    	'event',
    	'component_val',
    	'type' => 'CONNECT');


    $events = $DB->get_records_sql( '
		SELECT *
		FROM block_enhance_events AS A
		INNER JOIN (
			SELECT event_id
			FROM block_enhance_preferences
			WHERE type_4cs_map = :type
		) AS B
		ON A.id = B.event_id',
		array( 'type' => 'CONNECT')
	);


    // AGGREGATE CHART
    // ---------------
    $labels = array();
    $values = array();
    $bool_chart_empty = true;
	foreach ($events as $event) {
		$data['event'] = $event->event_name;
		$data['component_val'] = $event->component;
		if (!isset(($sum_pts = $DB->get_record_sql($sql, $data, $strictness=IGNORE_MISSING))->pts)) {
			$sum_pts = 0;
		} else {
			$sum_pts = $sum_pts->pts;
			$bool_chart_empty = false;
		}

		array_push($labels, $event->component.', '.$event->event_name);
		array_push($values, $sum_pts);
	}

	$serie1 = new core\chart_series('Points', $values);
	$chart_pts = new \core\chart_pie();
	$chart_pts->add_series($serie1);
	$chart_pts->set_labels($labels);

	if ($bool_chart_empty) {
		echo 
			'<div style="width: 40%; height: 40%">'.
				'<p style = "text-align: center">Connect</p>'.
				'<p style = "text-align: center">There are no events for activities or resources of type connect, for this course.</p>'.
			'</div>';
	}
	else {
		echo 
			'<div style="width: 40%; height: 40%">'.
				'<p style = "text-align: center">Connect</p>'.
				$OUTPUT->render($chart_pts).
			'</div>';
	}
}

echo $OUTPUT->footer();

?>