"use strict";

var $j = jQuery.noConflict();

$j(document).bind("gform_post_render", function (event, form_id) {
    var gfaacData = window["gfaacMainJsVars_" + form_id];

    if (!gfaacData) {
        return;
    }

    var getFieldsData = gfaacData.elements;
    
    $j.each(getFieldsData, function (index, field) {

        var options,
            formId = field["form_id"],
            fieldId = field["field_id"],
            type = field["type"],
            inputId = "input_" + formId + "_" + fieldId;

        if (field["country"]) {
            options = {
                types: ["geocode"],
                componentRestrictions: {
                    country: field["country"],
                },
            };
        } else {
            options = {
                types: ["geocode"],
            };
        }

        if( field['type'] == 'address' ) {
            var aac_id = inputId + "_1";
        } else {
            var aac_id = inputId;
        }

        var aac_input = document.getElementById(aac_id);
        aac_input.addEventListener( "keydown", function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

        var autocomplete = new google.maps.places.Autocomplete(
            aac_input,
            {
                options,
            }
        );

        google.maps.event.addListener(
            autocomplete,
            "place_changed",
            function () {
                var place = autocomplete.getPlace();

                var result = getLocation(place, formId, fieldId);

                if( type == 'address' && field['single_line']) {
                    jQuery('#' + inputId + '_1').val(result.address).trigger('change');
                } else if( type == 'text' ) {
                    jQuery('#' + inputId).val(result.address).trigger('change');
                } else {
                    jQuery("#" + inputId + "_1").val(result.street).trigger("change");
                    jQuery("#" + inputId + "_3").val(result.city).trigger("change");
                    jQuery("#" + inputId + "_4").val(result.region_name).trigger("change");
                    jQuery("#" + inputId + "_5").val(result.postal_code).trigger("change");
                    jQuery("#" + inputId + "_6").val(result.country_name).trigger("change");
                }
            }
        );

    });

    function triggerLocationData( sourceId, location_name, location_value ) {

        if ( !jQuery( '.pcafe_' + sourceId + '.pcafe_' + location_name).length ) {
            return;
        }
        
        jQuery( '.pcafe_' + sourceId + '.pcafe_' + location_name).find( 'input[type="text"]' ).val(location_value).trigger('change');
    }

    function getLocation( results, formId, fieldId ) {

        var address     = results.address_components || [],
            sourceId    = formId + '_' + fieldId;
        
        var address_data = {
            'street_number'     : '',
            'street_name'       : '',
            'street'            : '',
            'subpremise'        : '',
            'premise'           : '',
            'city'              : '',
            'region_code'       : '',
            'region_name'       : '',
            'neighborhood'      : '',
            'postal_code'          : '',
            'country_code'      : '',
            'country_name'      : '',
            'address'           : results.formatted_address,
            'latitude'          : results.geometry.location.lat() || '',
            'longitude'         : results.geometry.location.lng() || '',
            'place_id'			: results.place_id || ''
        };

        triggerLocationData( sourceId, 'address', address_data.address );
        triggerLocationData( sourceId, 'latitude', address_data.latitude );
        triggerLocationData( sourceId, 'longitude', address_data.longitude );
        triggerLocationData( sourceId, 'place_id', address_data.place_id );

        for ( var x in address ) {

            if( address[x].types[0] == "street_number" ) {
                address_data.street_number = address[x].long_name;
                triggerLocationData( sourceId, 'street_number', address_data.street_number );
            }

            // street name and street fields
            if ( address[x].types == 'route' && address[x].long_name != undefined ) {  

                //save street name in variable
                address_data.street_name = address[x].long_name;
                
                triggerLocationData( sourceId, 'street_name', address_data.street_name );
            
                //udpate street ( number + name ) fields  if street_number exists
                if ( address_data.street_number != '' ) {

                    var TempSubPremise = ' ';

                    if ( address_data.subpremise != '' ) {
                        TempSubPremise = '/' + address_data.subpremise + ' ';
                    } 
                    address_data.street    = address_data.street_number + TempSubPremise + address_data.street_name;
                    
                } else {
                    address_data.street    = address_data.street_name;
                }     

                triggerLocationData( sourceId, 'street', address_data.street );
            }


            if ( address[x].types == 'subpremise' && address[x].long_name != undefined ) {
                address_data.subpremise = address[x].long_name;
                triggerLocationData( sourceId, 'subpremise', address_data.subpremise );
            }    

            // premise
            if ( address[x].types == 'premise' && address[x].long_name != undefined ) {
                address_data.premise = address[x].long_name;
                triggerLocationData( sourceId, 'premise', address_data.premise );
            }

            // neighborhood
                if ( address[x].types == 'neighborhood,political' && address[x].long_name != undefined ) {
                address_data.neighborhood = address[x].long_name;
                triggerLocationData( sourceId, 'neighborhood', address_data.neighborhood );
            }

            // city
            if ( address[x].types == 'locality,political' && address[x].long_name != undefined ) {

                address_data.city = address[x].long_name;
                triggerLocationData( sourceId, 'city', address_data.city );
            }

            // county
            if ( address[x].types == 'administrative_area_level_2,political' && address[x].long_name != undefined ) {
                address_data.county = address[x].long_name;
                triggerLocationData( sourceId, 'county', address_data.county );
            }

            // region code and name
            if ( address[x].types == 'administrative_area_level_1,political' ) {

                address_data.region_name = address[x].long_name;
                address_data.region_code = address[x].short_name;
                
                triggerLocationData( sourceId, 'region_code', address_data.region_code );
                triggerLocationData( sourceId, 'region_name', address_data.region_name );              
            }  

            if( address[x].types[0] == "postal_code" ) {
                address_data.postal_code = address[x].long_name;
                triggerLocationData( sourceId, 'postal_code', address_data.postal_code );
            }

            // country code and name
            if ( address[x].types == 'country,political' ) {

                address_data.country_name = address[x].long_name;
                address_data.country_code = address[x].short_name;

                triggerLocationData( sourceId, 'country_code', address_data.country_code );
                triggerLocationData( sourceId, 'country_name', address_data.country_name );           
            }
        }

        return address_data;
    }
});
