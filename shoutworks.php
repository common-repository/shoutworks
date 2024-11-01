<?php
/**
 * shoutworks
 *
 * @package     Shoutworks
 * @author      SWKS Corp.
 * @license     Copyright SWKS Corp. and other Intellectual Property owners 2011 – 2020. Shoutworks is a Registered Trademark. Technology protected by US Patent No. 16/673,333.
 *
 * @wordpress-plugin
 * Plugin Name: Shoutworks - Get 90%+ Open Rates: Send Notifications Through Amazon Alexa
 * Plugin URI: https://shoutworks.com
 * Version: 1.0.9
 * Description: Grow and monetize your audience by sending notifications through the ultimate in-home chatbot, Amazon Alexa, in just a few clicks.
 * Author: SWKS Corp
 * License: Copyright SWKS Corp. and other Intellectual Property owners 2011 – 2020. Shoutworks is a Registered Trademark. Technology protected by US Patent No. 16/673,333.
 * License URI: https://shoutworks.com
 * Text Domain: shoutworks
 */

if( !function_exists('swks_activate') ){
    function swks_activate(){
        $description = get_bloginfo('description');
        $alex_site_intro = !empty( $description ) ? $description : get_bloginfo('name');

        add_option('swks_alex_site_intro', $alex_site_intro, '', 'no');
        add_option('swks_display_quote', 'yes', '', 'no');
        add_option('swks_quote_number', 5, '', 'no');
        add_option('swks_quote_title', "Quote of the day", '', 'no');
        add_option('swks_display_podcast', '', '', 'no');
        add_option('swks_podcast_rss_url', '', '', 'no');
        add_option('swks_notify_title', "Notifications", '', 'no');
        add_option('swks_alexa_notify', 'yes', '', 'no');
        add_option('swks_display_flash', 'yes', '', 'no');
        add_option('swks_flash_number', 5, '', 'no');
        add_option('swks_flash_title', "Flash Briefing", '', 'no');
        add_option('swks_display_blog', 'yes', '', 'no');
        add_option('swks_blog_title', "Blog Content Reader", '', 'no');
        add_option('swks_blog_number', 5, '', 'no');
        add_option('swks_display_deal', 'yes', '', 'no');
        add_option('swks_deal_number', 5, '', 'no');
        add_option('swks_deal_title', "Deal Of the day", '', 'no');
        add_option('swks_voice_support_email', get_option('admin_email'), '', 'no');
        add_option('pro_shoutworks_license_key', '', '', 'no');
        add_option('shoutworks_activated', 'no', '', 'no');
        add_option('swks_skill_published', 'no', '', 'no');
        add_option('shoutworks_user_id', '', '', 'no');
        add_option('swks_skill_name', get_bloginfo('skill_name'), '', 'no');
        add_option('swks_skill_invocation', get_bloginfo('skill_invocation'), '', 'no');
        add_option('swks_beta_tester_email', '', '', 'no');
        add_option('swks_skill_create_status', 'PENDING', '', 'no');
        add_option('swks_skill_icon', '', '', 'no');

        $fount_post = post_exists( "Deal of the day",'','','deals');
        if( !$fount_post ){
            $dealid = wp_insert_post(array(
                'post_title' => 'Deal of the day',
                'post_type' => 'deals',
                'post_status' => 'publish',
                'post_content' => '<p>Every morning can be the beginning of a new day of experiences, fun, and discovery.  Check back here for our special deals to help you make the most of your day.</p>'

            ));
            swks_add_image_attachment($dealid, 'nycsunrise.jpg');
        }
        $fount_post = post_exists( "Quote of the day",'','','quotes');
        if( !$fount_post ){
            $quoteid = wp_insert_post(array(
                'post_title' => 'Quote of the day',
                'post_type' => 'quotes',
                'post_status' => 'publish',
                'post_content' => "<p>If you don't have something nice to say, don't say anything at all.</p><p>-- Thumper the rabbit from the Disney animated film Bambi</p>"
            ));
            swks_add_image_attachment($quoteid, 'thumper.png');
        }
        $fount_post = post_exists( "Why Notifications Are Essential",'','','notify');
        if( !$fount_post ){
            $notifyid = wp_insert_post(array(
                'post_title' => 'Why Notifications Are Essential',
                'post_type' => 'notify',
                'post_status' => 'publish',
                'post_excerpt' => "Some things are so important they are just worth a nudge. That's why notifications are here. Use them sparingly."
            ));
        }
        $fount_post = post_exists( "Flash Briefing - The power of voice",'','','flash-briefings');
        if( !$fount_post ){
            $flashid = wp_insert_post(array(
                'post_title' => 'Flash Briefing - The power of voice',
                'post_type' => 'flash-briefings',
                'post_status' => 'publish',
                'post_content' => '<p>How do we really interact with the world and each other?... Humans have lived on this planet for over 200,000 years. The first writing only appeared in the 10th century BC. Keyboards appeared in 1868. It was all voice for over 98% of our timeline. We are all "hard-wired" to use voice.</p><p>Voice, communications, community made the difference. It is why we survived and thrived over the last 200,000 years. We beat stronger, bigger, faster "animals" and thrived… Because we used voice… Cooperation, communication, learning from others. Voice is what makes us special. Voice is fast, concise, always available, and direct. It is our natural way of communicating.</p>'
            ));
            swks_add_image_attachment($flashid, 'thehunt.jpg');
        }
        swks_generate_json();
    }
}
register_activation_hook(__FILE__, 'swks_activate');

$swks_domian = "swks_plugin";
add_action('plugins_loaded', 'swks_plugin_init'); 
function swks_plugin_init() {
    $swks_domian = "swks_plugin";
    load_plugin_textdomain( $swks_domian, false, plugin_dir_path(__FILE__).'/languages/' ); 
}



if( !function_exists('swks_add_image_attachment') ){
    function swks_add_image_attachment($post_id, $image_name){

        // Add Featured Image to Post
        $image_url = esc_url(plugins_url('images/' . $image_name, __FILE__));
        $image_name = 'wp-header-logo.png';
        $upload_dir = wp_upload_dir(); // Set upload folder
        $image_data = file_get_contents($image_url); // Get image data
        $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
        $filename = basename($unique_file_name); // Create image file name

        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        // Create the image  file on the server
        file_put_contents($file, $image_data);

        // Check image file type
        $wp_filetype = wp_check_filetype($filename, null);

        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        // Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file, $post_id);

        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);

        // Assign metadata to attachment
        wp_update_attachment_metadata($attach_id, $attach_data);

        // And finally assign featured image to post
        set_post_thumbnail($post_id, $attach_id);

        return true;
    }
}

/**
 * Add the settings page to the menu
 */
function shoutworks_ai_menu(){

    add_menu_page('Shoutworks', 'Shoutworks', 'manage_options', 'shoutworks', 'shoutworks_ai_options', plugins_url('shoutworks/images/setting.png'), 9);

    //add_options_page( __( 'ShoutWorks', 'shoutworks' ), __( 'ShoutWorks', 'shoutworks' ), 'read', 'shoutworks', 'shoutworks_ai_options' );

    wp_register_script('swks_bootstrap_js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js',array('jquery'));
    wp_enqueue_script('swks_bootstrap_js');

    wp_register_script('sw_customjs', plugin_dir_url(__FILE__) . 'js/sw_custom.js?time='.time());
    wp_enqueue_script('sw_customjs');

    $feed_names = [];
    $feed_names['blog_title']   = get_option('swks_blog_title','Quote of the day');
    $feed_names['quote_title']  = get_option('swks_quote_title','Blog Content Reader');
    $feed_names['flash_title']  = get_option('swks_flash_title','Flash Briefing');
    $feed_names['notify_title'] = get_option('swks_notify_title','Notifications');
    $feed_names['deal_title'] = get_option('swks_deal_title','Deal Of the day');
    $feed_names['podcast_title'] = get_option('swks_podcast_title','Podcast');

    wp_localize_script('sw_customjs','sw_ajax',array( 'sw_ajax_url' => admin_url( 'admin-ajax.php' ), 'feed_names' => $feed_names    ) );
    if( isset( $_GET['page'] ) && $_GET['page'] == 'shoutworks' ){
        wp_register_style('swks_bootstrap_css', plugin_dir_url(__FILE__) .'css/bootstrap.min.css');
        wp_enqueue_style('swks_bootstrap_css');
        wp_register_style('swks_admin_shoutworks_style_css', plugin_dir_url(__FILE__) .'css/sw_admin_style.css');
        wp_enqueue_style('swks_admin_shoutworks_style_css');
    }
    wp_localize_script('customjs', 'wpApiSettings', array('root' => esc_url_raw(rest_url()), 'nonce' => wp_create_nonce('wp_rest')));
}
add_action('admin_menu', 'shoutworks_ai_menu');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'swks_salcode_add_plugin_page_settings_link');
function swks_salcode_add_plugin_page_settings_link($links){

    $links[] = '<a href="' .
        admin_url('options-general.php?page=shoutworks') .
        '">' . __('Settings') . '</a>';
    return $links;
}

include_once('all_functions.php');
/**
 * The plugin options page
 */
function shoutworks_ai_options(){

    ?>
    <div class="wrap swks_wrapper">
        <h1><?php echo esc_html__('Shoutworks', 'shoutworks'); ?></h1>
        <?php
            global $sd_active_tab;

            $sd_active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'voice_score';
        ?>
        <h2 class="nav-tab-wrapper"><?php do_action('swks_sd_settings_tab'); ?></h2>
        <?php do_action('swks_sd_settings_content'); ?>
    </div>
    <?php
}

