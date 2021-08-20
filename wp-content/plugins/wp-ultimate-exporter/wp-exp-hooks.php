<?php
/**
 * WP Ultimate Exporter plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\SMEXP;

if ( ! defined( 'ABSPATH' ) ) {
        die;
}

global $plugin_ajax_hooks;

$plugin_ajax_hooks = [
    'parse_data',
    'total_records',
    'get_post_types',
    'get_taxonomies',
    'get_authors'
];