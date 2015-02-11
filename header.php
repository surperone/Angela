<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package Bigfa
 * @since Angela 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="initial-scale=1.0,user-scalable=no,minimal-ui">
    <title><?php
        wp_title( '-', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
    <![endif]-->
    <script>(function(){document.documentElement.className='js'})();</script>
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
<div id="canvas-menu">
    <div class="menu-inner angelaContainer">
        <?php wp_nav_menu( array( 'theme_location' => 'angela','menu_class'=>'menu','container'=>'ul')); ?>
    </div>
</div>
<div id="surface-content">
    <header class="angela-metabar">
        <div class="angelaContainer angelaHeader v-overflowHidden">
            <div class="v-floatLeft"><i class="icon-paragraphleft iconfont"></i></div><div class="v-floatRight"><a href="/"><?php echo get_bloginfo( 'name', 'display' );?></a></div>
        </div>
    </header>