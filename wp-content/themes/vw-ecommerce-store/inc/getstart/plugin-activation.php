<?php
if ( ! class_exists( 'VW_Ecommerce_Store_Plugin_Activation_Settings' ) ) {
	/**
	 * VW_Ecommerce_Store_Plugin_Activation_Settings initial setup
	 *
	 * @since 1.6.2
	 */

	class VW_Ecommerce_Store_Plugin_Activation_Settings {

        private static $instance;

        /** Initiator **/
        public static function get_instance() {
          if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
          }
          return self::$instance;
        }

        /*  Constructor */
        public function __construct() {

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            // ---------- Ibtana Plugin Activation -------
            add_filter( 'vw_ecommerce_store_recommended_plugins', array($this, 'vw_ecommerce_store_recommended_plugins_array') );

            $actions                   = $this->vw_ecommerce_store_get_recommended_actions();
            $this->action_count        = $actions['count'];
            $this->recommended_actions = $actions['actions'];

            add_action( 'wp_ajax_create_pattern_setup_builder', array( $this, 'create_pattern_setup_builder' ) );
        }

        public function vw_ecommerce_store_recommended_plugins_array($plugins){
            $plugins[] = array(
                'name'     => esc_html__('Ibtana - WordPress Website Builder', 'vw-ecommerce-store'),
                'slug'     => 'ibtana-visual-editor',
                'function' => 'Ibtana_Visual_Editor_Menu_Class',
                'desc'     => esc_html__('It is recommended that you install the Ibtana - WordPress Website Builder plugin to show advance blocks and templates on pages', 'vw-ecommerce-store'),
            );          
            return $plugins;
        }

        public function enqueue_scripts() {
            wp_enqueue_script('updates');
            wp_register_script( 'vw-ecommerce-store-plugin-activation-script', esc_url(get_template_directory_uri()) . '/assets/js/plugin-activation.js', array('jquery') );
            wp_localize_script('vw-ecommerce-store-plugin-activation-script', 'vw_ecommerce_store_plugin_activate_plugin',
                array(
                    'installing' => esc_html__('Installing', 'vw-ecommerce-store'),
                    'activating' => esc_html__('Activating', 'vw-ecommerce-store'),
                    'error' => esc_html__('Error', 'vw-ecommerce-store'),
                    'ajax_url' => esc_url(admin_url('admin-ajax.php')),
                    'ibtana_admin_url' => esc_url(admin_url('admin.php?page=ibtana-visual-editor-templates')),
                )
            );
            wp_enqueue_script( 'vw-ecommerce-store-plugin-activation-script' );

        }

        // --------- Plugin Actions ---------
        public function vw_ecommerce_store_get_recommended_actions() {

            $act_count           = 0;
            $actions_todo = get_option( 'recommending_actions', array());
            
            $plugins = $this->vw_ecommerce_store_get_recommended_plugins();

            if ($plugins) {
                foreach ($plugins as $key => $plugin) {
                    $action = array();
                    if (!isset($plugin['slug'])) {
                        continue;
                    }

                    $action['id']   = 'install_' . $plugin['slug'];
                    $action['desc'] = '';
                    if (isset($plugin['desc'])) {
                        $action['desc'] = $plugin['desc'];
                    }

                    $action['name'] = '';
                    if (isset($plugin['name'])) {
                        $action['title'] = $plugin['name'];
                    }

                    $link_and_is_done  = $this->vw_ecommerce_store_get_plugin_buttion($plugin['slug'], $plugin['name'], $plugin['function']);
                    $action['link']    = $link_and_is_done['button'];
                    $action['is_done'] = $link_and_is_done['done'];
                    if (!$action['is_done'] && (!isset($actions_todo[$action['id']]) || !$actions_todo[$action['id']])) {
                        $act_count++;
                    }
                    $recommended_actions[] = $action;
                    $actions_todo[]        = array('id' => $action['id'], 'watch' => true);
                }
                return array('count' => $act_count, 'actions' => $recommended_actions);
            }

        }

        public function vw_ecommerce_store_get_recommended_plugins() {
                
            $plugins = apply_filters('vw_ecommerce_store_recommended_plugins', array());
            return $plugins;
        }

        public function vw_ecommerce_store_get_plugin_buttion($slug, $name, $function) {
                $is_done      = false;
                $button_html  = '';
                $is_installed = $this->is_plugin_installed($slug);
                $plugin_path  = $this->get_plugin_basename_from_slug($slug);
                $is_activeted = (class_exists($function)) ? true : false;
                if (!$is_installed) {
                    $plugin_install_url = add_query_arg(
                        array(
                            'action' => 'install-plugin',
                            'plugin' => $slug,
                        ),
                        self_admin_url('update.php')
                    );
                    $plugin_install_url = wp_nonce_url($plugin_install_url, 'install-plugin_' . esc_attr($slug));
                    $button_html        = sprintf('<a class="vw-ecommerce-store-plugin-install install-now button-secondary button" data-slug="%1$s" href="%2$s" aria-label="%3$s" data-name="%4$s">%5$s</a>',
                        esc_attr($slug),
                        esc_url($plugin_install_url),
                        sprintf(esc_html__('Install %s Now', 'vw-ecommerce-store'), esc_html($name)),
                        esc_html($name),
                        esc_html__('Install & Activate', 'vw-ecommerce-store')
                    );
                } elseif ($is_installed && !$is_activeted) {

                    $plugin_activate_link = add_query_arg(
                        array(
                            'action'        => 'activate',
                            'plugin'        => rawurlencode($plugin_path),
                            'plugin_status' => 'all',
                            'paged'         => '1',
                            '_wpnonce'      => wp_create_nonce('activate-plugin_' . $plugin_path),
                        ), self_admin_url('plugins.php')
                    );

                    $button_html = sprintf('<a class="vw-ecommerce-store-plugin-activate activate-now button-primary button" data-slug="%1$s" href="%2$s" aria-label="%3$s" data-name="%4$s">%5$s</a>',
                        esc_attr($slug),
                        esc_url($plugin_activate_link),
                        sprintf(esc_html__('Activate %s Now', 'vw-ecommerce-store'), esc_html($name)),
                        esc_html($name),
                        esc_html__('Activate', 'vw-ecommerce-store')
                    );
                } elseif ($is_activeted) {
                    $button_html = sprintf('<div class="action-link button disabled"><span class="dashicons dashicons-yes"></span> %s</div>', esc_html__('Active', 'vw-ecommerce-store'));
                    $is_done     = true;
                }

                return array('done' => $is_done, 'button' => $button_html);
            }
        public function is_plugin_installed($slug) {
            $installed_plugins = $this->get_installed_plugins(); // Retrieve a list of all installed plugins (WP cached).
            $file_path         = $this->get_plugin_basename_from_slug($slug);
            return (!empty($installed_plugins[$file_path]));
        }
        public function get_plugin_basename_from_slug($slug) {
            $keys = array_keys($this->get_installed_plugins());
            foreach ($keys as $key) {
                if (preg_match('|^' . $slug . '/|', $key)) {
                    return $key;
                }
            }
            return $slug;
        }
        
        public function get_installed_plugins() {

            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            return get_plugins();
        }
        public function create_pattern_setup_builder() {

            $edit_page = admin_url().'post-new.php?post_type=page&create_pattern=true';
            echo json_encode(['page_id'=>'','edit_page_url'=> $edit_page ]);

            exit;
        }

    }
}
/**
 * Kicking this off by calling 'get_instance()' method
 */
VW_Ecommerce_Store_Plugin_Activation_Settings::get_instance();