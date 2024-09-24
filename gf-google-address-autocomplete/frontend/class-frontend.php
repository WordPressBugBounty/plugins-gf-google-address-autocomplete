<?php

class GF_auto_address_complete_frontend {

    function __construct() {
        add_action( 'gform_enqueue_scripts', array($this, 'pc_enqueue_scripts'), 10, 2 );
    }

    function pc_enqueue_scripts( $form, $is_ajax ) {

        $form_id = $form['id'];
        $fields_data = [];
  
        foreach($form['fields'] as $field) {
			if (property_exists($field, 'autocompleteGField') && $field->autocompleteGField) {
                $input = "{$field->formId}_{$field->id}";

                $fields_data[$input]['form_id'] = $field->formId;
                $fields_data[$input]['field_id'] = $field->id;
                $fields_data[$input]['type'] = $field->type;
                $fields_data[$input]['single_line'] = $field->singleAutofillGField;
                $fields_data[$input]['country'] = empty($field['restrictCountryGField']) ? '' : strtolower($field['restrictCountryGField']);
			}

			if (property_exists($field, 'textAutocompleteGField') && $field->textAutocompleteGField) {
                $input = "{$field->formId}_{$field->id}";

                $fields_data[$input]['form_id'] = $field->formId;
                $fields_data[$input]['field_id'] = $field->id;
                $fields_data[$input]['type'] = $field->type;
                $fields_data[$input]['country'] = empty($field['restrictCountryTextGField']) ? '' : strtolower($field['restrictCountryTextGField']);
            }
        }

        $pc_gf_google_api_key	=	get_option('pc_gf_google_api_key');
        $is_async	=	get_option('pcafe_load_with_async');
        $add_async = '';
        if( $is_async ) {
            $add_async = '&loading=async';
        }
        
		if(!empty($pc_gf_google_api_key)){
			wp_enqueue_script('pc-google-places',"https://maps.googleapis.com/maps/api/js?v=3.exp&key=".$pc_gf_google_api_key."&libraries=places" . $add_async);
		}
		else{
			wp_enqueue_script('pc-google-places',"https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places");
        }
        
        wp_enqueue_script('pc_ajax_data', GF_AUTO_ADDRESS_COMPLETE_URL.'js/map_data.js', array( 'pc-google-places' ), GF_AUTO_ADDRESS_COMPLETE_VERSION_NUM, true );
        wp_localize_script('pc_ajax_data', 'gfaacMainJsVars_'.$form_id, array(
            'elements' =>  $fields_data
            )
        );
    }


}

new GF_auto_address_complete_frontend();