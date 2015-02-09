<div id="comments">
		<div id="comments-count"><span class="icon-comment"></span>目前共 <?php comments_number('0 条评论', '1 条评论', '% 条评论' );?></div>
		<?php if($comments) : ?><!--如果有评论-->
		
			<?php if ( function_exists('wp_list_comments') ) : ?><!--评论列表-->
				<div id="commentlist">
					<ul class="commentlist"><?php wp_list_comments('type=comment&callback=angela_comment&max_depth=10000'); ?></ul>
					<nav class="commentnav"><?php paginate_comments_links('prev_text=上一页&next_text=下一页');?></nav>
				</div>
			<?php endif; ?>
			
		<?php endif; ?>
	
	
		<?php if(comments_open()) : ?>
			
			<div id="respond">
			
					<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform" name="comm_frm">
						
						<?php if($user_ID) : ?>
						
							<!--已登录-->
							<div class="welcomediv">
								<?php echo get_avatar( get_the_author_email(), 28 ); ?>
								<?php _e('当前登陆用户：','mugee'); ?><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php" class="profile"><?php echo $user_identity; ?></a>&#160;&#47;&#160;<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" class="logout">登出？</a>
							</div>
							
							<div id="cancel_comment_reply"><?php cancel_comment_reply_link('取消回复') ?></div>
							
						<?php else : ?>
							
							
							<div id="cancel_comment_reply"><?php cancel_comment_reply_link('取消回复') ?></div>
							
							<section id="comboxinfo">
								<div class="cominfodiv cominfodiv-author"><input type="text" name="author" id="author" placeholder="昵 称" value="<?php echo $comment_author; ?>" tabindex="1" /></div>
								<div class="cominfodiv cominfodiv-email"><input type="email" name="email" id="email" placeholder="邮 箱" value="<?php echo $comment_author_email; ?>" tabindex="2" /></div>
								<div class="cominfodiv cominfodiv-url"><input type="text" name="url" id="url" placeholder="网 址" value="<?php echo $comment_author_url; ?>" tabindex="3" /></div>
							</section>
						<?php endif; ?>
						
						<div id="text-area">
							<textarea name="comment" placeholder="输入评论内容......" id="comment" rows="10" tabindex="4"></textarea>
						</div>
					
						<div class="submitdiv clearfix">
							<div id="comment-tips"></div>
							<div class="submitcom"><input name="submit" type="submit" id="submit" tabindex="5" value="提交评论" /><?php comment_id_fields(); ?></div>
						</div>
						<?php do_action('comment_form', $post->ID); ?>
						
					</form>
					
			</div><!--end respond-->
			
		<?php endif; ?>

</div><!--end comments-->