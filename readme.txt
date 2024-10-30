=== Jinx-Breadcrumbs ===
Contributors: Lugat
Tags: breadcrumbs, seo
Requires at least: 5.0
Tested up to: 5.5.1
Requires PHP: 7.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple yet powerful breadcrumbs for geeks

== Description ==

The plugin allows you to render breadcrumbs and configurate them with filters.

== Installation ==

1. Unzip the downloaded package
2. Upload `jinx-block-renderer` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

Use the function 'jinx_breadcrumbs' to render the breadcrumbs where you want them to be. You may overwrite the default arguments by passing an array to the function.

`<?php

  if (function_exists('jinx_breadcrumbs')) :
  
    jinx_breadcrumbs([
      // default args
      'home' => __('Home', 'jinx-breadcrumbs'),
      'search' => __('Search: "%s"', 'jinx-breadcrumbs'),
      '404' => __('Error 404', 'jinx-breadcrumbs'),
      'author' => __('Author: %s', 'jinx-breadcrumbs'),
      'year' => 'Y',
      'month' => 'F',
      'day' => 'd',
      'before' => '<nav aria-label="breadcrumb"><ol>',
      'after' => '</ol></nav>',
      'before_item' => '<li%s>',
      'after_item' => '</li>',
    ]);
  
  endif;

?>`

You may also use the filter 'jinx_jinx_breadcrumbs' to overwrite them.

`<?php

  add_filter('jinx_breadcrumbs', function($args) {
    
    return array_merge($args, [
      'home' => __('Start', 'cca'),
      'search' => __('Your searched for "%s"', 'cca'),
    ]);
    
  });

?>`

The plugin will automatically try to find the correct archive pages by using the rewrite slug of custom taxonomies and post types.

If you may want to change this behavior, you may use some filters to manipulate the archive page.

If you return NULL, the archive page will be removed.

`<?php

  // filters the archive page, passing the PID, type ('taxonomy' or 'post_type') and name (eg. 'video')
  add_filter('jinx_breadcrumbs_archive', function($pid, $type, $name) {

    return $pid;

  }, 10, 3);

  // filters the archive page, passing the PID and name (eg. 'video')
  // the type is part of the filter (eg. 'jinx_breadcrumbs_archive_taxonomy')
  add_filter('jinx_breadcrumbs_archive_{type}', function($pid, $name) {

    return $pid;

  }, 10, 2);

  // filters the archive page, passing the PID
  // the type and name are part of the filter (eg. 'jinx_breadcrumbs_archive_post_type_video')
  add_filter('jinx_breadcrumbs_archive_{type}_{name}', function($pid) {

    return $pid;

  }, 10, 1);

?>`