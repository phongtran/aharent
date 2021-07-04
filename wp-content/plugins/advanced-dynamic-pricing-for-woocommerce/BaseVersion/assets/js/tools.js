/**
 * global wdp_export_items
 * */

jQuery(document).ready(function () {
    if (window.wdp_export_items) {
        var wdp_export_items = JSON.parse(window.wdp_export_items);

        jQuery('#wdp-export-select').change(function () {
            var selected = jQuery(this).val();
            jQuery('#wdp-export-data').val(JSON.stringify(wdp_export_items[selected]['data'], null, 5));
        }).change();


        jQuery('#wdp-export-data').click(function () {
            jQuery(this).select();
        });

        jQuery('#wdp-import').click(function () {
            if( jQuery('#wdp-import-data').val() == "" ) {
				return false;
			}
        });

	jQuery('#wdp-import-select').change(function () {
            var selected = jQuery(this).val();
            jQuery('.wdp-import-tools-form .wdp-import-type-options').removeClass('active');
            jQuery('.wdp-import-tools-form .wdp-import-type-options-' + selected).addClass('active');
        }).change();
    }
});