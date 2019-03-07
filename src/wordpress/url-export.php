<?php

include "wp-load.php";

$array = array(
    'post_type' => array('post', 'page'),
    'posts_per_page' => -1,
    'post_status' => 'publish'
);

$posts = new WP_Query($array);
$posts = $posts->posts;
/*
global $wpdb;
$posts = $wpdb->get_results("
    SELECT ID,post_type,post_title
    FROM {$wpdb->posts}
    WHERE post_status<>'auto-draft' AND post_type NOT IN ('revision','nav_menu_item')
");
*/

header('Content-type:text/plain');
foreach($posts as $post) {
    switch ($post->post_type) {
        case 'revision':
        case 'nav_menu_item':
            break;
        case 'page':
            $permalink = get_page_link($post->ID);
            break;
        case 'post':
            $permalink = get_permalink($post->ID);
            break;
        case 'attachment':
            $permalink = get_attachment_link($post->ID);
            break;
        default:
            $permalink = get_post_permalink($post->ID);
            break;
    }

    if (str_replace(home_url(), '', $permalink) == '/') {
        $link = '/all-things-sailing-blog';
        echo "\nrewrite ^{$link}$ https://yacht-charter-sailing-blog.sailchecker.com/ permanent;";
    } else {
        $link = str_replace(home_url(), '', $permalink);
        echo "\nrewrite ^{$link}$ https://yacht-charter-sailing-blog.sailchecker.com{$link} permanent;";
    }
}
