<?php

function init_or_add_course_module_preferences () {

	global $PAGE, $DB;

	$format = course_get_format($PAGE->course);
    $course = $format->get_course();
    $sections = $format->get_sections();
    $activities = get_array_of_activities($course->id);

    foreach ($activities as $activity) {
        $bool = $DB->record_exists('block_enhance_preferences', array('course_id' => $course->id, 'module_id' => $activity->cm));
        if (!$bool) {
        	 // find events we track for this module
            $events = $DB->get_records_sql(
                'SELECT *
                 FROM block_enhance_events
                 WHERE component = :component_val',
                 array( 'component_val' => $activity->mod)
            );

            foreach ($events as $event) {
	        	$DB->insert_record('block_enhance_preferences', array (	
        														'course_id' => $course->id,
                                                      			'module_id' => $activity->cm,
                                                        		'event_id' => $event->id,
                                                        		'type' => 'MANDATORY',
                                                        		'type_4cs_map' => $event->type_4cs_map,
                                                        		'points' => $event->points
                                                    ) );
	        }
        }
    }
}


function update_course_module_preferences ($fromform) {

    global $PAGE, $DB;

    $format = course_get_format($PAGE->course);
    $course = $format->get_course();
    $sections = $format->get_sections();
    $activities = get_array_of_activities($course->id);

    foreach ($activities as $activity) {        
         // find events we track for this module
        $events = $DB->get_records_sql(
            'SELECT id
             FROM block_enhance_events
             WHERE component = :component_val',
             array( 'component_val' => $activity->mod)
        );

        foreach ($events as $event) {
            $event_id = $event->id;

            $pref_values = $DB->get_record_sql(
                'SELECT type, type_4cs_map, points
                 FROM block_enhance_preferences
                 WHERE 
                    course_id = :courseid AND
                    module_id = :moduleid AND
                    event_id = :eventid',
                 array( 
                    'courseid' => $course->id, 
                    'moduleid' => $activity->cm, 
                    'eventid' => $event_id)
             );

            $update_array = array();

            $elem_type_4cs_map = 'type_4cs_map_'.$course->id.'_'.$activity->cm.'_'.$event_id.'_pref';
            if ($pref_values->type_4cs_map == 'CONNECT') {
                $elem_cur_type_4cs_map_value = '1';
            }
            else if ($pref_values->type_4cs_map == 'CONSUME') {
                $elem_cur_type_4cs_map_value = '2';
            }
            else if ($pref_values->type_4cs_map == 'CONTRIBUTE') {
                $elem_cur_type_4cs_map_value = '3';
            }
            else {
                $elem_cur_type_4cs_map_value = '4';
            }
            if ($fromform->$elem_type_4cs_map != $elem_cur_type_4cs_map_value) {
                $elem_new_type_4cs_map_value = 'CONNECT';
                if ($fromform->$elem_type_4cs_map == '2')
                    $elem_new_type_4cs_map_value = 'CONSUME';
                else if ($fromform->$elem_type_4cs_map == '3')
                    $elem_new_type_4cs_map_value = 'CONTRIBUTE';
                if ($fromform->$elem_type_4cs_map == '4')
                    $elem_new_type_4cs_map_value = 'CREATE';

                $update_array['type_4cs_map'] = $elem_new_type_4cs_map_value;
            }
            
            $elem_type = 'type_'.$course->id.'_'.$activity->cm.'_'.$event_id.'_pref';
            if ($pref_values->type == 'MANDATORY') {
                $elem_cur_type_value = '1';
            }
            else {
                $elem_cur_type_value = '2';
            }
            if ($fromform->$elem_type != $elem_cur_type_value) {
                $elem_new_type_value = 'MANDATORY';
                if ($fromform->$elem_type == '2')
                    $elem_new_type_value = 'OPTIONAL';

                $update_array['type'] = $elem_new_type_value;
            }

            $elem_points = 'points_'.$course->id.'_'.$activity->cm.'_'.$event_id.'_pref';
            if ($fromform->$elem_points != $pref_values->points) {
                $update_array['points'] = $fromform->$elem_points;
            }

            if (!empty($update_array)) {
                // update preference
                $elem_id = $DB->get_field('block_enhance_preferences', 'id', array('course_id' => $course->id, 'module_id' => $activity->cm, 'event_id' => $event_id));
                $update_array['id'] = $elem_id;
                $DB->update_record('block_enhance_preferences', $update_array);
            }
        }
    }
}

?>