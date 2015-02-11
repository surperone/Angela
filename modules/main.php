<?php
register_nav_menu( 'angela', 'Angela菜单' );

function angela_title( $title, $sep ) {
    global $paged, $page, $wp_query,$post;

    if ( is_feed() )
        return $title ;

    $title .= get_bloginfo( 'name', 'display' );

    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title = "$title $sep $site_description";

    if ( $paged >= 2 || $page >= 2 )
        $title = "第" .max( $paged, $page ) ."页 ". $sep . " " . $title;
    return $title;
}
add_filter( 'wp_title', 'angela_title', 10, 2 );

function angela_scripts() {

    // Load our main stylesheet.
    wp_enqueue_style( 'angela-style', get_stylesheet_uri() );
    wp_enqueue_script( 'global', get_bloginfo('template_directory') . '/static/js/global.js' , array(), '1.0.0', false);
    wp_localize_script( 'global', 'Angela', array(
        "comment" => get_bloginfo('template_directory') . '/static/js/comment.js',
        "ajax" => admin_url() . "admin-ajax.php",
        "loading" =>get_bloginfo('template_directory') . '/static/img/loading.gif',
        "error" => get_bloginfo('template_directory') . '/static/img/no.gif'
    ));
}
add_action( 'wp_enqueue_scripts', 'angela_scripts' );

function angela_get_background_image($post_id){
    if( has_post_thumbnail($post_id) ){
        $timthumb_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id),'full');
        $output = $timthumb_src[0];
    } else {
        $content = get_post_field('post_content', $post_id);
        $defaltthubmnail = get_template_directory_uri().'/images/default.jpg';
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if($n > 0){
            $output = $strResult[1][0];
        } else {
            $output = $defaltthubmnail;
        }
    }

    return $output;
}

function angela_is_has_image($post_id){
    static $has_image;
    global $post;
    if( has_post_thumbnail($post_id) ){
        $has_image = true;
    } else {
        $content = get_post_field('post_content', $post_id);
        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
        $n = count($strResult[1]);
        if($n > 0){
            $has_image = true;
        } else {
            $has_image = false;
        }
    }

    return $has_image;

}

