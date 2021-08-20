<?php
class BeRocket_AAPF_compat_JetSmartFilter {
    function __construct() {
        $filter_nn_name = apply_filters('berocket_aapf_filter_variable_name_nn', 'filters');
        if(defined('DOING_AJAX') && DOING_AJAX && !empty($_POST['action']) && $_POST['action'] == 'jet_smart_filters') {
            if( ! empty($_POST['brfilters']) ) {
                $_GET[$filter_nn_name] = $_POST['brfilters'];
                add_filter('jet-smart-filters/query/final-query', array($this, 'apply_filters'));
            }
        }
    }
    function apply_filters($query) {
        $BeRocket_AAPF = BeRocket_AAPF::getInstance();
        $query = $BeRocket_AAPF->woocommerce_filter_query_vars($query);
        return $query;
    }
}