/*
Tab 1
*/
add_action('swks_sd_settings_tab', 'swks_sd_welcome_tab', 1);
function swks_sd_welcome_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'voice_score' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=voice_score')); ?>"><?php esc_html_e('Welcome', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_welcome_render_options_page');
function swks_sd_welcome_render_options_page(){

    global $sd_active_tab;
    if ('' || 'voice_score' != $sd_active_tab)
        return;
    
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    /*if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }*/
    
    ?>
    <p></p>
    <h3><?php _e('Welcome! Get started by clicking on the License tab here', 'sd'); ?>
    <img alt="👆" width="25" class="imga" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAABICAMAAABiM0N1AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAABOUExURUdwTNKEC9iMCeeiE9uQC9KDC7ZgD9eICNmNE9yTCdGDCcp3C+OcEb1nDv/KPP/UVv/WX/vDMv/QSe6sFv/XZve7J+ijEPOyHOGYCslzC2lCP2oAAAAOdFJOUwBTid8gOfz+C6Jw372zoOKVWwAAA1RJREFUWMPF2NmS2yAQBVDtuxCLWPT/P5ruBiTHmUTIpiq4auyXOXVpGrQUxd/GMAxFjtGOyzJlcCbLpNTj105TM7lJpsoMgbZtk3r5Fqo8JOrmO2cYI9TmgDhAZSbI5ID4tuk8EN+EmbJAMheUKREMoaosq5YJ4tCQ30MKNm2WRErIPDVCiMt8ieyYJVEuCFYtE/T/Ew1N05wQtLZkn0HlUuulaiKEl5GPLkiTom1RTwRpD1VFWY3Vo0tAM9PRwaWpAiQxUQXZ4OgemrZt0q7hTW+wdzhnri18Iqi2sRJBu9TG1kvSwTusPe1Uvul5qADCn0zQuSThWzLG1JgyyfZwfnIQaQIIfoEA9A7fGA+SKVeP0y02HY5ySIWVkQht6OwgcU6u0Frb+4vd2huUNlEvRlAiAHaEOPceJGRCu/J24RwlEe7wkP//a2C1YNj6bv0mH4kZgOQPEPeSSIlkoQekOnwr7O+D6s8StuCIkaS0AXqVeMgEa8fu75vKHiNJDc2J16Od/wlhU+nb+6YBy82kQOhtbmH9MFECFOYmHEJvk/sNur0Bg3WzsEsxF0WKFKdE3JdI2P4WarFITBiCgkTCTp8QSN1D2JNaCGU9tAWK7+GbVj8FKipnANIaIC9dVOgimJlJgCYHRRJgMZJ8qqhtIZCZ7yEskkbolCRJ2At+YgC5OeFcWqBIMDx0zm97qZA1bk44dStoAIIidVXKOxCoX1MOyj5GAslbkua20dJToCMFKpYgUZ1EWD1+Ocr0R9I9atn3RlmSzpr7QDQxCNR3bdqFnySrzwmGMsVA7phTL5VHbyIl4uT85kCnP1Lv40qQnFFA2dAHLxA4XfLDV7sePVBonZtFxh46jjX9+XuYOghFqbQvkvSBcGLdo0emZu0o1BkpBnIPIahUR5WyOm4WakaM9PRJx0s2bju/9k8W7TxRzki4V/CQCtD68GXH2uHa0cLhCA5A88OH+JmgsF3wNkQBRNV+9hDfdAidLQ7d6R3Yas+Wre18L6FEQ53Q9LDWEVKXQ1DacXQdlReEFP417hNofYGIORPNz4pdzj9A4Lj56ZuXZn2ZWgxk3PLBG5xygeUnSIVIrp4+eoc3tNO41LRswNRLVX7+KnDAhy8//vVC8RdjDXRfAxtehQAAAABJRU5ErkJggg==">
    </h3>
    <div class="p-0 col-sm-7">
    <p style="font-size: 14px;">
        With Shoutworks, you can engage your audience, nurture leads, and increase sales through the power of Amazon Alexa. Watch the video below to see how to get started, or just start poking around the plugin to see for yourself.
</p><p style="font-size: 14px;">
If you ever get stuck, please reach out to our team at <a href="mailto:support@shoutworks.com">support@shoutworks.com</a> - we are here to help you grow your business and take advantage of the incredible opportunity in voice!
    </p>
    </div>
    <div class="card p-0 col-sm-7 <?php echo $cls; ?>">
        <?php echo $upgrade_link; ?>
        <iframe width="100%" height="495" src="https://www.youtube.com/embed/ZJI4GHQ3-hs" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
        <div class="card-header"  style="font-size: 14px;">Test Your Website's VoiceReady Score: Is your web page ready for voice activation? Enter your web page URL below to see your Shoutworks VoiceReady score. Anything over 50% is okay. If your score is under 40% then your site may have too many pictures and not enough text that can be turned into spoken words.
        </div>
        <div class="card-body">
            <form method="post" action="" class="">
                <input type="url" <?php if($cls=="") { ?> name="url" <?php } ?> class="form-control col-sm-12" style="background-color: #FFF;"
                       value="<?php echo esc_url(site_url()); ?>" placeholder="http://" required readonly>
                <p></p>
                <button type="submit" <?php if($cls=="") { ?> name="webpage" <?php } ?> class="btn btn-info col-sm-4 ml-1" style="float:right;">Get My
                    Shoutworks VoiceReady Score
                </button>
            </form>
            <p></p>
            <?php
            if (isset($_POST['webpage'])) {
                $all_url_list[] = site_url();
                //require_once(__DIR__ . "/library/vendor/autoload.php");
                //$caller = new \PhpInsights\InsightsCaller('AIzaSyAW8KupfPZeiEqX140Fd9ZW4T3dyp1S8E8', 'de');
                $result_html = "";
                $key = "AIzaSyAW8KupfPZeiEqX140Fd9ZW4T3dyp1S8E8";
                if (!empty($all_url_list)) {
                    foreach ($all_url_list as $all_url_list_s) {
                        /*$response = $caller->getResponse( $all_url_list_s, \PhpInsights\InsightsCaller::STRATEGY_MOBILE);
                        $result = $response->getMappedResult();
                        $getSpeedScore = $result->getSpeedScore();*/
                        $url = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$all_url_list_s}&category=performance&key={$key}";
                        $json_file = file_get_contents( $url );
                        $json_data = json_decode( $json_file, true );
                        $getSpeedScore = 0;
                        if( isset( $json_data['lighthouseResult']['categories']['performance']['score'] ) ){
                            $score = $json_data['lighthouseResult']['categories']['performance']['score'];
                            $getSpeedScore = $score * 100;
                        } 
                        $result_html .= "<label class='badge badge-light'>" . $all_url_list_s . "</label><div class='progress'><div class='progress-bar progress-bar-striped role='progressbar' style='width: " . $getSpeedScore . "%' aria-valuenow='" . $getSpeedScore . "' aria-valuemin='0' aria-valuemax='100'>" . $getSpeedScore . "%</div></div>";
                    }
                    echo $result_html;
                } else {
                    echo '<p class="error">Website is unable to crawl!</p>';
                }
            }
            ?>
        </div>
    </div>
    <?php
}

/*
Tab 2  Create Alexa skill
*/
add_action('swks_sd_settings_tab', 'swks_sd_alexaskill_tab', 7);
function swks_sd_alexaskill_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'alexaskill' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=alexaskill')); ?>"><?php _e('Create Alexa Skill', 'sd'); ?> </a>
    <?php
}


