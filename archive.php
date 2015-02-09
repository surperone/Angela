<?php get_header();?>
    <div id="primary" class="angelaContainer angelaBody">
        <header class="angelaArchive-header">
            <h1 class="page-title">
                <?php
                if ( is_category() ) :
                    single_cat_title();

                elseif ( is_tag() ) :
                    single_tag_title();


                elseif ( is_month() ) :
                    printf( esc_html__( 'Month: %s', 'twentyfifteen' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'twentyfifteen' ) ) );

                elseif ( is_tax( 'column' ) ) :
                    esc_html_e( 'column', 'twentyfifteen' );

                else :
                    //esc_html_e( 'Archives', 'twentyfifteen' );

                endif;
                ?>
            </h1>
            <?php
            // Show an optional term description.
            $term_description = term_description();
            if ( ! empty( $term_description ) ) :
                printf( '<div class="taxonomy-description">%s</div>', $term_description );
            endif;
            ?>
        </header>
        <div class="postlists">
            <?php if ( have_posts() ) : ?>
            <?php
            // Start the loop.
            while ( have_posts() ) : the_post();
                ?>
                <?php	get_template_part( 'content', 'home' );?>
            <?php endwhile;?>
        </div>
        <?php echo tg_get_adjacent_posts_link();?>
        <?php endif;?>

    </div><!-- .content-area -->
<?php get_footer();?>