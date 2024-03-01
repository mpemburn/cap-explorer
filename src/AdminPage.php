<?php

namespace CapExplorer;

class AdminPage
{
    private static $instance = null;
    protected Explore $explore;

    // Make this a singleton by preventing direct instantiation
    private function __construct()
    {
        $this->explore = new Explore();

        $this->addActions();
    }

    public static function boot()
    {
        if (!self::$instance) {
            self::$instance = new AdminPage();
        }

        return self::$instance;
    }
    
    protected function addActions(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_menu', [$this, 'addMenuPage']);
        // For WordPress multisites
        add_action('network_admin_menu', [$this, 'addMenuPage']);
        add_action('wp_ajax_nopriv_search_blogs', [$this, 'searchBlogs']);
        add_action('wp_ajax_search_blogs', [$this, 'searchBlogs']);
        add_action('wp_ajax_nopriv_search_users', [$this, 'searchUsers']);
        add_action('wp_ajax_search_users', [$this, 'searchUsers']);
        add_action('wp_ajax_nopriv_show_caps', [$this, 'showCapabilities']);
        add_action('wp_ajax_show_caps', [$this, 'showCapabilities']);
    }
    
    public function addMenuPage(): void
    {
        add_menu_page(
            __('Cap Explorer', 'uri'),
            'Cap Explorer',
            'switch_themes',
            'cap-explorer',
            [$this, 'showAdminPage'],
            'dashicons-admin-tools',
            90
        );
    }
    public function enqueue(): void
    {
        // Bail if we're not on this admin page
        if (! isset($_REQUEST['page']) || $_REQUEST['page'] !== 'cap-explorer') {
            return;
        }

        $jsFile = plugin_dir_path(__FILE__) . 'js/cap-explorer.js';
        $cssFile = plugin_dir_path(__FILE__) . 'css/cap-explorer.css';
        $jsUrl = plugins_url('/js/cap-explorer.js', __FILE__);
        $cssUrl = plugins_url('/css/cap-explorer.css', __FILE__);

        wp_enqueue_script('wp-util');
        wp_register_script('cap-explorer', $jsUrl, [], filemtime($jsFile), true);
        wp_enqueue_script('cap-explorer');
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        wp_enqueue_style('cap-explorer', $cssUrl, [], filemtime($cssFile));
    }

    public function showAdminPage(): void
    {
        $this->explore->listUsers();
        echo '<div style="max-width: 90%;">';
        echo '<h1>Capability Explorer</h1>';
        echo '<div id="controls">';
        echo $this->explore->buildBlogsDropdown();
        echo $this->explore->buildRolesDropdown();
        echo $this->explore->buildUsersDropdown();
        echo '    <button id="submit" class="button button-primary">Submit</button>';
        echo '    <button id="clear" class="button button-primary">Clear</button>';
        echo '    <img id="loading"
                    src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" alt="" width="24"
                    height="24">';
        echo '</div>';
        echo '<div id="results">';
        echo '    <div id="query"></div>';
        echo '    <div id="capabilities"></div>';
        echo '</div>';
    }

    public function searchBlogs()
    {
        $results = null;
        $blogCount = 0;

        $search = $_REQUEST['search'];

        if ($search && strlen($search) >= 3) {
            $results = $this->explore->buildBlogOptions($search);
            $blogCount = $this->explore->getBlogCount();
        }

        wp_send_json_success([
            'results' => $results,
            'count' => $blogCount
        ]);

        die();
    }
    
    public function searchUsers()
    {
        $results = null;
        $userCount = 0;

        $search = $_REQUEST['search'];

        if ($search && strlen($search) >= 3) {
            $results = $this->explore->buildUsersOptions($search);
            $userCount = $this->explore->getUserCount();
        }

        wp_send_json_success([
            'results' => $results,
            'count' => $userCount
        ]);

        die();
    }

    public function showCapabilities()
    {
        $blogId = $_REQUEST['blog_id'];
        $role = $_REQUEST['role'];

        $current = get_current_blog_id();
        switch_to_blog($blogId);
        $table = $this->explore->buildCapabilitiesTable($blogId, $role);
        switch_to_blog($current);

        wp_send_json_success(['results' => $table]);

        die();
    }
}
