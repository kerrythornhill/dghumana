<?php
// Run through: wp eval-file /absolute/path/to/tools/import-drafts.php
// This creates new DRAFT pages only. It does not activate a theme, publish, or change options.
if (!defined('WP_CLI') || !WP_CLI) { exit('WP-CLI only.'); }
$root = dirname(__DIR__);
$config = json_decode(file_get_contents($root . '/theme/config.json'), true);
if (rtrim(home_url(), '/') !== rtrim($config['domain'], '/')) { WP_CLI::error('Wrong site: the configured domain does not match home_url().'); }
foreach ($config['pages'] as $key => $page) {
    $existing = get_posts(array('post_type'=>'page','post_status'=>'any','numberposts'=>1,'meta_key'=>'_humana_route','meta_value'=>$key));
    if ($existing) { WP_CLI::log('Preserved existing ' . $key . ' page ' . $existing[0]->ID); continue; }
    $slug = $key === 'home' ? 'horizon-home' : $key;
    if (get_page_by_path($slug)) { WP_CLI::error('Page slug already exists: ' . $slug . '. Resolve the route before importing; no existing content was overwritten.'); }
    $id = wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_name'=>$slug,'post_title'=>$page['label'],'post_content'=>file_get_contents($root . '/content/' . $key . '.html'),'comment_status'=>'closed','ping_status'=>'closed','meta_input'=>array('_humana_route'=>$key)), true);
    if (is_wp_error($id)) { WP_CLI::error($id->get_error_message()); }
    WP_CLI::success('Created draft ' . $key . ': ' . $id);
}
