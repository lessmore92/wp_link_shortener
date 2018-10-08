<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://mojtababah.com
 * @since      1.0.0
 *
 * @package    Link_shortener
 * @subpackage Link_shortener/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Link_shortener
 * @subpackage Link_shortener/includes
 * @author     Mojtaba Bahrami <mojtababahrami70@gmail.com>
 */
class Link_shortener
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Link_shortener_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * @var string custom post type name
     */
    private $cpt_name = 'short_link';

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('LINK_SHORTENER_VERSION'))
        {
            $this->version = LINK_SHORTENER_VERSION;
        }
        else
        {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'link_shortener';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Link_shortener_Loader. Orchestrates the hooks of the plugin.
     * - Link_shortener_i18n. Defines internationalization functionality.
     * - Link_shortener_Admin. Defines all hooks for the admin area.
     * - Link_shortener_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-link_shortener-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-link_shortener-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-link_shortener-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-link_shortener-public.php';

        $this->loader = new Link_shortener_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Link_shortener_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Link_shortener_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Link_shortener_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $this, 'init');
        $this->loader->add_action('add_meta_boxes', $this, 'add_meta_box');
        $this->loader->add_action('save_post', $this, 'save_url');
        $this->loader->add_action('get_header', $this, 'check_url');

        $this->loader->add_filter('manage_edit-' . $this->cpt_name . '_columns', $this, 'custom_column');
        $this->loader->add_action('manage_' . $this->cpt_name . '_posts_custom_column', $this, 'column_content', 10, 2);

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Link_shortener_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Link_shortener_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }


    public function init()
    {
        $args = array(
            'labels'             => array(
                'name'               => __('Short Urls', $this->plugin_name),
                'singular_name'      => __('Short Url', $this->plugin_name),
                'add_new'            => __('Add Url', $this->plugin_name),
                'add_new_item'       => __('Add New Url', $this->plugin_name),
                'edit_item'          => __('Edit Url', $this->plugin_name),
                'new_item'           => __('New Url', $this->plugin_name),
                'all_items'          => __('All Urls', $this->plugin_name),
                'view_item'          => __('View Url', $this->plugin_name),
                'search_items'       => __('Search Urls', $this->plugin_name),
                'not_found'          => __('No url found', $this->plugin_name),
                'not_found_in_trash' => __('No urls found in Trash', $this->plugin_name),
                'parent_item_colon'  => '',
                'menu_name'          => __('Url Shortener', $this->plugin_name),
            ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title')
        );
        register_post_type($this->cpt_name, $args);
    }

    public function add_meta_box()
    {
        add_meta_box("lsh-meta", __("Short URL", $this->plugin_name), array(
            $this,
            'meta_box_content'
        ), $this->cpt_name, "normal", "low");
    }

    public function meta_box_content()
    {
        global $post;
        wp_nonce_field('lsh_nonce', 'lsh_nonce');

        $url = get_post_meta($post->ID, '_lsh_url', true);

        if (!empty($_GET['lsh_message'])) :
            switch ((int)$_GET['lsh_message']) :
                case 1:
                    echo '<div class="updated"><p>' . __("URL is not valid", $this->plugin_name) . '</p></div>';
                    break;
            endswitch;
        endif
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label><?php echo __('Destination Url', $this->plugin_name) ?>:</label></th>
                <td><input name="url" type="text" value="<?php echo $url ?>" class="regular-text"/></td>
            </tr>
        </table>

        <?php
    }

    public function save_url($post_id)
    {
        global $post;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['lsh_nonce']) || !wp_verify_nonce($_POST['lsh_nonce'], 'lsh_nonce'))
            return;

        // if our current user can't edit this post, bail
        if (!current_user_can('edit_post'))
            return;

        //Also, if the url is invalid, add custom message
        if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['url']))
        {
            add_filter('redirect_post_location', array($this, 'invalid_url'));
        }

        update_post_meta($post_id, '_lsh_url', $_POST['url']);
    }

    public function invalid_url($location)
    {
        return $location . '&lsh_message=1';
    }

    public function check_url()
    {
        global $wp_query;
        if ($wp_query->query_vars['post_type'] != $this->cpt_name)
        {
            //This is a not a short link
            return false;
        }
        $post_id = get_the_ID();
        $metas = get_post_meta($post_id);
        $url = $metas['_lsh_url'][0];
        $views = isset($metas['_lsh_views']) ? $metas['_lsh_views'][0] : 0;

        update_post_meta($post_id, '_lsh_views', intval($views) + 1);
        wp_redirect($url);
    }

    /**
     * @param array of columns
     * @return mixed
     */
    public function custom_column($columns)
    {
        $out = array();
        $out['cb'] = $columns['cb'];
        $out['title'] = $columns['title'];

        $out['shorten_url'] = __('Shorten Url');
        $out['destination_id'] = __('Destination Url');
        $out['views'] = __('Views');

        $out['date'] = $columns['date'];

        return $out;
    }

    public function column_content($column_name, $post_id)
    {
        $post_metas = get_post_meta($post_id);
        $url = $post_metas['_lsh_url'][0];
        $views = isset($post_metas['_lsh_views']) ? $post_metas['_lsh_views'][0] : 0;
        switch ($column_name)
        {
            case 'shorten_url':
                echo sprintf('<input type="text" readonly onclick="this.select()" value="%s">', get_permalink());
                break;
            case 'destination_id':
                echo sprintf('<input type="text" readonly onclick="this.select()" value="%s">', $url);
                break;
            case 'views':
                echo sprintf(_n('%s view', '%s views', $views, $this->plugin_name), $views);
                break;
        }
    }
}
