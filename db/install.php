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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     block_enhance
 * @category    upgrade
 * @copyright   2019 Eleftherios Kosmas <ekosmas@hmu.gr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_block_enhance_install() {

	global $DB;

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Discussion Created',
                                                      	'component' => 'forum',
                                                        'type_4cs_map' => 'CONNECT',
                                                        'points' => 3
                                                    ) );


	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Post Created',
                                                        'component' => 'forum',
                                                        'type_4cs_map' => 'CONNECT',
                                                        'points' => 3
                                                    ) );


	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Course Module Viewed',
                                                        'component' => 'resource',
                                                	    'type_4cs_map' => 'CONSUME',
                                                        'points' => 5
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Course Module Viewed',
                                                        'component' => 'folder',
                                                        'type_4cs_map' => 'CONSUME',
                                                        'points' => 5
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Course Module Viewed',
                                                        'component' => 'page',
                                                        'type_4cs_map' => 'CONSUME',
                                                        'points' => 5
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Course Module Viewed',
                                                        'component' => 'url',
                                                        'type_4cs_map' => 'CONSUME',
                                                        'points' => 5
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Meeting Joined',
                                                        'component' => 'bigbluebuttonbn',
                                                        'type_4cs_map' => 'CONSUME',
                                                        'points' => 10
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Recording viewed',
                                                        'component' => 'bigbluebuttonbn',
                                                	    'type_4cs_map' => 'CONSUME',
                                                        'points' => 10
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Record created',
                                                        'component' => 'data',
                                                	    'type_4cs_map' => 'CONTRIBUTE',
                                                        'points' => 12
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Assignment Submitted',
                                                        'component' => 'assign',
                                                        'type_4cs_map' => 'CREATE',
                                                        'points' => 10
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Quiz Attempt Submitted',
                                                        'component' => 'quiz',
                                                        'type_4cs_map' => 'CREATE',
                                                        'points' => 10
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Journal Entry Created',
                                                        'component' => 'journal',
                                                        'type_4cs_map' => 'CREATE',
                                                        'points' => 5
                                                    ) );

	$DB->insert_record('block_enhance_events', array (	'event_name' => 'Journal Entry Updated',
                                                        'component' => 'journal',
                                                        'type_4cs_map' => 'CREATE',
                                                        'points' => 5
                                                    ) );	

    return true;
}
