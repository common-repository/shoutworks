<?php
add_filter( 'wp_get_attachment_image_src', 'remove_query_from_image' );

function remove_query_from_image( $image ){
	$image[0] = substr($image[0], 0, strrpos( $image[0], "?"));
	return $image;
}
/**
 * Template Name: Custom RSS Template - shoutworks
*/

/* $uri = $_SERVER['REQUEST_URI'];
$tmp = explode('/', $uri);
$param = end($tmp);
echo $param;
die; */

$display_quote = get_option( 'swks_display_quote' );
$display_flash = get_option( 'swks_display_flash' );
$display_blog = get_option( 'swks_display_blog' );
$display_deal = get_option( 'swks_display_deal' );
$pageCount = get_option( 'swks_blog_number' );
$postCount = get_option( 'swks_blog_number' );
$dealsCount = get_option( 'swks_deal_number' );
$quoteCount = get_option( 'swks_quote_number' );
$flashbriefCount = get_option( 'swks_flash_number' );

/* Pages */

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
        <?php do_action('rss2_ns'); ?>>

<channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('swks_rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

        <?php do_action('rss2_head'); 
		if( isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="siteinfo" ){

			if( !empty( get_option('swks_alex_site_intro') ) ){ 
				$alex_site_intro = get_option('swks_alex_site_intro');
			}else{
				$alex_site_intro = "Every website has a purpose... We are always looking for ways to inform, delight, and surprise you so your day can be just a little bit better.";
			}
		?>	

			<item><title><?php echo esc_html($alex_site_intro); ?></title></item>
		<?php	
		}

		if( isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="supportemail" ){
		?>	
			<item><title><?php echo get_option('swks_voice_support_email'); ?></title></item>

		<?php	
		}

		if( isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="pages" ){
		?>	

        <pages>
		<?php 
		$pages = query_posts('post_type=page&post_status=publish&order=DESC&posts_per_page=' . $pageCount);

		while(have_posts()) : the_post(); ?>
            <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <dc:creator><?php the_author(); ?></dc:creator>
                    <guid isPermaLink="false"><?php the_guid(); ?></guid>

					<?php
					$content = get_the_content();
					if( !empty($content)){
					$content = wp_strip_all_tags( apply_filters('the_content', $content));
					?>

                    <description><![CDATA[<?php echo $content; ?>]]></description>
					<?php } ?>
                    <rssDescription><![CDATA[<?php the_excerpt_rss() ?>]]></rssDescription>

					<?php
					if ( has_post_thumbnail( get_the_ID() ) ) {
						$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
						echo("<featuredImg>{$featured_img_url}</featuredImg>");
					}

					$tags = get_the_tags();
					if($tags) {
						echo "<tags>";
						foreach ($tags as $tag){
							echo "<tag><name>". $tag->name ."</name><link>". get_tag_link($tag->term_id) ."</link></tag>";
						}
						echo "</tags>";
					}
					?>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
            </item>
        <?php endwhile; ?>
		</pages>

		<?php 
		}
		wp_reset_query();

		if( $display_blog == "yes" && isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="blogs" ){
		$posts = query_posts('post_type=post&post_status=publish&order=DESC&posts_per_page=' . $postCount);
		?>

		<posts>
		<?php while(have_posts()) : the_post(); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <link><?php the_permalink_rss(); ?></link>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <dc:creator><?php the_author(); ?></dc:creator>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>

				<?php
				$content = get_the_content();
				if( !empty($content)){
				$content = wp_strip_all_tags( apply_filters('the_content', $content));
				?>

                <description><![CDATA[<?php echo $content; ?>]]></description>
				<?php } ?>
                <rssDescription><![CDATA[<?php the_excerpt_rss() ?>]]></rssDescription>
             
				<?php
				if ( has_post_thumbnail( get_the_ID() ) ) {
					$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
					echo("<featuredImg>{$featured_img_url}</featuredImg>");
				}			

				$tags = get_the_tags();
				if($tags) {
					echo "<tags>";
					foreach ($tags as $tag){
						echo "<tag><name>". $tag->name ."</name><link>". get_tag_link($tag->term_id) ."</link></tag>";
					}
					echo "</tags>";
				}			

				?>
                <?php rss_enclosure(); ?>
                <?php do_action('rss2_item'); ?>
            </item>
        <?php endwhile; ?>
		</posts>

		<?php 
		}
		wp_reset_query();	

		if( $display_deal == "yes" && isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="deals" ){
		$deals = query_posts('post_type=deals&post_status=publish&order=DESC&posts_per_page=' . $dealsCount);
		?>
		<dealsoftheday>

		<?php 
		if( have_posts() ){
			while(have_posts()) : the_post(); ?>
                <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <dc:creator><?php the_author(); ?></dc:creator>
                    <guid isPermaLink="false"><?php the_guid(); ?></guid>

					<?php
					$content = get_the_content();
					if( !empty($content)){
					$content = wp_strip_all_tags( apply_filters('the_content', $content));
					?>

                    <description><![CDATA[<?php echo $content; ?>]]></description>
					<?php } ?>
                    <rssDescription><![CDATA[<?php the_excerpt_rss() ?>]]></rssDescription>

					<?php
					if ( has_post_thumbnail( get_the_ID() ) ) {
						$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
						echo("<featuredImg>{$featured_img_url}</featuredImg>");
					}			

					$tags = get_the_tags();		
					if($tags) {
						echo "<tags>";
						foreach ($tags as $tag){
							echo "<tag><name>". $tag->name ."</name><link>". get_tag_link($tag->term_id) ."</link></tag>";
						}
						echo "</tags>";
					}			

					?>
	                <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
        <?php endwhile; 
		}else{
		?>
			<item>
				<title>Deal of the day</title>
				<link><?php echo site_url();?></link>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', time('Y-m-d H:i:s', true), false); ?></pubDate>
				<dc:creator>admin</dc:creator>
				<guid isPermaLink="false"><?php echo site_url();?></guid>
				<description>
				<![CDATA[
				Every morning can be the beginning of a new day of experiences, fun, and discovery. Check back here for our special deals to help you make the most of your day.
				]]>
				</description>
				<rssDescription>
				<![CDATA[
				Every morning can be the beginning of a new day of experiences, fun, and discovery. Check back here for our special deals to help you make the most of your day.
				]]>
				</rssDescription>	

				<featuredImg><?php echo esc_url( plugins_url( 'images/nycsunrise.jpg'.$image_name, __FILE__ ) );?></featuredImg>						
			</item>
		<?php	
		}
		?>
		</dealsoftheday>

		<?php 
		}
		wp_reset_query();

		if( $display_quote == "yes" && isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="quotes" ){
		$quotes = query_posts('post_type=quotes&post_status=publish&order=DESC&posts_per_page=' . $quoteCount);
		?>
		<quotesoftheday>

		<?php 
		if( have_posts() ){
			while(have_posts()) : the_post(); ?>
                <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <dc:creator><?php the_author(); ?></dc:creator>
                    <guid isPermaLink="false"><?php the_guid(); ?></guid>

					<?php
					$content = get_the_content();
					if( !empty($content)){
					$content = wp_strip_all_tags( apply_filters('the_content', $content));
					?>

                    <description><![CDATA[<?php echo $content; ?>]]></description>
					<?php } ?>
                    <rssDescription><![CDATA[<?php the_excerpt_rss() ?>]]></rssDescription>
                     
					<?php
					if ( has_post_thumbnail( get_the_ID() ) ) {
						$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
						echo("<featuredImg>{$featured_img_url}</featuredImg>");
					}				

					$tags = get_the_tags();
					if($tags) {
						echo "<tags>";
						foreach ($tags as $tag){
							echo "<tag><name>". $tag->name ."</name><link>". get_tag_link($tag->term_id) ."</link></tag>";
						}
						echo "</tags>";
					}
					?>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
        <?php endwhile; 
		}else{
		?>	

	   	    <item>
				<title>Quote of the day</title>
				<link><?php echo site_url();?></link>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', time('Y-m-d H:i:s', true), false); ?></pubDate>
				<dc:creator>admin</dc:creator>
				<guid isPermaLink="false"><?php echo site_url();?></guid>
				<description><![CDATA[If you don&#8217;t have something nice to say, don&#8217;t say anything all. &#8212; Thumper the rabbit from the Disney animated film Bambi]]></description>
				<rssDescription><![CDATA[If you don&#8217;t have something nice to say, don&#8217;t say anything all. &#8212; Thumper the rabbit from the Disney animated film Bambi]]></rssDescription>	
				<featuredImg><?php echo esc_url( plugins_url( 'images/thumper.png'.$image_name, __FILE__ ) );?></featuredImg>					
			</item>
		<?php		
		}
		?>
		</quotesoftheday>

		<?php 
		}

		wp_reset_query();

		if( $display_flash == "yes" && isset($_GET["type"]) && sanitize_text_field($_GET["type"])=="flashes" ){
		$flashbriefs = query_posts('post_type=flash-briefings&post_status=publish&order=DESC&posts_per_page=' . $flashbriefCount);
		?>

		<flashbriefs>
		<?php 
		if( have_posts() ){
			while(have_posts()) : the_post(); ?>
                <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <dc:creator><?php the_author(); ?></dc:creator>
                    <guid isPermaLink="false"><?php the_guid(); ?></guid>

					<?php
					$content = get_the_content();
					if( !empty($content)){
					$content = wp_strip_all_tags( apply_filters('the_content', $content));
					?>

                    <description><![CDATA[<?php echo $content; ?>]]></description>
					<?php } ?>
                    <rssDescription><![CDATA[<?php the_excerpt_rss() ?>]]></rssDescription>

					<?php
					if ( has_post_thumbnail( get_the_ID() ) ) {
						$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
						echo("<featuredImg>{$featured_img_url}</featuredImg>");
					}

					$tags = get_the_tags();			
					if($tags) {
						echo "<tags>";
						foreach ($tags as $tag){
							echo "<tag><name>". $tag->name ."</name><link>". get_tag_link($tag->term_id) ."</link></tag>";
						}
						echo "</tags>";
					}
					?>
                    <?php rss_enclosure(); ?>
                    <?php do_action('rss2_item'); ?>
                </item>
        <?php endwhile; 
		}else{
		?>	
			<item>
				<title>Flash Briefing – The power of voice</title>
				<link><?php echo site_url();?></link>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', time('Y-m-d H:i:s', true), false); ?></pubDate>
				<dc:creator>admin</dc:creator>
				<guid isPermaLink="false"><?php echo site_url();?></guid>
				<description>
				<![CDATA[
				How do we really interact with the world and each other?&#8230; Humans have on this planet for over 200,000 years. The first writing only appeared in the 10th century BC. Keyboards appeared in 1868. It was all voice for over 98% of our timeline. We are all &#8220;hard-wired&#8221; to use voice. Voice, communications, community made the difference. It is why we survived and thrived over the last 200,000 years. We beat stronger, bigger, faster &#8220;animals&#8221; and thrived… Because we used voice… Cooperation, communication, learning from others. Voice is what makes us special. Voice is fast, concise, always available, and direct. It is our natural way of communicating.
				]]>
				</description>
				<rssDescription>
				<![CDATA[
				How do we really interact with the world and each other?&#8230; Humans have on this planet for over 200,000 years. The first writing only appeared in the 10th century BC. Keyboards appeared in 1868. It was all voice for over 98% of our timeline. We are all &#8220;hard-wired&#8221; to use voice. Voice, communications, community made [&#8230;]
				]]>
				</rssDescription>
				<featuredImg><?php echo esc_url( plugins_url( 'images/thehunt.jpg'.$image_name, __FILE__ ) );?></featuredImg>					
			</item>
		<?php	
		}
		?>
		</flashbriefs>
		<?php } ?>
</channel>
</rss>