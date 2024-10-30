<?php

  /**
   * Plugin Name: Jinx Breadcrumbs
   * Plugin URI: https://wordpress.org/plugins/jinx-breadcrumbs/
   * Description: Simple yet powerful breadcrumbs for geeks
   * Version: 0.2.11
   * Author: SquareFlower Websolutions (Lukas Rydygel) <hallo@squareflower.de>
   * Author URI: https://squareflower.de
   * Text Domain: jinx
   */

  require_once(__DIR__.'/src/Bread.php');
  require_once(__DIR__.'/src/Crumb.php');

  function jinx_breadcrumbs(array $args = []) {
    echo (new Jinx\Bread($args))->crumbs();
  }