add_action( 'wp_ajax_nopriv_angela_ajax_comment', 'angela_ajax_comment');
add_action( 'wp_ajax_angela_ajax_comment', 'angela_ajax_comment' );
function angela_ajax_comment(){
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
        global $wpdb;
        nocache_headers();
        $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
        $post = get_post($comment_post_ID);
        if ( empty($post->comment_status) ) {
            do_action('comment_id_not_found', $comment_post_ID);
            angela_ajax_error(__('Invalid comment status.')); // 將 exit 改為錯誤提示
        }

        // get_post_status() will get the parent status for attachments.
        $status = get_post_status($post);

        $status_obj = get_post_status_object($status);

        if ( !comments_open($comment_post_ID) ) {
            do_action('comment_closed', $comment_post_ID);
            angela_ajax_error(__('评论已关闭!')); // 將 wp_die 改為錯誤提示
        } elseif ( 'trash' == $status ) {
            do_action('comment_on_trash', $comment_post_ID);
            angela_ajax_error(__('Invalid comment status.')); // 將 exit 改為錯誤提示
        } elseif ( !$status_obj->public && !$status_obj->private ) {
            do_action('comment_on_draft', $comment_post_ID);
            angela_ajax_error(__('Invalid comment status.')); // 將 exit 改為錯誤提示
        } elseif ( post_password_required($comment_post_ID) ) {
            do_action('comment_on_password_protected', $comment_post_ID);
            angela_ajax_error(__('Password Protected')); // 將 exit 改為錯誤提示
        } else {
            do_action('pre_comment_on_post', $comment_post_ID);
        }

        $comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
        $comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
        $comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
        $comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;
        $user_id              = null;

        // If the user is logged in
        $user = wp_get_current_user();
        if ( $user->exists() ) {
            if ( empty( $user->display_name ) )
                $user->display_name=$user->user_login;
            $comment_author       = $wpdb->escape($user->display_name);
            $comment_author_email = $wpdb->escape($user->user_email);
            $comment_author_url   = $wpdb->escape($user->user_url);
            $user_id              = $user->ID;
            if ( current_user_can('unfiltered_html') ) {
                if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
                    kses_remove_filters(); // start with a clean slate
                    kses_init_filters(); // set up the filters
                }
            }
        } else {
            if ( get_option('comment_registration') || 'private' == $status )
                angela_ajax_error(__('你必须要登陆之后才可以发表评论.')); // 將 wp_die 改為錯誤提示
        }

        $comment_type = '';

        if ( get_option('require_name_email') && !$user->exists() ) {
            if ( 6 > strlen($comment_author_email) || '' == $comment_author )
                angela_ajax_error( __('请填写昵称和邮箱.') ); // 將 wp_die 改為錯誤提示
            elseif ( !is_email($comment_author_email))
                angela_ajax_error( __('请填写一个有效的邮箱.') ); // 將 wp_die 改為錯誤提示
        }

        if ( '' == $comment_content )
            angela_ajax_error( __('请输入评论.') ); // 將 wp_die 改為錯誤提示

        if ( !$user_id ) {

            // 增加: 檢查重覆評論功能
            $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
            if ( $comment_author_email ) $dupe .= "OR comment_author_email = '$comment_author_email' ";
            $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
            if ( $wpdb->get_var($dupe) ) {
                do_action( 'comment_duplicate_trigger', $comment_post_ID );
                angela_ajax_error(__('您已经发布过一条相同的评论!'));
            }


            // 增加: 檢查評論太快功能
            if ( $lasttime = $wpdb->get_var( $wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author) ) ) {
                $time_lastcomment = mysql2date('U', $lasttime, false);
                $time_newcomment  = mysql2date('U', current_time('mysql', 1), false);
                $flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
                if ( $flood_die ) {
                    angela_ajax_error(__('请过一会再发表评论.'));
                }
            }

        }

        $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

        $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_id');

        // 新建評論
        $comment_id = wp_new_comment( $commentdata );

        $comment = get_comment($comment_id);
        do_action('set_comment_cookies', $comment, $user);

        $comment_depth = 1;   //为评论的 class 属性准备的
        $tmp_c = $comment;
        while($tmp_c->comment_parent != 0){
            $comment_depth++;
            $tmp_c = get_comment($tmp_c->comment_parent);
        }

        //此处非常必要，无此处下面的评论无法输出 by mufeng
        $GLOBALS['comment'] = $comment;

        ?>
        <li id="comment-<?php comment_ID() ?>" <?php comment_class('commenttips',$comment_id,$comment_post_ID); ?> >
        <div class="comment-body">
            <div class="comment-avatar">
                <?php echo get_avatar( get_comment_author_email(), '40'); ?>
            </div>
            <div class="comment-meta">
                <span class="comment-id"><?php comment_author_link();?></span>
                <span class="comment-time">评论于<?php echo angela_time_ago(abs(strtotime($comment->comment_date_gmt . "GMT")),true); ?></span>
            </div>
            <div class="comment-text">
                <?php if ($comment->comment_approved == '0') : ?>
                    <?php _e('<p class="comment-warning">Your comment is awaiting moderation.</p>') ?>
                <?php endif; ?>
                <?php comment_text() ?><?php //edit_comment_link(' <编辑> '); ?>
            </div>
        </div>
        <?php
        die(); //以上是評論式樣, 不含 "回覆". 要用你模板的式樣 copy 覆蓋.
    }
}

/**
 * Ajax 错误提示
 */
function angela_ajax_error($text) {
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $text;
    exit;
}