add_action('swks_sd_settings_content', 'swks_sd_alexaskill_render_options_page');
function swks_sd_alexaskill_render_options_page(){
    if ( isset( $_GET['skill_created'] ) && sanitize_text_field($_GET['skill_created']) && sanitize_text_field($_GET['skill_created']) === "true" && sanitize_text_field($_GET['status']) === "success") {
        $oldname = generateFileName('json');
        update_option('swks_skill_create_status',"CREATED");
        update_option('swks_skill_name', sanitize_text_field($_GET['skill_name']));
        update_option('swks_skill_invocation', sanitize_text_field($_GET['skill_invocation']));
        update_option('swks_beta_tester_email', sanitize_email($_GET['email']));
        swks_generate_json($oldname.".json");
    }
    
    global $sd_active_tab;
    if ('' || 'alexaskill' != $sd_active_tab)
        return;
        
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    /*if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }*/
    ?>
    <p></p>
    <h3><?php _e('Create Alexa Skill', 'sd'); ?></h3>
    <p></p>
   
    <?php  if (get_option("swks_skill_create_status") !== "CREATED"): ?>
     <form method="post" id="createSkillForm" name="createSkillForm" action="" class="">
    <div class="card p-0 col-sm-8 createSkillFormContainer <?php echo $cls;?>" id="createSkillFormContainer">
        <?php echo $upgrade_link; ?>
        <div class="card-header">You've made it to the final step! Drum roll please...<br><br>Now is when you name your Alexa skill, create a preview version only you can access, and then publish it to the world.<br><br>First, enter your Skill Name and Invocation Name below. Skill Name is what users see on the screen, and Invocation Name is what they say to Alexa to call up your skill. Then, upload your image. When that's done, click the [ Preview Alexa Skill ] button to create a private test Alexa skill which you can try in a few minutes.
        <br><br>
        <strong>Note:</strong> If you do not have an Alexa device, <a target="_blank" href="https://www.shoutworks.com/AlexaReadyDevices">click here</a> for instructions on how to get Alexa on your smartphone, tablet, or PC.
        </div>
        <div class="card-body">
            <div id="alexaskill_msg" class="boxMessage"></div>
            <?php
            $queryParamsFeedUrl = [];
            if (get_option('swks_display_quote') == "yes" || get_option('swks_display_flash') == "yes" || get_option('swks_display_blog') == "yes" || get_option('swks_display_deal') == "yes") {
                if (get_option('swks_display_quote') == "yes") {
                    $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=quotes';
                }

                if (get_option('swks_display_flash') == "yes") {
                    $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=flashes';
                }

                if (get_option('swks_display_blog') == "yes") {
                    $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=blogs';
                }

                if (get_option('swks_display_deal') == "yes") {
                    $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=deals';
                }
            }

            $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=siteinfo';

            $queryParamsFeedUrl[] = 'feed_url[]=' . site_url() . '/feed/shoutworks?type=supportemail';
            ?>
                <input type="hidden" <?php if($cls=="") { ?> name="feed_url" <?php } ?> id="feed_url" class="form-control"
                       value='<?php echo implode("&", $queryParamsFeedUrl) ?>'/>
                <input type="hidden" <?php if($cls=="") { ?> name="skill_creation_tab" <?php } ?> id="skill_creation_tab"
                       value='<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=alexaskill')); ?>'/>
                <input type="hidden" <?php if($cls=="") { ?> name="key_hash" <?php } ?> id="key_hash" class="form-control"
                       value="<?php echo base64_encode(get_option('pro_shoutworks_license_key')); ?>"/>

                <div class="alert alert-danger col-sm-12">Please note: Before submitting your skill, please set your Permalinks to “Post name” in Settings on the left hand menu. For step-by-step instructions on how to do this, <a target="_blank" href="https://shoutworks.com/portfolio/how-to-change-your-site-permalinks-on-wordpress/">click here</a></div>
                       

                <label><strong>Skill Name</strong> – The name users will see on the screen in the Amazon Skill Store</label>
                <input type="text" <?php if($cls=="") { ?> name="skill_name" <?php } ?> id="skill_name" class="form-control"
                       value="<?php echo get_option('swks_skill_name'); ?>" placeholder="Choose a name for the skill"/>
                <br>
                <label><strong>Skill Invocation Name</strong> – The name users will say to Alexa to access your skill. (Try to keep this to three words or less. It can be the same as the Skill Name.)</label>

                <input type="text" <?php if($cls=="") { ?> name="skill_invocation" <?php } ?> id="skill_invocation" class="form-control" value="<?php echo get_option('swks_skill_invocation'); ?>" placeholder="Choose skill invocation"/>
                <br>
                <label>We'll send you a private link to test your new skill. Enter the email address for the Amazon account you have connected to Alexa. If you don't have an Alexa set up, you can use any email address — you'll be prompted to set up a new Alexa account.</label>

                <input type="email" <?php if($cls=="") { ?> name="beta_tester_email" <?php } ?> id="beta_tester_email" class="form-control" value="<?php echo get_option('swks_beta_tester_email'); ?>" placeholder="Enter email address" required/>
                <br>
               
                <?php if (get_option('swks_skill_published') == 'yes') { ?>
                   <p><small style="color:red;font-size:14px;">You have already created your Alexa Skill. No need to do it again. </small></p>
                <?php } ?>

                <?php if (get_option('swks_display_quote') == "yes" || get_option('swks_display_flash') == "yes" || get_option('swks_display_blog') == "yes" || get_option('swks_display_deal') == "yes") {
                } else {
                    ?>
                    <p><small style="color:red;font-size:14px;">Please select at least one feed before clicking on the <strong>Preview Skill</strong> button.</small></p>
                    <?php
                }
                ?>
               
            <p></p>
        </div>
    </div>
    <div class="card p-0 col-sm-8 <?php echo $cls;?>">
        <?php echo $upgrade_link; ?>
        <div class="card-header">Your Skill Icon</div>
        <?php
            $upload_dir = wp_upload_dir();
            $filename= generateFileName('icon');    
            $uploaded_path = $upload_dir['basedir']."/$filename-small.png";
            $uploaded_img ="";
            $icon_label ="Choose Skill Icon";
            if(file_exists($uploaded_path))
            {
                $uploaded_img = trailingslashit( $upload_dir['baseurl'] )."$filename-small.png?time=".time();   
                $icon_label ="Change Skill Icon";
            }
            else
                $uploaded_img = esc_url(plugins_url('images/skill-icon-default.png', __FILE__));
        ?>
        <div class="card-body">
            
                <div class="row">
                    <div class="alert  alert-danger col-sm-12 skill_icon_error" ></div>
                    <div class="alert  alert-success col-sm-12 skill_icon_success" style='display:none;'></div>
                    <label class="col-sm-2"><strong>Custom Skill Icon</strong></label>
                    <input type="file" <?php if($cls == "" ) { ?>name="sw_skill_icon" <?php } ?> id="file_skill_icon" class="file_skill_icon" accept="image/x-png, .png"/>
                    <a class="btn btn-info col-sm-3 upload_skill_icon" href="#"><?php echo $icon_label;?></a>
                    <label class="col-sm-3">
                        <!--img class="icon-skill" src='<?php echo esc_url(plugins_url('images/icon-skill.png', __FILE__)); ?>' alt='shoutworks'/-->
                        <div class="icon-skill">
                            <img class="icon-skill-img" src='<?php echo $uploaded_img; ?>' alt='shoutworks' <?php if($uploaded_img=='') { echo "style='display:none;'"; }?>/>       
                        </div>
                    </label>
                    
                </div>
                <label><strong>Upload a custom icon for your Alexa skill</strong></label>
                <label>This icon will appear in Amazon Alexa Skill Store and on Alexa devices with screens, like the Echo Show. Your image must be 512px X 512px in PNG format and no more than 1MB in size.</label>
                
            <p></p>

        </div>
    </div>
     <div class="card p-0 col-sm-8">
     <?php if (get_option('shoutworks_activated') == 'yes') { ?>
        <button id="previewSubmit" type="submit" style="float: right;" <?php if($cls=="") { ?> name="submit" <?php } ?> class="btn btn-info col-sm-3 ml-1" <?php if (get_option('swks_display_quote') == "yes" || get_option('swks_display_flash') == "yes" || get_option('swks_display_blog') == "yes" || get_option('swks_display_deal') == "yes") {
        } else {
            echo "disabled";
        }

        if (get_option('swks_skill_published') == 'yes') {
            echo "disabled";
        } ?>>

        <span id="creating-label" style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Creating...
        </span>
        <span id="create-skill-label">
            Preview Alexa Skill
        </span>
        </button>

        <div class="clearfix" style="margin-bottom:10px;"></div>
        <div class="card-header col-sm-12 previecreatenote" style="display:none;"><label>We are creating your custom Alexa Skill! This will take a few minutes. You can leave this tab open and go on with other things, or grab a cup of tea if you'd like.</label></div>

    <?php } else { ?>

        <p><small style="color:red;font-size:14px;">Shoutworks License Key required (<a href="http://www.shoutworks.com/GetLicense" target="_blank">Click Here</a> if you don't have a License key) </small></p>

    <?php } ?>
    
    </div>
    </form>
    <!--- Alexa Publish --->
    <?php  endif; ?>
    
    <?php

    function isEmailVerified(){

        $email = get_option('swks_voice_support_email');
        if (!$email || !is_shoutworks_activated() ) {
            return false;
        }
        $remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/email/verify?email='.$email;
        //$remote_url = 'https://shout.works/wp-json/shoutworx/v1/email/verify?email='.$email;
        $request =  wp_remote_get($remote_url);
        $response = wp_remote_retrieve_body( $request );
        $result = json_decode($response, true);

        if ($result['status'] === "Success") {
            return true;
        }
        return false;
    }
    ?>
    <?php  if (get_option("swks_skill_create_status") === "CREATED" && get_option('swks_skill_published') == 'no' ): ?>
    <?php $isEmailVerified = isEmailVerified(); ?>
        <div class="card p-0 col-sm-8 publishSkillFormContainer" id="publishSkillFormContainer">
        <div class="card-header">Click the button below to publish your Alexa skill</div>
        <div class="card-body">
            <div id="publishskill_msg" class="publishskill_msg">
                <div class='alert alert-success col-sm-12'><strong>Success!</strong> You've created your private test skill. Please check for an email from Amazon prompting you to test your new skill. If you don't receive it, please contact support.</div>
            </div>
            <?php if(!$isEmailVerified): ?>
                <div class='alert alert-danger col-sm-12'>Please verify the email used in the Support tab before publishing your skill</div>
            <?php endif; ?>

            <div id="pub_title"><p><strong>Skill Name: </strong><?php echo isset($_GET['skill_name']) ? sanitize_text_field($_GET['skill_name']) : get_option('swks_skill_name'); ?> </p><p><strong>Alexa Invocation Name: </strong><?php echo isset($_GET['skill_invocation']) ? sanitize_text_field($_GET['skill_invocation']) : get_option('swks_skill_invocation'); ?></p></div>

            <label id="msztxt" style="display:none;">Your skill was submitted to Amazon for review and approval. This should take 1 to 3 days (if submitted on a weekday) or 2 to 5 days (if submitted on a weekend). You'll get an email from us when your Skill is live!<br><br>WARNING: While the Skill is in review your test skill is no longer available. <br/><br /><small>If you want to change your Skill Name or Invocation Name, or any of your individual Section Names (e.g. “Deal of the Day”, “Flash Briefing”), please <a href='mailto:support@shoutworks.com'>contact our support team</a> and we’ll help you change it right away.</small></label>

            <form method="post" id="publishSkillForm" name="publishSkillForm" action="">
                <input type="hidden" name="skill_creation_tab" id="skill_creation_tab"
                       value='<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=alexaskill')); ?>'/>
                <input type="hidden" name="key_hash" id="key_hash" class="form-control"
                       value="<?php echo base64_encode(get_option('pro_shoutworks_license_key')); ?>"/>
                <input type="hidden" name="skill_name" id="skill_name" value="<?php echo isset($_GET['skill_name']) ? sanitize_text_field($_GET['skill_name']) : get_option('swks_skill_name'); ?>"/>
                <input type="hidden" name="skill_invocation" id="skill_invocation" value="<?php echo get_option('swks_skill_invocation'); ?>">
                <input type="hidden" name="beta_tester_email" id="beta_tester_email" value="<?php echo get_option('swks_beta_tester_email'); ?>" />

                <label>After you try your test skill, click this button to publish your Skill to the Alexa Skill store. Once submitted, Amazon will review your skill - this should take 1-2 days (if submitted on a weekday) or 2-4 days (if submitted on a weekend). You'll get an email from us when your Skill is live!<br><br>WARNING: Once you click the [ Publish Alexa Skill ] Button your test skill will no longer be available.
                <br /><br />If you want to change your Skill Name or Invocation Name, or any of your individual Section Names (e.g. “Deal of the Day”, “Flash Briefing”), please <a href="mailto:support@shoutworks.com">contact our support team</a> and we’ll help you change it right away.</label>
                <input type="hidden" name="skill_published" value="yes"/>
                <input type="hidden" id="ajax_url" name="ajax_url" value="<?php echo admin_url('admin-ajax.php'); ?>"/>
                <button id="publishSubmit" <?php echo !$isEmailVerified ? "disabled" : "" ?>  type="submit" style="float: right;" name="submit" class="btn btn-info col-sm-3 ml-1">
                <span id="creating-label-publish" style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Publishing... 
                </span>
                    <span id="publish-skill-label">
                    Publish Alexa Skill
                </span>
                </button>
            </form>
            <p></p>
        </div>
    </div>
    <?php  endif; ?>

   <?php if (get_option('swks_skill_published') == 'yes' ): ?>
            <div class="card p-0 col-sm-8 publishfinisjSkillFormContainer" id="publishfinisjSkillFormContainer">
                <div class="card-header">Click the button below to publish your Alexa skill</div>
                <div class="card-body">
                    <div class='alert alert-success col-sm-12'><strong>Success!</strong> Wahoo! You've sent your new Alexa skill off to Amazon for review. Check your email over the next few days to see progress.</div>
                    <div id="pub_title"><p><strong>Skill Name: </strong><?php echo get_option('swks_skill_name'); ?> </p><p><strong>Invocation Name: </strong><?php echo get_option('swks_skill_invocation'); ?></p></div>
                    <label>Your skill was submitted to Amazon for review and approval. This should take 1 to 3 days (if submitted on a weekday) or 2 to 5 days (if submitted on a weekend). You'll get an email from us when your Skill is live!<br><br>WARNING: While the Skill is in review your test skill is no longer available.</label>
                    <p>If you want to change your Skill Name or Invocation Name, or any of your individual Section Names (e.g. “Deal of the Day”, “Flash Briefing”), please <a href="mailto:support@shoutworks.com">contact our support team</a> and we’ll help you change it right away.</p>
                    </div>
                </div>
    <?php  endif; ?>
    <?php
}

