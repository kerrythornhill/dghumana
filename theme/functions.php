<?php
if (!defined('ABSPATH')) { exit; }
function humana_config() {
    static $config = null;
    if ($config === null) { $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true); }
    return $config;
}
function humana_is_draft() { return get_option('wpvibe_draft_theme') === get_stylesheet(); }
function humana_key() {
    $pages = humana_config()['pages'];
    if (humana_is_draft() && isset($_GET['site_view'])) {
        $key = sanitize_key(wp_unslash($_GET['site_view']));
        if (isset($pages[$key])) { return $key; }
    }
    if (is_front_page()) { return 'home'; }
    $key = get_post_meta(get_queried_object_id(), '_humana_route', true);
    return isset($pages[$key]) ? $key : '';
}
function humana_record($key) {
    if (!isset(humana_config()['pages'][$key])) { return null; }
    $posts = get_posts(array('post_type'=>'page','post_status'=>humana_is_draft() ? array('publish','draft','pending') : 'publish','numberposts'=>1,'meta_key'=>'_humana_route','meta_value'=>$key,'orderby'=>'ID','order'=>'ASC'));
    return $posts ? $posts[0] : null;
}
function humana_url($key) {
    if (!isset(humana_config()['pages'][$key])) { return home_url('/'); }
    if (humana_is_draft()) {
        $args = array('site_view'=>$key);
        if (isset($_GET['wpvibe_preview'])) { $args['wpvibe_preview'] = sanitize_text_field(wp_unslash($_GET['wpvibe_preview'])); }
        return add_query_arg($args, home_url('/'));
    }
    if ($key === 'home') { return home_url('/'); }
    $post = humana_record($key);
    return $post ? get_permalink($post) : home_url('/' . $key . '/');
}
function humana_tokens($html) {
    return preg_replace_callback('/\[\[(asset:)?([a-zA-Z0-9_.\/-]+)\]\]/', function($m) {
        if (!empty($m[1])) {
            if (strpos($m[2], '..') !== false) { return ''; }
            return esc_url(get_template_directory_uri() . '/assets/' . $m[2]);
        }
        return esc_url(humana_url($m[2]));
    }, $html);
}
function humana_partial($name) {
    if (!in_array($name, array('header','footer'), true)) { return; }
    $html = file_get_contents(__DIR__ . '/partials/' . $name . '.html');
    $key = humana_key();
    if ($key) { $html = str_replace('data-route="' . $key . '"', 'data-route="' . $key . '" aria-current="page"', $html); }
    echo humana_tokens($html); // Trusted theme HTML; token URLs are escaped above.
}
function humana_render($key) {
    if (!isset(humana_config()['pages'][$key])) { return; }
    $post = humana_record($key);
    if ($post && trim($post->post_content) !== '') {
        echo humana_tokens(apply_filters('the_content', $post->post_content));
    } else {
        echo humana_tokens(file_get_contents(__DIR__ . '/content/' . $key . '.html'));
    }
}
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form','gallery','caption','style','script'));
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('editor.css');
});
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('humana-design', get_template_directory_uri() . '/assets/site.css', array(), filemtime(__DIR__ . '/assets/site.css'));
});
add_filter('body_class', function($classes) { $classes[] = humana_config()['body_class']; return $classes; });
add_filter('pre_get_document_title', function($title) {
    $key = humana_key();
    return $key ? humana_config()['pages'][$key]['title'] : $title;
});
add_action('wp_head', function() {
    $key = humana_key();
    if (!$key) { return; }
    $page = humana_config()['pages'][$key];
    echo '<meta name="description" content="' . esc_attr($page['description']) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($page['title']) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($page['description']) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    if (!humana_is_draft()) { echo '<meta property="og:url" content="' . esc_url(humana_url($key)) . '">' . "\n"; }
    if (!has_site_icon()) { echo '<link rel="icon" href="' . esc_url(get_template_directory_uri() . '/assets/favicon.svg') . '" type="image/svg+xml">' . "\n"; }
}, 5);
add_filter('wp_robots', function($robots) {
    if (humana_is_draft()) { $robots['noindex'] = true; $robots['nofollow'] = true; unset($robots['index']); }
    return $robots;
});
add_action('template_redirect', function() {
    if (humana_is_draft()) { nocache_headers(); header('X-Robots-Tag: noindex, nofollow', true); }
});
