<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package Bigfa
 * @since Anglea 1.0
 */
?>
<footer id="colophon" class="angelaContainer angelaFooter" role="contentinfo">
    <div class="site-info">
        <a href="http://fatesinger.com/74850">Angela Theme</a>
    </div><!-- .site-info -->
</footer><!-- .site-footer -->
</div>
<div class="v-hide"></div><!-- 统计代码 -->
<?php wp_footer(); ?>
<?php
define('APPID','');//yourAppID
define('APPSECRET','');//yourAppSecret
if( APPID && APPSECRET ){
    $jssdk = new JSSDK(APPID, APPSECRET);
    $signPackage = $jssdk->GetSignPackage();
    if(is_single()){
        global $post;
        $title = get_the_title();
        $url = get_permalink();
        $image = angela_get_background_image($post->ID);
        $des = get_the_excerpt();
    } else {
        $title = get_bloginfo( 'name', 'display' );
        $url = home_url();
        $image = '';
        $des = get_bloginfo( 'description', 'display' );
    }
    ?>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <?php echo $signPackage["timestamp"];?>,
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                'onMenuShareTimeline','onMenuShareAppMessage'
            ]
        });
        wx.ready(function () {
            wx.onMenuShareAppMessage({
                title: '<?php echo $title;?>', // 分享标题
                desc: '<?php echo $des;?>', // 分享描述
                link: '<?php echo $url;?>', // 分享链接
                imgUrl: '<?php echo $image;?>', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {

                }
            });
            wx.onMenuShareTimeline({
                title: '<?php echo $title;?>', // 分享标题
                link: '<?php echo $url;?>', // 分享链接
                imgUrl: '<?php echo $image;?>', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    </script>
<?php } ?>
</body>
</html>