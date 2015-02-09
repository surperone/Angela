<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package Bigfa
 * @since Angela 1.0
 */

get_header(); ?>
    <div id="primary" class="angelaContainer angelaBody">
        <div class="postlists">
            <?php if ( have_posts() ) : ?>
            <?php
            // Start the loop.
            while ( have_posts() ) : the_post();
                ?>
                <?php	get_template_part( 'content', 'home' );?>
            <?php endwhile;?>

        </div><?php echo tg_get_adjacent_posts_link();?>
        <?php endif; ?>
    </div><!-- .content-area -->

<?php get_footer();?>