<?php
if( ! class_exists('BeRocket_AAPF_compat_Relevanssi') ) {
    class BeRocket_AAPF_compat_Relevanssi {
        function __construct() {
            if( function_exists('relevanssi_do_query') ) {
                remove_filter('berocket_aapf_recount_terms_query', array('BeRocket_AAPF_faster_attribute_recount', 'search_query'), 50, 3);
                add_filter('berocket_aapf_recount_terms_query', array(__CLASS__, 'search_query'), 50, 3);
                add_filter('bapf_query_count_before_update', array(__CLASS__, 'count_before_update'));
                add_filter('relevanssi_modify_wp_query', array(__CLASS__, 'relevanssi_query'));
            }
        }
        static function count_before_update($query) {
            if( function_exists('relevanssi_do_query') ) {
                $search_ok = true;
                if ( ! $query->is_search() ) {
                    $search_ok = false;
                }
                if ( ! $query->is_main_query() ) {
                    $search_ok = false;
                }
                if( apply_filters('bapf_compat_relevansi_apply_count_before_update', $search_ok) ) {
                    relevanssi_do_query($query);
                }
            }
            return $query;
        }
        static function search_query($query, $taxonomy_data, $terms) {
            extract($taxonomy_data);
            if( ! empty($use_filters) ) {
                $WC_query = WC_Query::get_main_query();
                $search_ok = (! empty($WC_query) && ! empty($WC_query->query_vars['s']));
                if( apply_filters('bapf_compat_relevansi_apply_search_query', $search_ok) ) {
                    $args  = array(
                        's'           => $WC_query->query_vars['s'],
                        'nopaging '   => true,
                        'fields'      => 'ids'
                    );
                    global $wpdb;
                    $queryrelevanssi = new WP_Query();
                    $queryrelevanssi->parse_query( $args );

                    $posts = relevanssi_do_query( $queryrelevanssi );
                    if( empty($posts) || count($posts) == 0 ) {
                        $posts = array(0);
                    }
                    $query['where']['search'] = "AND {$wpdb->posts}.ID IN (" . implode(',', $posts) . ")";
                }
            }
            return $query;
        }
        public static function is_relevanssi( $s ) {
            if ( function_exists('relevanssi_do_query') and strstr( $s, "}')" ) !== false ) {
                return true;
            }

            return false;
        }
        static function relevanssi_query($query) {
            global $wpdb;
            $args = array(
                'where' => '',
                'join'  => '',
            );
            $BeRocket_AAPF = BeRocket_AAPF::getInstance();
            $args = $BeRocket_AAPF->price_filter_post_clauses($args, $query);
            if( ! empty($args['where']) || ! empty($args['join']) ) {
                $join = ( empty($args['join']) ? '' : $args['join'] );
                $where = ( empty($args['where']) ? '' : $args['where'] );
                $request = "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} $join WHERE 1=1 $where group by {$wpdb->posts}.ID";
                $ids = $wpdb->get_col($request, 0);
                if( ! is_array($ids) || empty($ids) ) {
                    $ids = array();
                }
                $posts_in = $query->get( 'post__in' );
                if( empty($posts_in) ) {
                    $posts_in = $ids;
                } else {
                    $posts_in = array_intersect($posts_in, $ids);
                }
                if( count($posts_in) == 0 ) {
                    $posts_in = array(0);
                }
                $posts_in = $query->set( 'post__in', $posts_in );
            }
            return $query;
        }
    }
    new BeRocket_AAPF_compat_Relevanssi();
}