function swks_country_listing(){

    update_option('swks_skill_name', sanitize_text_field($_POST['skill_name']) );
    update_option('swks_skill_invocation', sanitize_text_field($_POST['skill_invocation']));
    update_option('swks_beta_tester_email', sanitize_text_field($_POST['beta_tester_email']));
    update_option('swks_skill_published', 'yes');
    swks_generate_json();
    die("updated success");
    exit;
}

add_action('wp_ajax_country_list', 'swks_country_listing');
add_action('wp_ajax_nopriv_country_list', 'swks_country_listing');

function swks_skill_publish(){
    if( ! is_shoutworks_activated() ){
        echo json_encode(array("status" => "Error"));
        die();
    }
    $skill_name = get_option("swks_skill_name");
    $remote_url = 'http://shoutworks.com/wp-json/shoutworx/v1/skill/publish';
    //$remote_url = 'http://shout.works/wp-json/shoutworx/v1/skill/publish';
    $args = array(
        'method' => 'POST',
        'timeout' => 115,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => ['Authorization' => base64_encode(get_option('pro_shoutworks_license_key'))],
        'body' => json_encode( [
            'skill_name' => $skill_name,
        ]),
        'cookies' => []
    );

    $response = wp_remote_post($remote_url, $args);
    if (is_wp_error($response)) {
        echo json_encode(array("status" => "Error"));
    } else {
        echo $response["body"];
        update_option('swks_skill_create_status',"PUBLISHED");
        swks_generate_json();
    }
    die;
}

add_action('wp_ajax_skill_publish', 'swks_skill_publish');
add_action('wp_ajax_nopriv_skill_publish', 'swks_skill_publish');

/*
Tab 3  INFORM
*/

add_action('swks_sd_settings_tab', 'swks_sd_inform_tab', 3);
function swks_sd_inform_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'inform' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=inform')); ?>"><?php _e('Inform', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_inform_render_options_page');
function swks_sd_inform_render_options_page(){

    global $sd_active_tab;
    if ('' || 'inform' != $sd_active_tab)
        return;
    ?>
    <p></p>
    <h3><?php _e('Inform', 'sd'); ?></h3>
    <p></p>
    <div class="card p-0 col-sm-6">
        <div class="card-header">This is what Alexa will say to introduce your Alexa skill. Keep it short and sweet - this is what Alexa says after she says "Welcome to [your skill]" and before she gets to the good stuff!<br><br>Once you're done here, click on Engage to customize the core sections of your Alexa skill.
            <br /><br />
            If you want Alexa to say “Welcome to [your skill]” and go straight to the good stuff without more of an introduction, you can put a dash (“-”) here and click update.
        </div>
        <div class="card-body">
            <?php
            if (isset($_POST['submit']) && !empty($_POST['alex_site_intro'])) {
                $alex_site_intro = sanitize_text_field($_POST["alex_site_intro"]);
                if (update_option('swks_alex_site_intro', $alex_site_intro)) {
                    echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Saved successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger col-sm-12"><strong>Sorry!</strong> Please add text and try again.</div>';
                }
                swks_generate_json();
            }
            ?>

            <form method="post" action="" class="">
                <textarea class="form-control" cols=60 rows=5 name="alex_site_intro"      required><?php echo stripslashes_deep(get_option('swks_alex_site_intro')); ?></textarea>
                <p></p>
                <button type="submit" name="submit" class="btn btn-info col-sm-2 ml-1" style="float:right;">Update
                </button>
            </form>
            <p></p>
        </div>
    </div>
    <?php
}

/*
Tab 4  ENGAGE
*/

