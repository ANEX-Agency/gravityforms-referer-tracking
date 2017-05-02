<?php

class GF_Field_Referer_Tracking extends GF_Field {

    public $type = 'reftrack';
	
	// # FORM EDITOR & FIELD MARKUP -------------------------------------------------------------------------------------
	
    public function get_form_editor_field_title() {
        return esc_attr__( 'Referer Tracking', 'gravityforms-referer-tracking' );
    }
	
	/**
	 * Return the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
    public function get_form_editor_field_settings() {
        return array(
            'label_setting',
        );
    }
	
	/**
	 * Returns the field inner markup.
	 *
	 * @param array $form The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
    public function get_field_input( $form, $value = '', $entry = NULL ) {
        return '';
    }

    public function get_field_content( $value, $force_frontend_label, $form ) {

        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;

        if( !is_admin() )
            return '';

        $form_id         = $form['id'];
        $admin_buttons   = $this->get_admin_buttons();

        $field_label     = $this->get_field_label( $force_frontend_label, $value );
        $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";

        $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

        return $field_content;
		
    }

    public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {

        $engine = GF_Referer_Tracking::get_instance()->get_engine();

        return serialize( $engine->get_cookie_data() );
		
    }

    protected function _get_pair_strings( $data, $flat = true, $wrapStart = '', $wrapEnd = '' ) {
		
        $pairs = array();

        foreach( $data as $k => $v ) {

            if( ! is_array( $v ) ) {

                $html = $wrapStart . $k . ': ' . $v . $wrapEnd;

            } else if( $flat ) {

                $html = $wrapStart . $k . ': ' . implode( ', ', $v ) . $wrapEnd;

            } else {

                $html = $wrapStart . $k . '<ul style="padding: 0 15px">';

                foreach( $v as $x ) {
                    $html .= '<li>' . $x . '</li>';
                }

                $html .= '</ul>' . $wrapEnd;
            }

            $pairs[] = $html;
        }

        return $pairs;
		
    }

    protected function _get_data( $data ) {

        $data = @unserialize( $data );
		
        if( ! $data )
            return;

        if( isset( $data['timestamp'] ) && is_numeric( $data['timestamp'] ) ) {
			
            $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
            $date	= date_i18n( $format, $data['timestamp'] );

            // ensure that it is at the end
            unset( $data['timestamp'] );
            $data[__( 'Date', 'gravityforms-referer-tracking' )] = $date;
			
        }

        return $data;
		
    }
	
	/**
	 * Format the entry value for display on the entries list page.
	 *
	 * @param string|array $value The field value.
	 * @param array $entry The Entry Object currently being processed.
	 * @param string $field_id The field or input ID currently being processed.
	 * @param array $columns The properties for the columns being displayed on the entry list page.
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return string
	 */
    public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

        $data = $this->_get_data( $value );

        if( ! $data )
            return '';

        return esc_html( implode( ', ', $this->_get_pair_strings( $data ) ) );
		
    }
	
	/**
	 * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * @param string|array $value The field value.
	 * @param string $currency The entry currency code.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string $media The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

        $data = $this->_get_data( $value );

        if( ! $data )
            return '';

        $items = implode( '', $this->_get_pair_strings( $data, false, '<li>', '</li>' ) );

        return "<ul class='bulleted'>{$items}</ul>";
    }

}

GF_Fields::register( new GF_Field_Referer_Tracking() );