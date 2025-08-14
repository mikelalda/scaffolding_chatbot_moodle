<?php

class block_course_chatbot extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_course_chatbot');
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        $display_name = $this->config->chatbot_display_name ?? get_string('defaultchatbotname', 'block_course_chatbot');
        $units = $this->config->units ?? [];

        if (empty($units)) {
            $this->content->text = html_writer::tag('p', get_string('noconfigset_single', 'block_course_chatbot'));
            return $this->content;
        }

        $data = [
            'blockid' => $this->instance->id,
            'displayname' => $display_name,
            'units' => array_values($units),
            'chatbot_initial_message' => get_string('initialmessage', 'block_course_chatbot'),
        ];

        $renderer = $this->page->get_renderer('block_course_chatbot');
        $this->content->text = $renderer->render_from_template('block_course_chatbot/chatbot_block', $data);

        return $this->content;
    }

    public function after_get_content() {
        if (!empty($this->config->units)) {
            $js_params = [
                'blockid' => $this->instance->id,
                'error_string' => get_string('errorprocessing', 'block_course_chatbot'),
                'choose_unit_title' => get_string('chooseunit', 'block_course_chatbot')
            ];
            $this->page->requires->js_call_amd('block_course_chatbot/chatbot', 'init', [$js_params]);
        }
        return parent::after_get_content();
    }

    public function instance_config_save($data, $dontdelete = false) {
        if ($this->config === null) {
            $this->config = new stdClass();
        }

        $data = (object)$data;
        $this->config->chatbot_display_name = $data->config_chatbot_display_name ?? get_string('defaultchatbotname', 'block_course_chatbot');
        
        $old_units = $this->config->units ?? [];
        $new_units = [];
        $fs = get_file_storage();
        $unitcount = 5;

        for ($i = 0; $i < $unitcount; $i++) {
            $unitname = trim($data->{'unitname['.$i.']'} ?? '');
            $draftitemid = $data->{'jsonfile['.$i.']'} ?? null;

            if (empty($unitname)) {
                continue; // Skip empty slots.
            }

            $json_content = null;

            // Priority 1: A new file was uploaded.
            if ($draftitemid) {
                $usercontext = \context_user::instance($GLOBALS['USER']->id);
                $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'sortorder', false);
                if ($files) {
                    $file = reset($files);
                    $content = $file->get_content();
                    if (json_decode($content) !== null) {
                        $json_content = $content;
                    }
                }
            }

            // Priority 2: No new file, so keep the old JSON if it exists for this slot.
            if (is_null($json_content) && isset($old_units[$i])) {
                $json_content = $old_units[$i]->json;
            }

            // We must have JSON content to save the unit.
            if (!is_null($json_content)) {
                $new_units[$i] = (object)[
                    'name' => $unitname,
                    'json' => $json_content
                ];
            }
        }
        
        $this->config->units = array_values($new_units); // Re-index the array.
        
        return parent::instance_config_save((object)$this->config, $dontdelete);
    }

    public function instance_allow_multiple() {
        return true;
    }
}