add_action('swks_sd_settings_tab', 'swks_sd_engage_tab', 4);
function swks_sd_engage_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'engage' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=engage')); ?>"><?php _e('Engage', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_engage_render_options_page');
function swks_sd_engage_render_options_page(){

    global $sd_active_tab;
    if ('' || 'engage' != $sd_active_tab)
        return;
        
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }
    ?>
    <p></p>
    <h3><?php _e('Engage', 'sd'); ?></h3>
    <p></p>

    <form method="post" id="frmShoutWorksEngage" action="">
        <div class="card p-0 col-sm-6">
            <div class="card-header">These are three of the sections that form the core of your Alexa skill. For instance, when the user selects "Quote of the Day" inside your skill, Alexa will start to narrate the posts under Quote of the Day. When you're done here, head over to the Sell tab to continue customizing your Alexa skill's features.<br><br>Un-check any of the checkboxes below if you DON'T want that section included in your Alexa skill. Be sure to click Update after you make changes!
            </div>
        </div>

        <?php
        
        if (isset($_POST['submit'])) {
            $feed_names = [];
            echo '<p></p><div class="alert alert-success col-sm-6"><strong>Success!</strong> Saved successfully.</div>';
            if (isset($_POST['display_quote'])) {
                update_option('swks_display_quote', sanitize_text_field($_POST['display_quote']));
                update_option('swks_quote_number', sanitize_text_field($_POST['quote_number']));
            } else {
                update_option('swks_display_quote', 'no');
                update_option('swks_quote_number', 5);
            }

            if (isset($_POST['display_flash'])) {
                update_option('swks_display_flash', sanitize_text_field($_POST['display_flash']));
                update_option('swks_flash_number', sanitize_text_field($_POST['flash_number']));
            } else {
                update_option('swks_display_flash', 'no');
                update_option('swks_flash_number', 5);
            }
            
            if (isset($_POST['display_podcast'])) {
			    update_option('swks_display_podcast', sanitize_text_field($_POST['display_podcast']));
                update_option('swks_podcast_rss_url', sanitize_text_field($_POST['podcast_rss_url']));
            } else {
                update_option('swks_display_podcast', 'no');
                update_option('swks_podcast_rss_url', '');
            }
            
            if (isset($_POST['alexa_notify'])) {
                if(get_option('swks_skill_id')!='')
				{
					update_option('swks_alexa_notify', sanitize_text_field($_POST['alexa_notify']));
				}
				else
				{
					update_option('swks_alexa_notify', 'no');
					echo '<p></p><div class="alert alert-danger col-sm-6">We’ve turned Notifications off for now since there’s no one to notify yet. You can turn Notifications back on after you have pressed the “Preview Alexa Skill” button to create the private test version of your skill. </div>';	
				}
            } else {
                update_option('swks_alexa_notify', 'no');
                
            }

            if (isset($_POST['display_blog'])) {
                update_option('swks_display_blog', sanitize_text_field($_POST['display_blog']));
                if($cls=="")
                {
                    update_option('swks_blog_number', sanitize_text_field($_POST['blog_number']));
                }
                else
                {
                    if(sanitize_text_field($_POST['blog_number']) > 5)
                        update_option('swks_blog_number', 5);   
                    else
                        update_option('swks_blog_number', sanitize_text_field($_POST['blog_number']));
                }   
            } else {
                update_option('swks_display_blog', 'no');
                update_option('swks_blog_number', 5);
            }
            //print_r($_POST);
            swks_generate_json();
            skws_update_feed_name();
        }
        ?>
         <div class="card p-0 col-sm-6">
            <div class="card-header">
				<div class="title_view">
					<span><?php echo get_option('swks_blog_title','Blog Content Reader'); ?></span>
					<a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
				</div>
				<div class="title_edit">
					<span><input type="text" class="title_txt" name="custom_title" value="<?php echo get_option('swks_blog_title','Blog Content Reader'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
					<a class="update_title" href="#" type="blog"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
				</div>
			</div>
            <div class="card-body">
                <p><strong>Add a Blog Post by clicking Add New, and see all posts in this section by clicking View Posts:</strong> &nbsp;&nbsp;&nbsp; <a class="btn btn-primary"
                    href="<?php echo admin_url(); ?>edit.php">View Posts</a> &nbsp;&nbsp;&nbsp; <a class="btn btn-info" href="<?php echo admin_url(); ?>post-new.php">Add New</a></p>
                <?php generate_shout_player( 'post' ); ?>                    
                <p style="margin-bottom: 5px;"><strong>Include Blog Content Reader in My Alexa Skill</strong>&nbsp;&nbsp;&nbsp; <input class="form-control" type="checkbox" name="display_blog" value="yes" <?php if (get_option('swks_display_blog') == "yes") {
                        echo "checked";
                    } ?> /></p>

                <p style="font-size: 12px;margin:0;">Check this box if you want your Alexa skill to include a Blog section. Be sure to click Update afterward! </p>

                <p><strong>How many blog feeds you want to display</strong>&nbsp;&nbsp;&nbsp; <input style="width:60px;display:inline;" class="form-control" type="number" name="blog_number" value="<?php echo get_option('swks_blog_number'); ?>" <?php if($cls != '' ) { echo "max='5'"; } ?>/></p>
            </div>
        </div>
        
        <div class="card p-0 col-sm-6 <?php echo $cls; ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">
				<div class="title_view">
					<span><?php echo get_option('swks_quote_title','Quote Of The Day'); ?></span>
					<a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
				</div>
				<div class="title_edit">
					<span><input type="text" class="title_txt" name="custom_title"  value="<?php echo get_option('swks_quote_title','Quote Of The Day'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
					<a class="update_title" href="#" type="quote"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
				</div>
			</div>
            
            <div class="card-body">
                <p><strong>Add a Quote of the Day by clicking Add New, and see all posts in this section by clicking View Posts:</strong> &nbsp;&nbsp;&nbsp; <a class="btn btn-primary" href="<?php echo admin_url(); ?>edit.php?post_type=quotes">View Posts</a> &nbsp;&nbsp;&nbsp; <a class="btn btn-info" href="<?php echo admin_url(); ?>post-new.php?post_type=quotes">Add New</a></p>
                <?php generate_shout_player( 'quotes' ); ?>
                <p style="margin-bottom: 5px;"><strong>Include Quote Of The Day in My Alexa Skill</strong>&nbsp;&nbsp;&nbsp; <input class="form-control" type="checkbox" <?php if($cls=="") { ?>name="display_quote" <?php } ?> value="yes" <?php if (get_option('swks_display_quote') == "yes") {
                        echo "checked";
                    } ?> /></p>

                <p style="font-size: 12px;margin:0;">Check this box if you want your Alexa skill to include a Quote of the Day section. Be sure to click Update afterward! </p>

                <p><strong>How many Quote Of The Day feeds you want to display</strong>&nbsp;&nbsp;&nbsp; <input style="width:60px;display:inline;" class="form-control" type="number" <?php if($cls=="") { ?>name="quote_number" <?php } ?> value="<?php echo get_option('swks_quote_number'); ?>"/></p>
            </div>
        </div>

        <div class="card p-0 col-sm-6 <?php echo $cls; ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">
				<div class="title_view">
					<span><?php echo get_option('swks_flash_title','Flash Briefing'); ?></span>
					<a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
				</div>
				<div class="title_edit">
					<span><input type="text" class="title_txt" name="custom_title" value="<?php echo get_option('swks_flash_title','Flash Briefing'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
					<a class="update_title" href="#" type="flash"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
				</div>
			</div>
            <div class="card-body">
                <p><strong>Add a Flash Briefing by clicking Add New, and see all posts in this section by clicking View Posts:</strong> &nbsp;&nbsp;&nbsp; <a class="btn btn-primary" href="<?php echo admin_url(); ?>edit.php?post_type=flash-briefings">View Posts</a> &nbsp;&nbsp;&nbsp; <a class="btn btn-info" href="<?php echo admin_url(); ?>post-new.php?post_type=flash-briefings">Add New</a></p>
                <?php generate_shout_player( 'flash-briefings' ); ?>
                <p style="margin-bottom: 5px;"><strong>Include Flash Briefing in My Alexa Skill</strong>&nbsp;&nbsp;&nbsp; <input class="form-control" type="checkbox" <?php if($cls=="") { ?> name="display_flash" <?php } ?> value="yes" <?php if (get_option('swks_display_flash') == "yes") {
                        echo "checked";
                    } ?> /></p>

                <p style="font-size: 12px;margin:0;">Check this box if you want your Alexa skill to include a Flash Briefing section. Be sure to click Update afterward! </p>

                <p><strong>How many Flash Briefing feeds you want to display</strong>&nbsp;&nbsp;&nbsp; <input style="width:60px;display:inline;" class="form-control" type="number" <?php if($cls=="") { ?> name="flash_number" <?php } ?> value="<?php echo get_option('swks_flash_number'); ?>"/></p>
            </div>
        </div>
        <div class="card p-0 col-sm-6 <?php echo $cls; ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">
                <div class="title_view">
                    <span><?php echo get_option('swks_podcast_title','Podcast'); ?></span>
                    <a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
                </div>
                <div class="title_edit">
                    <span><input type="text" class="title_txt" name="custom_title" value="<?php echo get_option('swks_podcast_title','Podcast'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
                    <a class="update_title" href="#" type="podcast"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
                </div>
            </div>
            <div class="card-body">
				<p>
					<strong>Podcast RSS URL</strong> 
					<input type="text"  name="podcast_rss_url" class="form-control col-6" value="<?php echo get_option('swks_podcast_rss_url'); ?>" style="display: inline;"/>
				</p>
				
				<p><strong>Include Podcast in My Alexa Skill</strong> <input class="form-control" type="checkbox" <?php if($cls=="") { ?> name="display_podcast" <?php } ?> value="yes" <?php if (get_option('swks_display_podcast') == "yes") {echo "checked"; } ?> /></p>
                <p style="margin-bottom: 5px;">
					<strong></strong>&nbsp;&nbsp;&nbsp;
                </p>
				<p style="font-size: 12px;">This section allows you to put your Podcast RSS feed into your Alexa skill. You can retrieve your podcast RSS feed from wherever your podcast is hosted (Anchor.fm, Transistor.fm, etc.). <a target="_blank" href="https://www.podcastinsights.com/podcast-rss-feed/">Click here</a> to learn how to find your podcast’s RSS feed link.</p>
<p style="font-size: 12px;margin:0;">
Check this box if you want your Alexa skill to include a Podcast section. Be sure to click Update afterward! </p>
            </div>
        </div>
		 <div class="card p-0 col-sm-6 <?php echo $cls; ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">
				<div class="title_view">
					<span><?php echo get_option('swks_notify_title','Notifications'); ?></span>
					<a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
				</div>
				<div class="title_edit">
					<span><input type="text" class="title_txt" name="custom_title" value="<?php echo get_option('swks_notify_title','Notifications'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
					<a class="update_title" href="#" type="notify"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
				</div>
			</div>
            <div class="card-body">
                <p>Alexa notifications allow you to send a notification to all the people who have enabled your skill. <a class="float-right btn btn-primary" href="<?php echo admin_url(); ?>edit.php?post_type=notify">Manage Notifications</a> </p>
                <p>
                <a target="_blank" href="https://shoutworks.com/portfolio/alexa-notifications-in-action/">Click here</a> to see a video of Alexa notifications in action. </p>

				<p><strong>Enable Alexa Notifications</strong> <input class="form-control" type="checkbox" <?php if($cls=="") { ?> name="alexa_notify" <?php } ?> value="yes" <?php if (get_option('swks_alexa_notify') == "yes") {echo "checked"; } ?> /></p>
				<p style="font-size: 12px;margin:0;">Check this box if you want to be able to send Notifications to the people who have subscribed to (or “enabled”) your Alexa skill. Be sure to click Update afterward!</p>
            </div>
        </div>
       <p></p>
        <div class="card p-0 card-button col-sm-6">

            <?php wp_nonce_field( 'shoutworks_engage_panel', 'shoutworks_engage_field' ); ?>
            <button type="submit" name="submit" class="btn btn-info col-sm-3 ml-1" style="float:right;">
                <span id="update-button-action-label">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Updating...
                </span>
                <span id="update-button-default-label">
                    Update
                </span>
            </button>
        </div>
    </form>
    <?php
}

/*
Tab 5  SELL
*/

add_action('swks_sd_settings_tab', 'swks_sd_sell_tab', 5);
function swks_sd_sell_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'sell' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=sell')); ?>"><?php _e('Sell', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_sell_render_options_page');
function swks_sd_sell_render_options_page(){

    global $sd_active_tab;
    if ('' || 'sell' != $sd_active_tab)
        return;
    
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }
    ?>
    <p></p>
    <h3><?php _e('Sell', 'sd'); ?></h3>
    <p></p>

    <form method="post" id="frmShoutWorksEngage" action="">
        <div class="card p-0 col-sm-6 <?php echo $cls; ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">
				<div class="title_view">
					<span><?php echo get_option('swks_deal_title','Deal Of The Day'); ?></span>
					<a class="edit_title" href="#"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-pencil.png" /></a>
				</div>
				<div class="title_edit">
					<span><input type="text" class="title_txt" name="custom_title" value="<?php echo get_option('swks_deal_title','Deal Of The Day'); ?>" onkeypress="this.style.width = ((this.value.length + 1) * 8) + 'px';"/></span>
					<a class="update_title" href="#" type="deal"><img src="<?php echo plugin_dir_url(__FILE__) ?>images/icon-check.png" /></a>
				</div>
			</div>
            <div class="card-body">
                <?php
                if (isset($_POST['submit'])) {
                    echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Saved successfully.</div>';

                    if (isset($_POST['display_deal'])) {
                        update_option('swks_display_deal', sanitize_text_field($_POST['display_deal']));
                        update_option('swks_deal_number', sanitize_text_field($_POST['deal_number']));
                    } else {
                        update_option('swks_display_deal', 'no');
                        update_option('swks_deal_number', 5);
                    }
                    swks_generate_json();
                    skws_update_feed_name();
                }
                ?>

                <p><strong>Add a Deal of the Day by clicking Add New, and see all posts in this section by clicking View Posts:</strong> &nbsp;&nbsp;&nbsp; <a class="btn btn-primary" href="<?php echo admin_url(); ?>edit.php?post_type=deals">View Posts</a> &nbsp;&nbsp;&nbsp; <a class="btn btn-info" href="<?php echo admin_url(); ?>post-new.php?post_type=deals">Add New</a></p>
                <?php generate_shout_player( 'deals' ); ?>
                <p style="margin-bottom: 5px;"><strong>Include Deal of the Day in My Alexa Skill</strong>&nbsp;&nbsp;&nbsp; <input class="form-control" type="checkbox" <?php if($cls=="") { ?> name="display_deal" <?php } ?> value="yes" <?php if (get_option('swks_display_deal') == "yes") {
                        echo "checked";
                    } ?> /></p>

                <p style="font-size: 12px;margin:0;">Check this box if you want your Alexa skill to include a Deal of the Day section. Be sure to click Update afterward! </p>

                <p><strong>How many Deal Of The Day posts do you want to include?</strong>&nbsp;&nbsp;&nbsp; <input style="width:60px;display:inline;" class="form-control" type="number" <?php if($cls=="") { ?> name="deal_number" <?php } ?> value="<?php echo get_option('swks_deal_number'); ?>"/></p>

                <p></p>
                <button type="submit" <?php if($cls=="") { ?> name="submit" <?php } ?> class="btn btn-info col-sm-3 ml-1" style="float:right;">
                    <span id="update-button-action-label">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Updating...
                    </span>
                    <span id="update-button-default-label">
                        Update
                    </span>
                </button>
            </div>
        </div>
    </form>
    <?php
}

