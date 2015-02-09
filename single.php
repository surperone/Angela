<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package Bigfa
 * @since Angela 1.0
 */

get_header(); ?>

    <div id="primary" class="angelaContainer angelaBody">
        <div>
            <?php if ( have_posts() ) : ?>

            <?php
            // Start the loop.
            while ( have_posts() ) : the_post();
                ?>

                <?php get_template_part( 'content', 'single' );?>
                <?php comments_template(); ?>
            <?php endwhile;?>
        </div>
        <?php
        endif;
        ?>

    </div><!-- .content-area -->

<?php get_footer();?>