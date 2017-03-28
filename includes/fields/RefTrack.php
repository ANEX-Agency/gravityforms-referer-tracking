<?php

class GF_Field_RefTrack extends GF_Field {

    public $type = 'reftrack';

    public function get_form_editor_field_title() {
        return esc_attr__( 'Referer Tracking', 'gravityforms-referer-tracking' );
    }

    public function get_form_editor_field_settings() {
        return array(
            'label_setting',
        );
    }

    public function get_field_input() {
        return '';
    }

    public function get_field_content($value, $force_frontend_label, $form) {

        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;

        if(!is_admin())
            return '';

        $form_id         = $form['id'];
        $admin_buttons   = $this->get_admin_buttons();

        $field_label     = $this->get_field_label( $force_frontend_label, $value );
        $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";

        $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

        return $field_content;
    }

    public function get_value_save_entry() {

        $engine = Rebits_GF_RefTrack::get_instance()->getEngine();

        return serialize($engine->getCookieData());
    }

    protected function _getPairStrings($data, $flat = true, $wrapStart = '', $wrapEnd = '') {
        $pairs = array();

        foreach($data as $k => $v) {

            if(!is_array($v)) {

                $html = $wrapStart . $k . ': ' . $v . $wrapEnd;

            } else if($flat) {

                $html = $wrapStart . $k . ': ' . implode(', ', $v) . $wrapEnd;

            } else {

                $html = $wrapStart . $k . '<ul style="padding: 0 15px">';

                foreach($v as $x) {
                    $html .= '<li>' . $x . '</li>';
                }

                $html .= '</ul>' . $wrapEnd;
            }

            $pairs[] = $html;
        }

        return $pairs;
    }

    protected function _getData($data) {

        $data = @unserialize($data);
        if(!$data)
            return;

        if(isset($data['timestamp']) && is_numeric($data['timestamp'])) {
            $format = get_option('date_format') . ' ' . get_option('time_format');
            $date = date_i18n($format, $data['timestamp']);

            // ensure that it is at the end
            unset($data['timestamp']);
            $data[__('Date', 'gravityforms-referer-tracking')] = $date;
        }

        return $data;
    }

    public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

        $data = $this->_getData($value);

        if(!$data)
            return '';

        return esc_html( implode(', ', $this->_getPairStrings($data)) );
    }

    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

        $data = $this->_getData($value);

        if(!$data)
            return '';

        $items = implode('', $this->_getPairStrings($data, false, '<li>', '</li>'));

        return "<ul class='bulleted'>{$items}</ul>";
    }
}

GF_Fields::register( new GF_Field_RefTrack() );