/*
Tab 6  Support
*/
add_action('swks_sd_settings_tab', 'swks_sd_service_tab', 6);
function swks_sd_service_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'support' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=support')); ?>"><?php _e('Support', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_service_render_options_page');
function swks_sd_service_render_options_page(){

    global $sd_active_tab;
    if ('' || 'support' != $sd_active_tab)
        return;
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }    
    ?>
    <div class="col-md-8 ">
        <p></p>
        <h3><?php _e('Support', 'sd'); ?></h3>
        <p></p>
        <div class="card p-0 col-sm-12 <?php echo  $cls ?>">
            <?php echo $upgrade_link; ?>
            <div class="card-header">Your users can speak a message to you through Alexa. Alexa will automatically take down their message and send the text to you at the email address below. She'll include their email address for easy follow-up.<br><br>
            There are many ways to use this feature:<br>- Generate leads by offering a lead magnet like a free eBook or giveaway<br>- Take orders for products, food, or services<br>- Request an estimate or quote<br>- Say thanks or submit a testimonial<br>- Free up time by automating customer support<br>- Or anything else you can think of!
            </div>
            <div class="card-body">
                <?php
                if (isset($_POST['submit'])) {
                    if (!empty($_POST['voice_support_email']) && is_shoutworks_activated() ) {
                        swks_update_support_mail();
                        echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Your support email address has been saved.</div>';
                    }
                    swks_generate_json();
                }
                ?>

                
                <form method="POST" class="form" action="">
                    <input class="form-control" type="email" <?php if($cls=="") { ?> name="voice_support_email" <?php } ?> required="" placeholder="Your email address" value="<?php echo get_option('swks_voice_support_email'); ?>">
                    <p></p>
                        <input type="submit" <?php if($cls=="") { ?> name="submit" <?php } ?> class="btn btn-info button-submit-ticket form-control col-sm-2" style="float:right;" value="Update">
                </form>
            </div>
        </div>
    </div>
    <?php
}

function swks_update_support_mail(){

    $email = sanitize_email($_POST['voice_support_email']);
    update_option('swks_voice_support_email', $email);

    $remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/email/verify';
    //$remote_url = 'https://shout.works/wp-json/shoutworx/v1/email/verify';
    $args = [
        'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'        => json_encode([
            'email' => $email,
        ])
    ];

    wp_remote_post($remote_url, $args);
}

/*
Tab 6  shoutworkssupport
*/
add_action('swks_sd_settings_tab', 'swks_sd_support_tab', 6);
function swks_sd_support_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'shoutworkssupport' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url('options-general.php?page=shoutworks&tab=shoutworkssupport'); ?>"><?php _e('Shoutworks Support', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_support_render_options_page');
function swks_sd_support_render_options_page(){

    global $sd_active_tab;
    if ('' || 'shoutworkssupport' != $sd_active_tab)
        return;
    $cls="";    
    $upgrade_link = "";
    $data = swks_get_plan_info();
    /*if($data)
    {
        $cls="upgarde_plan";
        $upgrade_link = getUpgradeLink();
    }*/  
    ?>

    <p></p>
    <?php
    function custom_slug_mail_from_sh($email)
    {
        if (isset($_REQUEST['support_email']))
            return sanitize_email($_REQUEST['support_email']);
        return "support@shoutworks.com";
    }

    function custom_slug_name_from_sh($name)
    {
        if (isset($_REQUEST['support_name']))
            return sanitize_text_field($_REQUEST['support_name']);
        return "shoutworks";
    }

    if (isset($_POST['submit'])) {
        $emailTo = "support@shoutworks.com";
        $site_url = site_url();
        $error_array = array();
        $support_subject = '';
        $support_name = '';

        if (empty(trim($_POST['support_subject']))) {
            $error_array[] = 'Please Summarize your issue in a few words.';
        } else {
            $support_subject = sanitize_text_field($_POST['support_subject']);
        }

        if (empty(trim($_POST['support_name']))) {
            $error_array[] = 'Please enter your name.';
        } else {
            $support_name = sanitize_text_field($_POST['support_name']);
        }

        $support_email = '';

        if (empty(trim($_POST['support_email']))) {
            $error_array[] = "Please enter your email";
        } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+.[a-z]{2,4}$/i", sanitize_text_field($_POST['support_email']))) {
            $error_array[] = 'You entered an invalid email address.';
        } else {
            $support_email = sanitize_email($_POST['support_email']);
        }

        $support_request = '';
        if (empty(trim($_POST['support_request']))) {
            $error_array[] = 'Please describe your issue.';
        } else {
            $support_request = sanitize_text_field($_POST['support_request']);
        }

        if (empty($error_array)) {
            $subject = 'Issue for Plugin From ' . $support_email;
            $body = "Name: $support_name \n\n Subject: $support_subject \n\n Email: $support_email \n\n Issue: $support_request \n\n Site URL: $site_url";

            add_filter('wp_mail_from', 'custom_slug_mail_from_sh');
            add_filter('wp_mail_from_name', 'custom_slug_name_from_sh');

            $mailResult = wp_mail($emailTo, $subject, $body);

            remove_filter('wp_mail_from', 'custom_slug_mail_from_sh');
            remove_filter('wp_mail_from_name', 'custom_slug_name_from_sh');

            echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Your support request was sent to us. We usually respond within 2 to 12 hours. In the meantime, <a target="_blank" href="https://www.shoutworks.com/faq">Click Here</a> for our FAQ.</div>';
        } else {
            echo '<div class="alert alert-danger col-sm-12"><strong>Error!</strong> ' . implode('<br/>', $error_array) . '</div>';
        }
    }

    ?>
    <p></p>
    <div class="support col-sm-12">
        
        <h2>Enter your support request</h2>
        <p style="font-size:16px;">A lot of issues are already described in the <a href="http://www.shoutworks.com/documentation" target="_blank">documentation.</a> Please check if your issue is already in the knowledge base before submitting a ticket.</p>

        <p style="font-size:16px;">Please provide a short description, your e-mail address and a summary of the issue(s) you are experiencing. The following information is automatically added to your ticket to provide better service:</p>

        <ul class="support-list" style="margin-left: 30px;list-style: square;">
            <li>license key</li>
            <li>scan results</li>
            <li>your domain</li>
        </ul>
    </div>

    <p></p>
    <div class="card p-0 col-sm-8 <?php echo $cls; ?>">
        <?php echo $upgrade_link; ?>
        <div class="card-header">Enter your support request</div>
        <div class="card-body">
            <form method="POST" class="form" action="">
                <input class="form-control" type="text" <?php if($cls=="") { ?> name="support_name" <?php } ?> required="" placeholder="Name">
                <br/>
                <input class="form-control" type="text" <?php if($cls=="") { ?> name="support_subject" <?php } ?> required=""
                    placeholder="Summarize your issue in a few words">

                <br/>

                <input class="form-control" type="text" <?php if($cls=="") { ?> name="site_url" <?php } ?> style="background-color: #FFF;" value="<?php echo site_url(); ?>" placeholder="WordPress site URL" required readonly>
                <br/>

                <input class="form-control" type="email" <?php if($cls=="") { ?> name="support_email" <?php } ?> required=""
                       placeholder="Your email address" value="<?php echo get_option('admin_email'); ?>">
                <br/>

                <textarea class="form-control" placeholder="Describe your issue" rows="6" <?php if($cls=="") { ?> name="support_request" <?php } ?> required=""></textarea>

                <br/>

                <input type="submit" <?php if($cls=="") { ?> name="submit" <?php } ?> class="btn btn-info button-submit-ticket form-control col-md-3 " style="float:right;" value="Submit Support Ticket">

            </form>
        </div>
    </div>
    <p></p>
    <?php
}

/*
Tab 7 License
*/

add_action('swks_sd_settings_tab', 'swks_sd_license_tab', 9);
function swks_sd_license_tab(){

    global $sd_active_tab; ?>
    <a class="nav-tab <?php echo $sd_active_tab == 'license' || '' ? 'nav-tab-active' : ''; ?>"
       href="<?php echo esc_url(admin_url('options-general.php?page=shoutworks&tab=license')); ?>"><?php _e('License', 'sd'); ?> </a>
    <?php
}

