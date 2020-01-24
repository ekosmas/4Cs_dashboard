<?php

defined('MOODLE_INTERNAL') || die();

class block_enhance_observer {
    /**
     * Event processor - Discussion created
     *
     * @param \mod_forum\event\discussion_created $event
     * @return bool
     */
    public static function discussion_created(\mod_forum\event\discussion_created $event) {

		global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Discussion Created'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );
        return true;
    }

    /**
     * Event processor - Post created
     *
     * @param \mod_forum\event\post_created $event
     * @return bool
     */
    public static function post_created(\mod_forum\event\post_created $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Post Created'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );
        return true;
    }

    /**
     * Event processor - Course module viewed - File
     *
     * @param \mod_resource\event\course_module_viewed $event
     * @return bool
     */
    public static function file_viewed(\mod_resource\event\course_module_viewed $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Course Module Viewed', 'component' => 'resource'));

        $bool = $DB->record_exists('block_enhance_points', array (  'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    //'module_id' => $event->objectid,
                                                                    'module_id' => $event->contextinstanceid,
                                                                    'event_id' => $event_id
                                                            ));

        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for file module id:'.$event->contextinstanceid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            //'module_id' => $event->objectid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );       

        return true;
    }

    /**
     * Event processor - Course module viewed - Folder
     *
     * @param \mod_folder\event\course_module_viewed $event
     * @return bool
     */
    public static function folder_viewed(\mod_folder\event\course_module_viewed $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Course Module Viewed', 'component' => 'folder'));
        $bool = $DB->record_exists('block_enhance_points', array (  'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    'module_id' => $event->contextinstanceid,
                                                                    'event_id' => $event_id
                                                            ));

        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for folder module id:'.$event->contextinstanceid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );       
        return true;
    }

    /**
     * Event processor - Course module viewed - Page
     *
     * @param \mod_page\event\course_module_viewed $event
     * @return bool
     */
    public static function page_viewed(\mod_page\event\course_module_viewed $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Course Module Viewed', 'component' => 'page'));
        $bool = $DB->record_exists('block_enhance_points', array (  'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    'module_id' => $event->contextinstanceid,
                                                                    'event_id' => $event_id
                                                            ));

        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for page module id:'.$event->contextinstanceid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );       
        return true;
    }


    /**
     * Event processor - Course module viewed - URL
     *
     * @param \mod_url\event\course_module_viewed $event
     * @return bool
     */
    public static function ulr_viewed(\mod_url\event\course_module_viewed $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Course Module Viewed', 'component' => 'url'));
        $bool = $DB->record_exists('block_enhance_points', array (  'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    'module_id' => $event->contextinstanceid,
                                                                    'event_id' => $event_id
                                                            ));

        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for url module id:'.$event->contextinstanceid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );       
        return true;
    }


    /**
     * Event processor - Meeting joined
     *
     * @param \mod_bigbluebuttonbn\event\meeting_joined $event
     * @return bool
     */
    public static function meeting_joined(\mod_bigbluebuttonbn\event\meeting_joined $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Meeting Joined'));

        $bool = $DB->record_exists('block_enhance_points', array (  'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    'module_id' => $event->contextinstanceid,
                                                                    'event_id' => $event_id
                                                                ));

        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for BigBlueButton meeting id:'.$event->objectid.' and module id:'.$event->contextinstanceid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            
       
        return true;
    }

    /**
     * Event processor - Recording viewed
     *
     * @param \mod_bigbluebuttonbn\event\recording_viewed $event
     * @return bool
     */
    public static function recording_viewed(\mod_bigbluebuttonbn\event\recording_viewed $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $bool = $DB->record_exists('block_enhance_recordings', array (  'user_id' => $event->userid, 
                                                                        'course_id' => $event->courseid,
                                                                        'recording_id' => $event->other
                                                                ));
        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for BigBlueButton video id:'.$event->other.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Recording viewed'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            
        $DB->insert_record('block_enhance_recordings', array (   'user_id' => $event->userid, 
                                                                'course_id' => $event->courseid,
                                                                'recording_id' => $event->other
                                                            ) );
        return true;
    }


    /**
     * Event processor - Record created
     *
     * @param \mod_data\event\record_created $event
     * @return bool
     */
    public static function record_created(\mod_data\event\record_created $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Record created'));
        $bool = $DB->record_exists('block_enhance_points_values', array (   'user_id' => $event->userid, 
                                                                            'course_id' => $event->courseid,
                                                                            'event_id' => $event_id,
                                                                            'value' => $event->objectid
                                                                    ));
        
        if ($bool) {
            $DB->insert_record('block_enhance', array ('message' => 'user:'.$event->userid.' points for Data record id:'.$event->objectid.' in course: '.$event->courseid.' already counted!'));
            return true;
        }

        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            
        $DB->insert_record('block_enhance_points_values', array (   'user_id' => $event->userid, 
                                                                    'course_id' => $event->courseid,
                                                                    'event_id' => $event_id,
                                                                    'value' => $event->objectid
                                                                ) );       
        return true;
    }

    /**
     * Event processor - A submission has been submitted
     *
     * @param \mod_assign\event\assessable_submitted
     * @return bool
     */
    public static function submission(\mod_assign\event\assessable_submitted $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }        

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Assignment Submitted'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            

        return true;
    }

    /**
     * Event processor - Quiz attempt submitted
     *
     * @param \mod_quiz\event\attempt_submitted
     * @return bool
     */
    public static function attempt_submitted(\mod_quiz\event\attempt_submitted $event) {

        global $DB;

        $DB->insert_record('block_enhance', array ('message' => 'quiz submitted!!!'));

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Quiz Attempt Submitted'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            

        return true;
    }

    /**
     * Event processor - Journal entry created
     *
     * @param \mod_journal\event\entry_created
     * @return bool
     */
    public static function journal_entry_created(\mod_journal\event\entry_created $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Journal Entry Created'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            

        return true;
    } 
          
    /**
     * Event processor - Journal entry updated
     *
     * @param \mod_journal\event\entry_updated
     * @return bool
     */
    public static function journal_entry_updated(\mod_journal\event\entry_updated $event) {

        global $DB;

        $context = context_course::instance($event->courseid);
        if (!has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            $DB->insert_record('block_enhance', array ('message' => 'user: '.$event->userid.' has no capability in context: '.$event->contextid));
            return true;
        }

        $event_id = $DB->get_field('block_enhance_events', 'id', array('event_name' => 'Journal Entry Updated'));
        $DB->insert_record('block_enhance_points', array (  'user_id' => $event->userid, 
                                                            'course_id' => $event->courseid,
                                                            'module_id' => $event->contextinstanceid,
                                                            'event_id' => $event_id
                                                            ) );            

        return true;
    }                 
}
