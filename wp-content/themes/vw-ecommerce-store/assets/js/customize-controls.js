( function( api ) {

	// Extends our custom "vw-ecommerce-store" section.
	api.sectionConstructor['vw-ecommerce-store'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );