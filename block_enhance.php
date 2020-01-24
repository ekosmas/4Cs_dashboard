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
 * Block enhance is defined here.
 *
 * @package     block_enhance
 * @copyright   2019 Eleftherios Kosmas <ekosmas@hmu.gr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enhance extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        global $COURSE, $DB;

        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_enhance');

    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {

        global $COURSE, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();

        $courseid = $COURSE->id;
        $context = context_course::instance($courseid);
        $div = '';
        $this->content->text = '';
        if (has_capability('block/enhance:editpreferences', $context, NULL, false)) {
            // show Teacher's edit link
            $url_edit_preferences = new moodle_url('/blocks/enhance/edit_preferences.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
            $this->content->text = html_writer::link($url_edit_preferences, get_string('show_edit_preferences', 'block_enhance')).'<hr><p> Nurse Sally\'s 4Cs Dashboard</p>';
        }
        else if (has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            // show Student's link
            $url_my_report_aggregate = new moodle_url('/blocks/enhance/my_report_aggregate.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));

            $picurl = new moodle_url('/blocks/enhance/photos/4C_Map.png');
            $piclink = html_writer::link(
                $url_my_report_aggregate, 
                html_writer::tag('img', '', array('src' => $picurl, 'alt' => 'hello', 'width'=> '180', 'height' => '180', 'class' => 'atto_image_button_middle'))
            );
            $div1 = html_writer::div($piclink, null, array('style' => 'text-align:center;'));

            $div2 = html_writer::div(
                '<p style="text-align: center;"> Click on the image above to access your "<b>4Cs Dashboard</b>" and monitor your behaviors!</p>', 
                null, 
                array('style' => 'background-color: #def2f8; color: #2f6473; border-style: solid; border-width: thin; border-color: #d1edf6; padding: 4px;')
            );

            $div = $div1." ".$div2."<hr>";
        }

        if (has_capability('block/enhance:editpreferences', $context, NULL, false) ||
            has_capability('block/enhance:earnpoints', $context, NULL, false)) {
            // show Nurse Sully's link
            $url_my_report_aggregate = new moodle_url('/blocks/enhance/nurse_sally.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
            $picurl = new moodle_url('/blocks/enhance/photos/Nurse_Sally_Dashboard.png');
            $piclink = html_writer::link(
                $url_my_report_aggregate, 
                html_writer::tag('img', '', array('src' => $picurl, 'alt' => 'hello', 'width'=> '180', 'height' => '180', 'class' => 'atto_image_button_middle'))
            );
            $div3 = html_writer::div($piclink, null, array('style' => 'text-align:center;'));

            $div4 = html_writer::div(
                '<p style="text-align: center;"> Click on the suitcase above to access "<b>Nurse Sally\'s 4Cs Dashboard</b>" and monitor her behaviors!</p>',
                '', 
                array('style' => 'background-color: #def2f8; color: #2f6473; border-style: solid; border-width: thin; border-color: #d1edf6; padding: 4px;')
            );
            
            $div = $div.$div3." ".$div4;
        }

        $this->content->text .= $div;

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_enhance');
        } else {
            $this->title = $this->config->title;
        }


    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    function has_config() {
        return true;
    }
}
