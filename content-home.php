<?php
/**
 * The template for displaying home content
 *
 * Used for both single and index/archive/search.
 *
 * @package Bigfa
 * @since Angela 1.0
 */
?>
<article class="angela--post-home">
    <?php if(angela_is_has_image($post->ID)) :?>
        <a class="block-image" href="<?php the_permalink();?>" style="background-image:url(<?php echo angela_get_background_image($post->ID);?>)"></a>
    <?php endif;?>
    <h2 class="angela-title"><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></h2>
    <div class="block-snippet block-snippet--subtitle"><?php echo mb_strimwidth(strip_shortcodes(strip_tags($post->post_content)), 0, 120,"...");?></div>
    <div class="v-clearfix block-postMetaWrap">
        <div class="block-postMeta">
            <div class="postMetaInline-avatar">
                <?php echo get_avatar( get_the_author_meta( 'user_email' ), 40 );?>
            </div>
            <div class="postMetaInline-feedSummary">
                In
                <?php the_category(",")?>
                , by
                <span class="link--accent"><?php the_author();?></span>
<span class="postMetaInline postMetaInline--supplemental">
<?php echo angela_time_ago(abs(get_the_date('U')),true);?>
            </div>
        </div>
    </div>
</article>