<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(

//	Event name							Component			Database query type		4Cs Mapping		Points
//	Discussion created					Forum				create					CONNECT			3
 	array(
        'eventname'   => '\mod_forum\event\discussion_created',
        'callback'    => 'block_enhance_observer::discussion_created',
        'internal'    => false,
    ),

//	Post created						Forum				create					CONNECT			3
 	array(
        'eventname'   => '\mod_forum\event\post_created',
        'callback'    => 'block_enhance_observer::post_created',
        'internal'    => false,
    ),

//	Course module viewed 				File				read					CONSUME			5
 	array(
        'eventname'   => '\mod_resource\event\course_module_viewed',
        'callback'    => 'block_enhance_observer::file_viewed',
        'internal'    => false,
    ),


//	Course module viewed				Folder				read					CONSUME			5
 	array(
        'eventname'   => '\mod_folder\event\course_module_viewed',		// ??? or \mod_publication\event\course_module_viewed ???
        'callback'    => 'block_enhance_observer::folder_viewed',
        'internal'    => false,
    ),

//	Course module viewed				Page				read					CONSUME			5
 	array(
        'eventname'   => '\mod_page\event\course_module_viewed',
        'callback'    => 'block_enhance_observer::page_viewed',
        'internal'    => false,
    ),

//	Course module viewed				URL					read					CONSUME			5
 	array(
        'eventname'   => '\mod_url\event\course_module_viewed',
        'callback'    => 'block_enhance_observer::ulr_viewed',
        'internal'    => false,
    ),

//	Meeting joined						BigBlueButtonBN		?						CONSUME			10
 	array(
        'eventname'   => '\mod_bigbluebuttonbn\event\meeting_joined',
        'callback'    => 'block_enhance_observer::meeting_joined',
        'internal'    => false,
    ),

//	Recording viewed					BigBlueButtonBN		?						CONSUME			10
 	array(
        'eventname'   => '\mod_bigbluebuttonbn\event\recording_viewed',
        'callback'    => 'block_enhance_observer::recording_viewed',
        'internal'    => false,
    ),

//	Record created						Database			create					CONTRIBUTE		10
 	array(
        'eventname'   => '\mod_data\event\record_created',
        'callback'    => 'block_enhance_observer::record_created',
        'internal'    => false,
    ),

//	A submission has been submitted		Assignment			update					CREATE			10
 	array(
        'eventname'   => '\mod_assign\event\assessable_submitted',
        'callback'    => 'block_enhance_observer::submission',
        'internal'    => false,
    ),
    
    			
//	Quiz attempt submitted				Quiz				update					CREATE			10
 	array(
        'eventname'   => '\mod_quiz\event\attempt_submitted',
        'callback'    => 'block_enhance_observer::attempt_submitted',
        'internal'    => false,
    ),    
    
//	Journal entry created				Journal				?						CREATE			5
 	array(
        'eventname'   => '\mod_journal\event\entry_created',
        'callback'    => 'block_enhance_observer::journal_entry_created',
        'internal'    => false,
    ),       
    
//	Journal entry updated				Journal				?						CREATE			5
 	array(
        'eventname'   => '\mod_journal\event\entry_updated',
        'callback'    => 'block_enhance_observer::journal_entry_updated',
        'internal'    => false,
    ),       
);
