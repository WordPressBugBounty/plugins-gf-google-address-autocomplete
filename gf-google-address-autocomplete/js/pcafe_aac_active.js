class PCAFE_AAC_Frontend {
    constructor( options ) {
        if (!options || !options.input || !options.formId || !options.fieldId) {
            throw new Error('Missing required options: input, formId, or fieldId');
        }
        this.options = options;
        this.init();
    }
    init() {
        if (this.options.is_async) {
            setTimeout(() => {
                this.init_autocomplete();
            }, 1500);
        } else {
            this.init_autocomplete();
        }
    }

    init_autocomplete() {
        const field_id = this.options.type === 'address' ? `${this.options.input}_1` : this.options.input;
        const init_selector = document.getElementById(field_id);

        if (!init_selector) {
            console.logo(`Element with ID ${field_id} not found`);
            return;
        }

        if (!window.google || !window.google.maps || !window.google.maps.places) {
            console.log('Google Maps API not loaded');
            return;
        }

        var options = {
            types: ["geocode"],
		};

        if (this.options.restrict_countries?.length > 0) {
            options.componentRestrictions = { country: this.options.restrict_countries };
        }

        const autocomplete = new google.maps.places.Autocomplete( init_selector, options );

        google.maps.event.addListener( autocomplete, 'place_changed', () => {
            
            var place = autocomplete.getPlace();
            
            var result = this.get_location(place, this.options.formId, this.options.fieldId);

            if (this.options.type === 'address' && this.options.single_line) {
                const input = document.getElementById(`${this.options.input}_1`);
                if (input) {
                    input.value = result.address;
                    input.dispatchEvent(new Event('change'));
                }
            } else if (this.options.type === 'text') {
                const input = document.getElementById(this.options.input);
                if (input) {
                    input.value = result.address;
                    input.dispatchEvent(new Event('change'));
                }
            } else {
                const fields = [
                    { id: `${this.options.input}_1`, value: result.street },
                    { id: `${this.options.input}_3`, value: result.city },
                    { id: `${this.options.input}_4`, value: result.region_name },
                    { id: `${this.options.input}_5`, value: result.postal_code },
                    { id: `${this.options.input}_6`, value: result.country_name },
                ];
                fields.forEach(({ id, value }) => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.value = value;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
        });
    }

    trigger_location_data(sourceId, location_name, location_value) {
        const selector = `.pcafe_${sourceId}.pcafe_${location_name} input[type="text"]`;
        const elements = document.querySelectorAll(selector);
        elements.forEach((input) => {
            input.value = location_value;
            input.dispatchEvent(new Event('change'));
        });
    }

    get_location( results, formId, fieldId ) {

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

        const commonFields = [
            { name: 'address', value: address_data.address },
            { name: 'latitude', value: address_data.latitude },
            { name: 'longitude', value: address_data.longitude },
            { name: 'place_id', value: address_data.place_id },
        ];

        commonFields.forEach(({ name, value }) => {
            if (document.querySelector(`.pcafe_${sourceId}.pcafe_${name}`)) {
                this.trigger_location_data(sourceId, name, value);
            }
        });

        // Process address components
        address.forEach((component) => {
            const types = component.types;
            const long_name = component.long_name || '';
            const short_name = component.short_name || '';

            // Single-type mappings
            const singleTypeMap = [
                { type: 'street_number', name: 'street_number', value: long_name },
                { type: 'route', name: 'street_name', value: long_name },
                { type: 'subpremise', name: 'subpremise', value: long_name },
                { type: 'premise', name: 'premise', value: long_name },
                { type: 'postal_code', name: 'postal_code', value: long_name },
            ];

            // Multi-type mappings
            const multiTypeMap = [
                { types: ['neighborhood', 'political'], name: 'neighborhood', value: long_name },
                { types: ['locality', 'political'], name: 'city', value: long_name },
                { types: ['administrative_area_level_2', 'political'], name: 'county', value: long_name },
                { types: ['administrative_area_level_1', 'political'], name: 'region_name', value: long_name },
                { types: ['administrative_area_level_1', 'political'], name: 'region_code', value: short_name },
                { types: ['country', 'political'], name: 'country_name', value: long_name },
                { types: ['country', 'political'], name: 'country_code', value: short_name },
            ];

            // Process single-type components
            singleTypeMap.forEach(({ type, name, value }) => {
                if (types.includes(type)) {
                    address_data[name] = value;
                    if (document.querySelector(`.pcafe_${sourceId}.pcafe_${name}`)) {
                        this.trigger_location_data(sourceId, name, value);
                    }
                }
            });

            // Process multi-type components
            multiTypeMap.forEach(({ types: requiredTypes, name, value }) => {
                if (requiredTypes.every(t => types.includes(t))) {
                    address_data[name] = value;
                    if (document.querySelector(`.pcafe_${sourceId}.pcafe_${name}`)) {
                        this.trigger_location_data(sourceId, name, value);
                    }
                }
            });
    
        });

        // Compute street field
        if (address_data.street_name) {
            address_data.street = address_data.street_number
                ? `${address_data.street_number}${address_data.subpremise ? `/${address_data.subpremise}` : ''} ${address_data.street_name}`
                : address_data.street_name;
            if (document.querySelector(`.pcafe_${sourceId}.pcafe_street`)) {
                this.trigger_location_data(sourceId, 'street', address_data.street);
            }
        }

        return address_data;
    }
}