function angela_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
<li id="comment-<?php comment_ID() ?>" <?php comment_class('commenttips',$comment_id,$comment_post_ID); ?> >
    <div class="comment-body">
        <div class="comment-avatar">
            <?php echo get_avatar( get_comment_author_email(), '40'); ?>
        </div>
        <div class="comment-meta">
            <span class="comment-id"><?php comment_author_link();?></span>
            <span class="comment-time"><?php echo angela_time_ago(abs(strtotime($comment->comment_date_gmt . "GMT")),true); ?></span>
            <span class="reply"><?php comment_reply_link(array_merge( $args, array('reply_text' => '回复TA','depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
        </div>
        <div class="comment-text">
            <?php if ($comment->comment_approved == '0') : ?>
                <?php _e('<p class="comment-warning">Your comment is awaiting moderation.</p>') ?>
            <?php endif; ?>
            <?php comment_text() ?><?php //edit_comment_link(' <编辑> '); ?>
        </div>
    </div>
<?php
}
function angela_time_ago($older_date, $comment_date = false) {
    $chunks = array(
        array(86400 , '天前'),
        array(3600 , '小时前'),
        array(60 , '分钟前'),
        array(1 , '秒前'),
    );
    $newer_date = time();
    $since = abs($newer_date - $older_date);
    if($since < 2592000){
        for ($i = 0, $j = count($chunks); $i < $j; $i++){
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) break;
        }
        $output = $count.$name;
    }else{
        $output = !$comment_date ? (date('Y-m-j', $older_date)) : (date('Y-m-j', $older_date));
    }
    return $output;
}
function comment_mail_notify($comment_id) {
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;

    $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
    $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
    $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";

    if (($parent_id != '') && ($spam_confirmed != 'spam')) {
        $to = trim(get_comment($parent_id)->comment_author_email);
        $subject = '你在 [' . get_option("blogname") . '] 的留言有了新回复';
        $message = '
				<div style="background-color:#eef2fa; border:1px solid #d8e3e8; color:#111; padding:0 15px; -moz-border-radius:5px; -webkit-border-radius:5px; -khtml-border-radius:5px; border-radius:5px;">
				<p><strong>' . trim(get_comment($parent_id)->comment_author) . ', 你好!</strong></p>
				<p><strong>您曾在《' . get_the_title($comment->comment_post_ID) . '》的留言为:</strong><br />'
            . trim(get_comment($parent_id)->comment_content) . '</p>
				<p><strong>' . trim($comment->comment_author) . ' 给你的回复是:</strong><br />'
            . trim($comment->comment_content) . '<br /></p>
				<p>你可以点击此链接 <a href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整内容</a></p><br />
				<p>欢迎再次来访<a href="' . get_option('home') . '">' . get_option('blogname') . '</a></p>
				<p>(此邮件为系统自动发送，请勿直接回复.)</p>
				</div>';

        wp_mail( $to, $subject, $message, $headers );
    }
}
add_action('comment_post', 'comment_mail_notify');

function get_ssl_avatar($avatar) {
    $avatar = str_replace(array("www.gravatar.com","0.gravatar.com","1.gravatar.com","2.gravatar.com"),"secure.gravatar.com",$avatar);
    return $avatar;
}
add_filter('get_avatar', 'get_ssl_avatar');

function tg_get_adjacent_posts_link() {
    global $paged, $wp_query;

    if ( !$max_page )
        $max_page = $wp_query->max_num_pages;
    if ( $max_page < 2)
        return;
    if ( !$paged )
        $paged = 1;
    $output = '<nav class="v-textAlignCenter fontSmooth posts-load-btn">';

    $nextpage = intval($paged) + 1;
    if ( !$max_page || $max_page >= $nextpage )
        $next_post = get_pagenum_link($nextpage);
    $previouspage = intval($paged) - 1;
    if ( $previouspage < 1 )
        $previouspage = 1;
    $previous_post =  get_pagenum_link($previouspage);
    if ( $paged > 1 ) {
        $output .= '<a class="posts-load-prompt"  href="' . $previous_post . '" data-title="Page '.$paged.'">上一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">上一页</span>';

    }

    $output .= '<span class="posts-load-num">'.$paged.' / '. $max_page .'</span>';

    if ( $nextpage <= $max_page ) {
        $output .= '<a class="posts-load-prompt" data-title="Page '.$nextpage .'" href="' . $next_post . '">下一页</a>';
    } else {
        $output .= '<span class="posts-load-disabled">下一页</span>';

    }
    $output .= '</nav>';

    return $output;
}