add_action('swks_sd_settings_content', 'swks_sd_license_render_options_page');
function swks_sd_license_render_options_page(){

    global $sd_active_tab;

    if ('' || 'license' != $sd_active_tab)
        return;
    
    $first_name = get_option( 'signup_first_name' );
    $last_name = get_option( 'signup_last_name' );
    $domain = get_option( 'signup_domain' );
    $email = get_option( 'signup_email' );

    $current_user = wp_get_current_user();    
    $first_name = $first_name ? $first_name : $current_user->user_firstname;
    $last_name = $last_name ? $last_name : $current_user->user_lastname;
    $domain = $domain ? $domain : get_option( 'siteurl' );
    $email = $email ? $email : get_option( 'admin_email' );

    $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field($_POST['first_name']) : $first_name;
    $last_name = isset( $_POST['last_name'] ) ? sanitize_text_field($_POST['last_name']) : $last_name;
    $domain = isset( $_POST['domain'] ) ? sanitize_text_field($_POST['domain']) : $domain;
    $email = isset( $_POST['email'] ) ? sanitize_email($_POST['email']) : $email;
    ?>

    <p></p>
    <h3><?php _e('License', 'sd'); ?></h3>
    <p></p>
    <div class="card p-0 col-sm-6">
        <div class="card-header">If you already received your License, please enter it here.</div>
        <div class="card-body">
            <?php
            $signup_error_array = array();
            if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'shoutworks_signup' ) ) {
                if (empty(trim($_POST['first_name']))) {
                    $signup_error_array[] = 'Please enter First name.';
                }
                if (empty(trim($_POST['last_name']))) {
                    $signup_error_array[] = 'Please enter Last name.';
                }
                if (empty(trim($_POST['domain']))) {
                    $signup_error_array[] = 'Please enter Domain.';
                }
                if (empty(trim($_POST['email']))) {
                    $signup_error_array[] = 'Please enter Email address.';
                }
                if (!count( $signup_error_array )) {
                    update_option( 'signup_first_name', sanitize_text_field( $_POST['first_name'] ) );
                    update_option( 'signup_last_name', sanitize_text_field( $_POST['last_name'] ) );
                    update_option( 'signup_domain', sanitize_text_field( $_POST['domain'] ) );
                    update_option( 'signup_email', sanitize_email( $_POST['email'] ) );
                    $remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/signup';
                    //$remote_url = 'https://shout.works/wp-json/shoutworx/v1/signup';
                    $host = $domain = sanitize_text_field( $_POST['domain'] );
                    $domain_details = parse_url( $domain );
                    if( isset( $domain_details['host'] ) && !empty( $domain_details ) ){
                        $host = $domain_details['host'];
                    }
                    $args = array(
                        'method' => 'POST',
                        'timeout' => 115,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => json_encode([
                            'first_name'    => sanitize_text_field( $_POST['first_name'] ),
                            'last_name'     => sanitize_text_field( $_POST['last_name'] ),
                            'email'         => sanitize_email( $_POST['email'] ),
                            'domain'        => $host,
                        ]),
                        'cookies' => []
                    );

                    $response = wp_remote_post($remote_url, $args);
                    if (is_wp_error($response)) {
                        $error_message = $response->get_error_message();
                        $signup_error_array[] = "Something went wrong. Please try it again.";
                    } else {
                        $json_values = json_decode($response['body'], true);
                        if (isset($json_values["key"])) {
                            update_option('pro_shoutworks_license_key', $json_values["key"]);
                            echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> You are signed up successfully. Please click to activate your license.</div>';
                        }else{
                            $signup_error_array[] = $json_values["error_message"];
                        }
                    }
                }
            }
            if (isset($_POST['submit_activate'])) {
                $error_array = array();
                if (empty(trim($_POST['pro_shoutworks_license_key']))) {
                    $error_array[] = 'Please enter license kay.';
                } else {
                    $pro_shoutworks_license_key = sanitize_text_field($_POST['pro_shoutworks_license_key']);
                    $remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/validate';
					//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/validate';
                    
                    $args = array(
                        'method' => 'POST',
                        'timeout' => 115,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => json_encode([
                            'key' => $pro_shoutworks_license_key,
                            'domain' => parse_url(site_url())['host']
                        ]),
                        'cookies' => []
                    );

                    $response = wp_remote_post($remote_url, $args);
                    if (is_wp_error($response)) {
                        $error_message = $response->get_error_message();
                        $error_array[] = "Something went wrong. Please try again with correct License key.";
                    } else {
                        $json_values = json_decode($response['body'], true);
                        //if( isset( $json_values["domain"] ) && $json_values["domain"] == base_url() ){
    
                        if (isset($json_values["user_id"])) {
                            update_option('pro_shoutworks_license_key', $pro_shoutworks_license_key);
                            update_option('shoutworks_activated', 'yes');
                            update_option('shoutworks_user_id', $json_values["user_id"]);
                            swks_check_plan();
                            swks_generate_json();
                        } else {
                            if(isset($json_values['error']))
                                $error_array[]= $json_values['error_message'];
                            else
                                $error_array[] = 'License key is invalid.';
                        }
                    }
                }

                if (!empty($error_array)) {
                    update_option('shoutworks_activated', 'no');
                    update_option('pro_shoutworks_license_key', '');
                    swks_generate_json();
                    echo '<div class="alert alert-danger col-sm-12"><strong>Sorry!</strong> ' . implode('<br/>', $error_array) . '</div>';
                }

                if (get_option('shoutworks_activated') == 'yes') {
                    echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Shoutworks license activated.</div>';
                }
            }

            if (isset($_POST['submit_deactivate'])) {
                update_option('shoutworks_activated', 'no');
                update_option('pro_shoutworks_license_key', '');
                update_option('swks_plan', '');
                swks_generate_json();
                echo '<div class="alert alert-success col-sm-12"><strong>Success!</strong> Shoutworks license deactivated.</div>';
            }

            ?>

            <form method="POST" class="form" action="">
                <input id="pro_shoutworks_license_key" name="pro_shoutworks_license_key"
                       placeholder="Enter your Shoutworks license here" type="text" class="form-control" value="<?php echo get_option('pro_shoutworks_license_key'); ?>" required>

                <!--<span style="background-color:green;padding:5px 10px;margin:10px;display:inline-block;border-radius:5px;color:#FFF;">active</span>-->

                <br>

                <?php if (get_option('shoutworks_activated') == 'yes') { ?>
                    <strong>Current Licence status: </strong><span class="badge badge-success"
                    style="font-size:16px;font-weight:normal;"><?php echo ( swks_get_plan_info() ? 'Free' : 'Paid' ); ?></span>

                    <!--<span class="badge badge-danger" style="font-size:16px;font-weight:normal;">Inactive</span>-->

                    <button type="submit" name="submit_deactivate" style="background:none;border: 0;"><span class="badge badge-warning" style="font-size:16px;font-weight:normal;">Deactivate License</span>
                    </button>

                    <?php if( swks_get_plan_info() ){ ?>
                        <a href="https://shoutworks.com/login/" target="_blank" class="badge badge-success" style="font-size: 16px;font-weight:normal;background: #17a2b8;">Upgrade</a>
                    <?php } ?>
                <?php } ?>

                <input type="submit" class="btn btn-info button-submit-ticket form-control col-sm-3" name="submit_activate" style="float:right;" value="Activate License" <?php if (get_option('shoutworks_activated') == 'yes') { ?> disabled <?php } ?>>
            </form>
        </div>
    </div>
    <?php if (get_option('shoutworks_activated') == 'yes') { 
        return;
    } ?>    
    <div class="card p-0 col-sm-6 signup_panel">
        <div class="card-header">Don't have a License yet? Sign up here!<br><br>Sign up below to start using <a href="https://shoutworks.com/portfolio/free-plan-explained/" target="_blank">Shoutworks Basic</a>, free forever. 
<br /><br/>
Want more features? <a href="https://shoutworks.com/plans-and-pricing/" target="_blank">Click here to start your 30-Day Free Trial of Shoutworks Standard</a>, which gives you access to all of our powerful features - including sending Alexa Notifications, creating an Alexa skill for your Podcast, and way more. See all the features included in Shoutworks Standard <a href="https://shoutworks.com/plans-and-pricing/" target="_blank">here</a></div>
        <div class="card-body">
            <?php
                if (count($signup_error_array)) {
                    echo '<div class="alert alert-danger col-sm-12"><strong>Sorry!</strong> ' . implode('<br/>', $signup_error_array) . '</div>';
                }
            ?>
            <form class="form" action="" method="post">
                <input type="text" required="required" class="form-control" name="first_name" placeholder="First Name" value="<?php echo $first_name; ?>" />
                <input type="text" required="required" class="form-control" name="last_name" placeholder="Last Name" value="<?php echo $last_name; ?>" />
                <input type="text" required="required" class="form-control" name="domain" placeholder="Domain" value="<?php echo $domain; ?>" />
                <input type="email" required="required" class="form-control" name="email" placeholder="Email" value="<?php echo $email; ?>" />
                <?php wp_nonce_field( 'shoutworks_signup', '_wpnonce' ); ?>
                <input type="submit" class="btn btn-info button-submit-ticket form-control col-sm-3" style="float:right;" value="Sign Up" >
            </form>
        </div>
    </div>
    <p></p>
    <?php
}

/**
 * Load the translation file
 */

function shoutworks_ai_load_plugin_textdomain(){

    load_plugin_textdomain('shoutworks');
}
add_action('plugins_loaded', 'shoutworks_ai_load_plugin_textdomain');

/*
* Creating a function to create our CPT
*/

