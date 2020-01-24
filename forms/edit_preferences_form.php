<?php

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
class edit_preferences_form extends moodleform {
    
    // Add elements to form
    public function definition() {
        global $CFG, $DB, $COURSE, $PAGE;
 
        $format = course_get_format($PAGE->course);
        $course = $format->get_course();
        $sections = $format->get_sections();
        $activities = get_array_of_activities($course->id);
        $context = context_course::instance($course->id);

        $mform = $this->_form; 
        $mform->addElement('html', 
            '<div style = "table-layout:fixed">'.
                '<table>'.
                    '<tr style = "text-align: center">'.
                        '<th> Section </th>'.
                        '<th> Activity-Resource </th>'.
                        '<th> Component </th>'.
                        '<th> Name </th>'.
                        '<th> Type </th>'.
                        '<th> Preference </th>'.
                        '<th> Points </th>'.
                    '</tr>');

        $cur_section_id = -1;
        foreach ($activities as $activity) {

            if ($cur_section_id != $activity->section) {
                $cur_section_id = $activity->section;

                $title = null;
                if (!empty($sections[$cur_section_id]->name)) {
                    // If the section has explicit title defined, it is used.
                    $title = format_string($sections[$cur_section_id]->name, true, array('context' => $context));
                }
                else {
                    $title = $format->get_section_name($sections[$cur_section_id]);
                }

                $mform->addElement('html',
                    '<tr style = "text-align: center; border-top: 2px solid #ddd">'.
                        '<td>'.$title.'</td>');
            }
            else {
                $mform->addElement('html',
                    '<tr style = "text-align: center">'.
                        '<td> </td>');
            }

            $mform->addElement('html',
                        '<td>'.$activity->name.'</td>'.
                        '<td>'.$activity->mod.'</td>');

            // find events we track for this module
            $events = $DB->get_records_sql(
                'SELECT *
                 FROM block_enhance_events
                 WHERE component = :component_val',
                 array( 'component_val' => $activity->mod)
            );
            $bool_first_event = true;
            foreach ($events as $event) {
                if ($bool_first_event) {
                    $bool_first_event = false;
                }
                else {
                    $mform->addElement('html',
                    '<tr style = "text-align: center">'.
                        '<td> </td>'.
                        '<td> </td>'.
                        '<td> </td>');
                }

                $pref = $DB->get_record_sql(
                    'SELECT type, type_4cs_map, points
                     FROM block_enhance_preferences
                     WHERE 
                        course_id = :courseid AND
                        module_id = :moduleid AND
                        event_id = :eventid',
                     array( 
                        'courseid' => $course->id,
                        'moduleid' => $activity->cm,
                        'eventid' => $event->id)
                );

                $mform->addElement('html',
                        '<td>'. $event->event_name .'</td>'.
                        '<td style = "width: 20%">');

                $elem_name = 'type_4cs_map_'.$course->id.'_'.$activity->cm.'_'.$event->id.'_pref';
                $mform->addElement('select', $elem_name, '', ['1' => 'CONNECT', '2' => 'CONSUME', '3' => 'CONTRIBUTE', '4' => 'CREATE']);
                $mform->setType($elem_name, PARAM_RAW);                     // Set type of element
                if ($pref->type_4cs_map == 'CONSUME')
                    $mform->getElement($elem_name)->setSelected('2');  
                else if ($pref->type_4cs_map == 'CONTRIBUTE')
                    $mform->getElement($elem_name)->setSelected('3'); 
                else if ($pref->type_4cs_map == 'CREATE')
                    $mform->getElement($elem_name)->setSelected('4');

                $mform->addElement('html',
                        '</td>'.
                        '<td  style = "width: 20%">');

                $elem_name = 'type_'.$course->id.'_'.$activity->cm.'_'.$event->id.'_pref';
                $mform->addElement('select', $elem_name, '', ['1' => 'MANDATORY', '2' => 'OPTIONAL']);
                $mform->setType($elem_name, PARAM_RAW);                     // Set type of element
                if ($pref->type == 'OPTIONAL')
                    $mform->getElement($elem_name)->setSelected('2');            

                $mform->addElement('html',
                        '</td>'.
                        '<td>');

                $elem_name = 'points_'.$course->id.'_'.$activity->cm.'_'.$event->id.'_pref';
                $mform->addElement('text', $elem_name, '', array('size'=>1));
                $mform->setType($elem_name, PARAM_INT);                     // Set type of element
                $mform->setDefault($elem_name, $pref->points);

                 $mform->addElement('html',
                        '</td>'.
                    '</tr>');
            }
        }

        $mform->addElement('html',
                    '<tr style = "text-align: center">'.
                        '<td colspan="7">');

        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
        $this->add_action_buttons();

        $mform->addElement('html',
                        '</td>'.
                    '</tr>'.
                '</table>'.
            '</div>');
    }

    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}