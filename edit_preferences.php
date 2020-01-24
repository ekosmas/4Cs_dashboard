<?php

require_once('../../config.php'); 
require_once('./forms/edit_preferences_form.php');
require_once("./lib/preferences_lib.php");

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);
 
// // Next look for optional variables.
// $id = optional_param('id', 0, PARAM_INT);
 
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_enhance', $courseid);
}
 
require_login($course);

$PAGE->set_url('/blocks/enhance/edit_preferences.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('edit_preferences', 'block_enhance'));

$edit_preferences_node = $PAGE->settingsnav->add(get_string('enhancesettings', 'block_enhance'));
$edit_preferences_url = new moodle_url('/blocks/enhance/edit_preferences.php', array('courseid' => $courseid, 'blockid' => $blockid));
$editnode = $edit_preferences_node->add(get_string('edit_preferences_nav', 'block_enhance'), $edit_preferences_url);
$editnode->make_active();

init_or_add_course_module_preferences();

$mform = new edit_preferences_form();
 
// Form processing and displaying is done here
if ($mform->is_cancelled()) {
	// Handle form cancel operation, if cancel button is present on form
	$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $mform->get_data()) {
	// In this case you process validated data. $mform->get_data() returns data posted in form.
	
	update_course_module_preferences($fromform);

	$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.

	echo $OUTPUT->header();

	//Set default data (if any)
	$toform['blockid'] = $blockid;
	$toform['courseid'] = $courseid;
	$mform->set_data($toform);

	//displays the form
	$mform->display();

	echo $OUTPUT->footer();

}

?>