function swks_custom_post_type(){
	$title = get_option("swks_notify_title","Notifications");
    $notifyslabels = array(
        'name' => _x($title, 'Post Type General Name', 'twentythirteen'),
        'singular_name' => _x('Deal', 'Post Type Singular Name', 'twentythirteen'),
        'menu_name' => __($title, 'twentythirteen'),
        'parent_item_colon' => __('Parent Deal', 'twentythirteen'),
        'all_items' => __('All '.$title, 'twentythirteen'),
        'view_item' => __('View '.$title, 'twentythirteen'),
        'add_new_item' => __('Add New '.$title, 'twentythirteen'),
        'add_new' => __('Add '.$title, 'twentythirteen'),
        'edit_item' => __('Modify '.$title, 'twentythirteen'),
        'update_item' => __('Update '.$title, 'twentythirteen'),
        'search_items' => __('Search '.$title, 'twentythirteen'),
        'not_found' => __('Not Found', 'twentythirteen'),
        'not_found_in_trash' => __('Not found in Trash', 'twentythirteen'),
    );

    $notifyargs = array(
        'label' => __($title, 'twentythirteen'),
        'description' => __($title, 'twentythirteen'),
        'labels' => $notifyslabels,
        'supports' => array('title', 'excerpt'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    // Registering your Custom Post Type - Notify
    register_post_type('notify', $notifyargs);
	
	
    // Set UI labels for Custom Post Type - Deals of the day
	$title = get_option("swks_deal_title","Deal of The Day");
    $dealslabels = array(
        'name' => _x($title, 'Post Type General Name', 'twentythirteen'),
        'singular_name' => _x('Deal', 'Post Type Singular Name', 'twentythirteen'),
        'menu_name' => __($title, 'twentythirteen'),
        'parent_item_colon' => __('Parent Deal', 'twentythirteen'),
        'all_items' => __('All '.$title, 'twentythirteen'),
        'view_item' => __('View '.$title, 'twentythirteen'),
        'add_new_item' => __('Add New '.$title, 'twentythirteen'),
        'add_new' => __('Add '.$title, 'twentythirteen'),
        'edit_item' => __('Modify '.$title, 'twentythirteen'),
        'update_item' => __('Update '.$title, 'twentythirteen'),
        'search_items' => __('Search '.$title, 'twentythirteen'),
        'not_found' => __('Not Found', 'twentythirteen'),
        'not_found_in_trash' => __('Not found in Trash', 'twentythirteen'),
    );

    // Set other options for Custom Post Type - Deals of the day
    //'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),

    $dealsargs = array(
        'label' => __($title, 'twentythirteen'),
        'description' => __($title, 'twentythirteen'),
        'labels' => $dealslabels,

        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail'),

        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies' => array('Tags', 'post_tag'), // this is IMPORTANT

        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */

        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    // Registering your Custom Post Type - Deals of the day
    register_post_type('deals', $dealsargs);

    // Set UI labels for Custom Post Type - Quotes of the day
    $qtitle = get_option("swks_quote_title","Quotes of The Day");
    $quoteslabels = array(
        'name' => _x('Quotes', 'Post Type General Name', 'twentythirteen'),
        'singular_name' => _x('Quote', 'Post Type Singular Name', 'twentythirteen'),
        'menu_name' => __($qtitle, 'twentythirteen'),
        'parent_item_colon' => __('Parent Quotes', 'twentythirteen'),
        'all_items' => __('All '.$qtitle, 'twentythirteen'),
        'view_item' => __('View '.$qtitle, 'twentythirteen'),
        'add_new_item' => __('Add New '.$qtitle, 'twentythirteen'),
        'add_new' => __('Add '.$qtitle, 'twentythirteen'),
        'edit_item' => __('Modify '.$qtitle, 'twentythirteen'),
        'update_item' => __('Update '.$qtitle, 'twentythirteen'),
        'search_items' => __('Search '.$qtitle, 'twentythirteen'),
        'not_found' => __('Not Found', 'twentythirteen'),
        'not_found_in_trash' => __('Not found in Trash', 'twentythirteen'),
    );

    // Set other options for Custom Post Type - Quotes of the day
    //'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),

    $quotesargs = array(
        'label' => __($qtitle, 'twentythirteen'),
        'description' => __($qtitle, 'twentythirteen'),
        'labels' => $quoteslabels,

        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail'),

        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies' => array('Tags', 'post_tag'), // this is IMPORTANT

        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */

        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    // Registering your Custom Post Type - Quotes of the day
    register_post_type('quotes', $quotesargs);

    // Set UI labels for Custom Post Type - Flashs
    $ftitle = get_option("swks_flash_title","Quotes of The Day");
    $flashslabels = array(
        'name' => _x($ftitle, 'Post Type General Name', 'twentythirteen'),
        'singular_name' => _x('Flash', 'Post Type Singular Name', 'twentythirteen'),
        'menu_name' => __($ftitle, 'twentythirteen'),
        'parent_item_colon' => __('Parent '.$ftitle, 'twentythirteen'),
        'all_items' => __('All '.$ftitle, 'twentythirteen'),
        'view_item' => __('View '.$ftitle, 'twentythirteen'),
        'add_new_item' => __('Add New '.$ftitle, 'twentythirteen'),
        'add_new' => __('Add '.$ftitle, 'twentythirteen'),
        'edit_item' => __('Modify '.$ftitle, 'twentythirteen'),
        'update_item' => __('Update '.$ftitle, 'twentythirteen'),
        'search_items' => __('Search '.$ftitle, 'twentythirteen'),
        'not_found' => __('Not Found', 'twentythirteen'),
        'not_found_in_trash' => __('Not found in Trash', 'twentythirteen'),
    );

    // Set other options for Custom Post Type - Flashs
    //'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),

    $flashsargs = array(
        'label' => __($ftitle, 'twentythirteen'),
        'description' => __($ftitle, 'twentythirteen'),
        'labels' => $flashslabels,

        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail'),

        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies' => array('Tags', 'post_tag'), // this is IMPORTANT

        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */

        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

    // Registering your Custom Post Type - Flashs
    register_post_type('flash-briefings', $flashsargs);
}

/* Hook into the 'init' action so that the function */

add_action('init', 'swks_custom_post_type', 0);

// RSS

add_action('init', 'swks_customRSS');
function swks_customRSS(){

    global $wp_rewrite;
    $wp_rewrite->flush_rules();
    add_feed('shoutworks', 'swks_customRSSFunc');
}

function swks_customRSSFunc(){

    load_template(dirname(__FILE__) . '/rss-shoutworks.php');
}

function swks_rssLanguage(){

    update_option('swks_rss_language', 'en');
}
add_action('admin_init', 'swks_rssLanguage');

//flush_rewrite_rules();

register_deactivation_hook(__FILE__, 'swks_deactivation');
function swks_deactivation(){

    $allposts = get_posts(array('post_type' => array('quotes', 'deals', 'flash-briefings'), 'numberposts' => -1, 'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')));

    foreach ($allposts as $eachpost) {

        $args = array( 'post_parent' => $eachpost->ID );
        $post_attachments = get_children($args);
        if($post_attachments) {
            foreach ($post_attachments as $attachment) {
                wp_delete_attachment($attachment->ID, true);
            }
        }
        wp_delete_post($eachpost->ID, true);
    }
    
    $upload_dir = wp_upload_dir();
    $path = trailingslashit( $upload_dir['basedir'] );
    $filename = generateFileName('icon');
    if(file_exists($path."$filename-small.png"))
    {
        removeFilesFromAWS("$filename-small.png",'icon');
        unlink("$path"."$filename-small.png");
        
    }
    if(file_exists($path."$filename-large.png"))
    {
        removeFilesFromAWS("$filename-large.png",'icon');
        unlink("$path"."$filename-large.png");
    }
    if(file_exists($path."$filename-original.png"))
    {
        unlink("$path"."$filename-original.png");
    }
    
    if(file_exists($path."$filename.json"))
    {
        
        removeFilesFromAWS("$filename.json",'json');
        unlink($path."$filename.json");
    }   
    
    unregister_post_type('quotes');
    unregister_post_type('deals');
    unregister_post_type('flash-briefings');
    unregister_post_type('notify');

    delete_option('swks_alex_site_intro');
    delete_option('swks_display_quote');
    delete_option('swks_quote_number');
    delete_option('swks_quote_title');
    delete_option('swks_alexa_notify');
    delete_option('swks_notify_title');
    delete_option('swks_display_flash');
    delete_option('swks_flash_number');
    delete_option('swks_flash_title');
    delete_option('swks_display_blog');
    delete_option('swks_blog_number');
    delete_option('swks_blog_title');
    delete_option('swks_display_deal');
    delete_option('swks_deal_number');
    delete_option('swks_deal_title');
    delete_option('swks_skill_id');
    delete_option('swks_skill_published');
    delete_option('pro_shoutworks_license_key');
    delete_option('shoutworks_activated');
    delete_option('swks_voice_support_email');
    delete_option('swks_skill_name');
    delete_option('swks_skill_invocation');
    delete_option('swks_beta_tester_email');
    delete_option('swks_skill_create_status');
    delete_option('swks_plan');
    delete_option('shoutworks_user_id');
    delete_option('swks_skill_icon');
    
  

    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

/* when user upgrade plan it will update plan in plugin as well on customer's site*/
include_once("class_shotworks_restapi.php");
$objSWKSRestAPI = new swks_rest_api();


add_action( 'add_meta_boxes', 'shoutworks_register_meta_boxes' );
function shoutworks_register_meta_boxes() {
    remove_meta_box( 'postexcerpt', 'notify', 'normal' );
    add_meta_box( 'postexcerpt', __( 'Write the text of your notification here' ), 'shoutworks_post_excerpt_meta_box', 'notify', 'normal', 'high' );
}


function shoutworks_post_excerpt_meta_box( $post ) {
?>
<label class="screen-reader-text" for="excerpt"><?php _e( 'Write the text of your notification here' ) ?></label>
<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
<p><?php
    _e( '(280 character limit)<br /> 
Alexa notifications allow you to send a notification to anyone who has enabled your Alexa skill. Users will receive your notification at the date and time specified in the Publish section on the right-hand side.' );
?></p> 
<p><?php
    _e( 'To set a notification to send in the future, just click “Edit” next to where it says “Publish immediately”.' );
?></p>
<p><?php
    _e( 'When users receive your notification, Alexa will play her “notification sound” and then read your message. To learn more about Alexa notifications and how they work, <a target="_blank" href="https://shoutworks.com/portfolio/alexa-notifications-in-action/">click here.</a>' );
?></p>
<p><?php
    _e( 'This feature is powerful, so please remember that it is meant for informational purposes only. Do not use this to send spam.' );
?></p>
<p><?php
    _e( 'Finally, please do not use any quotation marks in this field (“ or ‘) or your notification may not go through.' );
?></p>
<?php
}