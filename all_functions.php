<?php 
/* Check user plan on main site */
function swks_check_plan()
{
	if (get_option('shoutworks_activated') == 'yes')
	{	

		$license_key = get_option('pro_shoutworks_license_key',true);
		$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/checkplan';
		//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/checkplan';
		
		$args = array(
			'method' => 'POST',
			'timeout' => 115,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => ['Content-Type' => 'application/json'],
			'body' => json_encode([
				'key' => $license_key,
			]),
			'cookies' => []
		);

		$response = wp_remote_post($remote_url, $args);
		if($response)
		{
			 if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				$error_array[] = "Something went wrong. Please try again with correct License key.";
			} else {
				$json_values = json_decode($response['body'], true);
				if(isset($json_values['plan']))
					update_option('swks_plan',$json_values['plan']);
				if(isset($json_values['plan']) && strtolower($json_values['plan'])=="free")
				{
					return true;
				}
				else
				{
					if($json_values['error']=="1")
					{
						return $json_values['error_message'];
					}
					return false;
				}
			}
		}
	}
	else
	{
		update_option('swks_plan','FREE');
		//echo "License Not Activated";
		return 1;	
	}
}

/* get plan info from database */
function swks_get_plan_info()
{
	$plan = get_option('swks_plan');
	if(strtolower($plan) == "free")
	{
		return true;	
	}
	else
		return false;
}

/* if plan is  free or license is not activated it will show this upgrade button */
function getUpgradeLink()
{
	return '<div class="upgrade-link"><a href="https://shoutworks.com/upgrade-plan/" target="_blank" class="btn btn-primary">'.__('Upgrade Now',$swks_domian).'</a></div>';
}

/* generate config json file */
function swks_generate_json($oldfile="")
{
	if( !is_shoutworks_activated() ){
		return false;
	}
	$activated = get_option('shoutworks_activated',true);
	$data  = swks_get_plan_info();		
	 if (get_option('swks_display_deal') == "yes" && !$data ) {
		 $title = get_option('swks_deal_title','Deals of the Day');
		$feedArr [] = array('key'=>'deal','title'=>$title,'feed_url'=>site_url().'/feed/shoutworks?type=deals','number'=>get_option('swks_deal_number',true), 'type' => 'text', 'enabled' => true );
	}
	if (get_option('swks_display_flash') == "yes" && !$data ) {
		$title = get_option('swks_flash_title','Flash Briefing');
		$feedArr [] = array('key'=>'flash','title'=>$title,'feed_url'=>site_url().'/feed/shoutworks?type=flashes','number'=>get_option('swks_flash_number',true), 'type' => 'text', 'enabled' => true );
	}
	if (get_option('swks_display_quote') == "yes" && !$data ) {
		$title = get_option('swks_quote_title','Quote of the Day');
		$feedArr [] = array('key'=>'quote','title'=>$title,'feed_url'=>site_url().'/feed/shoutworks?type=quotes','number'=>get_option('swks_quote_number',true), 'type' => 'text', 'enabled' => true );
	}
	if (get_option('swks_display_blog') == "yes") {
		$title = get_option('swks_blog_title','Blog');
		$feedArr [] = array('key'=>'blog','title'=>$title,'feed_url'=>site_url().'/feed/shoutworks?type=blogs','number'=>get_option('swks_blog_number',true), 'type' => 'text', 'enabled' => true );
	}
	
	
	$json_data['skill_name'] = get_option('swks_skill_name',true);
	$json_data['skill_invocation'] = get_option('swks_skill_invocation',true);
	$skill_published = get_option('swks_skill_published',true);
	if($skill_published=="no" || $skill_published =="")
		$skill_published= false;
	else
		$skill_published = true;
	
	$json_data['skill_published'] = $skill_published;
	$json_data['skill_create_status'] = get_option('swks_skill_create_status',true);
	$json_data['shoutworks_user_id'] = get_option('shoutworks_user_id',true);
	
	if($data)
	{
		$json_data['license_state'] = "FREE";
		$json_data['voice_support_enabled'] = false;
	}
	else
	{
		$json_data['license_state'] = "PRO";
		$json_data['voice_support_enabled'] = true;
	}
	
	$lkey = get_option('pro_shoutworks_license_key',true);
	if($lkey=="")
		return;
		
	$json_data['license_key'] = $lkey;
	
	$l_activated = get_option('shoutworks_activated',true);
	if($l_activated == "no" || $l_activated == "")
		$l_activated =false;
	else
		$l_activated=true;
		
	$json_data['license_activated'] = $l_activated;
	$json_data['voice_support_email'] = get_option('swks_voice_support_email',true);
	$json_data['beta_tester_email'] = get_option('swks_beta_tester_email',true);
	$json_data['site_intro'] = get_option('swks_alex_site_intro',true);
	if (get_option('swks_display_podcast') == "yes") {
		//$json_data['podcast'] = get_option('swks_podcast_rss_url',true);
		$custom_title = get_option( 'swks_podcast_title', 'Podcast' );
		$feedArr [] = array('key'=>'podcast', 'title'=> $custom_title,'feed_url'=> get_option('swks_podcast_rss_url') ,'number'=> 5, 'type' => 'audio', 'enabled' => true );
	}
	$json_data['feeds'] = $feedArr;
	
	$filename = generateFileName();
	$json_file = $filename.".json";	
	
	$upload_dir = wp_upload_dir();

	$path = trailingslashit( $upload_dir['basedir'] );
	file_put_contents($path."/".$json_file,json_encode($json_data));	
	$res = uploadJsonToServer($path."/".$json_file,$json_file,'json',$oldfile, $json_data['skill_name']);
	generateFeedJSON( $oldfile );
}
function generateFeedJSON($oldfile=""){
	
	$json_data['skill_invocation'] = get_option('swks_skill_invocation',true);
	$skill_name = get_option('swks_skill_name',true);
	$title = get_option('swks_deal_title','Deals of the Day');
	$feedArr [] = array( 'key'=>'deal', 'title'=>$title );
	
	$title = get_option('swks_flash_title','Flash Briefing');
	$feedArr [] = array( 'key'=>'flash', 'title'=>$title );

	$title = get_option('swks_quote_title','Quote of the Day');
	$feedArr [] = array( 'key'=>'quote','title'=>$title );

	$title = get_option('swks_blog_title','Blog');
	$feedArr [] = array( 'key'=>'blog','title'=>$title );

	$title = get_option( 'swks_podcast_title', 'Podcast' );
	$feedArr [] = array('key'=>'podcast', 'title'=> $title );

	$json_data['feeds'] = $feedArr;

	$filename = generateFileName();
	$json_file = 'feeds-'.$filename.".json";	
	
	$upload_dir = wp_upload_dir();

	$path = trailingslashit( $upload_dir['basedir'] );
	file_put_contents($path."/".$json_file,json_encode($json_data));	
	if( $oldfile ){
		$oldfile = 'feeds-' . $oldfile;
	}
	$res = uploadJsonToServer($path."/".$json_file,$json_file,'json',$oldfile, $skill_name);
}
function uploadJsonToServer($file,$filename,$filetype,$oldfile='', $skill_name)
{
	if($filetype == "json")
	{
		$file_data = file_get_contents( $file );
	}
	else
	{
		$file_data =$file;
	}
	$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/uploadjson';
	//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/uploadjson';
	$args = array(
		'method' => 'POST',
		'timeout' => 115,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => ['Content-Type' =>'application/json', 'Authorization' => base64_encode(get_option('pro_shoutworks_license_key'))],
		'body' => json_encode([
			'file' =>  $file_data,
			'file_name' =>  $filename,	
			'file_type' =>  $filetype,	
			'old_file' =>  $oldfile,
			'skill_name' => $skill_name	
		]),
		'cookies' => []
	);

	$response = wp_remote_post($remote_url, $args);
	if($response)
	{
		 if (is_wp_error($response)) {	 
		 }
		 else
		 {
			 
		}
	 }
}
function swks_resize_image($file, $newfile,$w, $h, $crop=FALSE) {
	
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefrompng($file);
    
    $dst = imagecreatetruecolor($newwidth, $newheight);
    //imagecolorallocatealpha($dst, 0,0,0,127);
   
    imagealphablending($dst, false);
	imagesavealpha($dst,true);
	$transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
	imagefilledrectangle($dst, 0, 0, $newwidth, $newheight, $transparent);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	imagepng($dst, $newfile);
    return $dst;
}


function is_shoutworks_activated(){
	$flag = get_option( 'shoutworks_activated' );
	return $flag == 'yes' ? true : false;
}

function generateFileName($type="json")
{
	$user_id = get_option('shoutworks_user_id',true);
	$skill_name = get_option('swks_skill_name',true);
	if($skill_name!='') 
	{
		$slug = sanitize_title($skill_name);
	}
	else
	{
		if($type=="icon")
			$slug = "skill-icon";
		else
			$slug ="shoutworks-config";
	}
		
	if($user_id!='')
		return $slug."-".$user_id;
	else
		return $slug;
}
function generate_text_voice( $post ){
	$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/texttovoice';
	//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/texttovoice';
	$license_key = get_option('pro_shoutworks_license_key',true);
	$args = array(
		'method' => 'POST',
		'timeout' => 115,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => ['Content-Type' => 'application/json'],
		'body' => json_encode([
			'key' => $license_key,
			'domain' => parse_url(site_url())['host'],
			'text' => substr( $post->post_content, 0, 3000),
			'type' => $post->post_type
		]),
		'cookies' => []
	);

	$response = wp_remote_post($remote_url, $args);
}

function generate_shout_player( $type ){
	$user_id = get_option('shoutworks_user_id',true);
	$file = 'https://shoutworks.com/wp-content/uploads/voice_files/'.$user_id.'_'.$type.'.mp3';	
	//$file = 'https://shout.works/wp-content/uploads/voice_files/'.$user_id.'_'.$type.'.mp3';	
	$file_headers = @get_headers($file);
	if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.0 404 Not Found' ) {
	    $posts = query_posts('post_type='.$type.'&post_status=publish&order=DESC&posts_per_page=1');
	    if( have_posts() ){
	    	$data = new stdClass();
	    	$data->post_type = $type;
	    	$content = '';
			while(have_posts()) : the_post();
				$content = get_the_content();
				if( !empty($content)){
					$content = wp_strip_all_tags( apply_filters('the_content', $content));
				}
			endwhile;
			$data->post_content = $content;
			generate_text_voice( $data );
			printf( '<div class="shout_player"><p><strong>Hear How it Sounds</strong></p> <audio controls data-src="%s"> Your browser does not support the <code>audio</code> element.</audio></div>', $file );
		}
	}
	else {
	    printf( '<div class="shout_player"><p><strong>Hear How it Sounds</strong></p> <audio controls data-src="%s"> Your browser does not support the <code>audio</code> element.</audio></div>', $file );
	}
}

/* upload icon and move to s3 */
add_action('wp_ajax_sw_upload_icon','sw_upload_icon_callback');
add_action('wp_ajax_nopriv_sw_upload_icon','sw_upload_icon_callback');
function sw_upload_icon_callback()
{
	if(isset($_REQUEST['skill_name']) && $_REQUEST['skill_name']!='')
	{
		$filename =  sanitize_title($_REQUEST['skill_name']);
	}
	else
	{
		$skill_name = get_option('swks_skill_name',true);
		$filename = sanitize_title($skill_name);
	}
	
	$user_id = get_option('shoutworks_user_id',true);
	if($user_id > 0)
	{
		$filename = $filename."-".$user_id;	
	}
	
	if(isset($_FILES["sw_skill_icon"]["name"]) && $_FILES["sw_skill_icon"]["name"]!='')
    {
		$error ="";
		//print_r($_FILES["sw_skill_icon"]);
		$ext = explode(".",$_FILES["sw_skill_icon"]["name"]);
		$extension = $ext[count($ext)-1];
		if($extension!='')
		{
			if(strtolower($extension)!='png')
			{
				$arr['msg'] = "FILE_TYPE_ERROR";
				$arr['status'] = "fail";
				echo json_encode($arr);
				exit;
				
			}
			if($error == "")
			{
				if($_FILES["sw_skill_icon"]["size"] > 1048576)
				{
					$arr['msg'] = "FILE_SIZE_ERROR";
					$arr['status'] = "fail";
					echo json_encode($arr);
					exit;
				}
			}
			if($error =="")
			{
				list($width, $height) = getimagesize($_FILES["sw_skill_icon"]["tmp_name"]);
				if($width < 512 && $height < 512) {
					$arr['msg'] = "FILE_DIM_ERROR";
					$arr['status'] = "fail";
					echo json_encode($arr);
					exit;
				}
			}
			if($error == "")
			{
				$upload_dir = wp_upload_dir();
				$path = trailingslashit( $upload_dir['basedir'] );
				
				$name="$filename-original.png";	
				
				$res1  = move_uploaded_file($_FILES["sw_skill_icon"]["tmp_name"], "$path"."$name");
				
				if($width == 512 && $height == 512) {
					copy("$path"."$name", $path."$filename-large.png");
				}
				else
				{
					$large_img = swks_resize_image("$path"."$name",$path."$filename-large.png",512,512,true);
				}
				$small_img = swks_resize_image("$path"."$name",$path."$filename-small.png",108,108,true);
				
				$large_url = trailingslashit( $upload_dir['baseurl'] ).$filename."-large.png";
				$small_url = trailingslashit( $upload_dir['baseurl'] ).$filename."-small.png";
				
				$arr['small_url'] = $small_url;
				$arr['large_url'] = $large_url;
				$arr['status'] = "success";
				echo json_encode($arr);
				exit;
				
			}
		}
		else
		{
			$arr['msg'] = "FILE_TYPE_ERROR";
			$arr['status'] = "fail";
			echo json_encode($arr);
			exit;
			
			
		}
		
	}
	exit;
}
/* upload default icon to s3 if no icon uploaded */
add_action('wp_ajax_sw_save_deafult_icon','sw_save_deafult_icon_callback');
add_action('wp_ajax_nopriv_sw_save_deafult_icon','sw_save_deafult_icon_callback');
function sw_save_deafult_icon_callback()
{
	
	if(isset($_REQUEST['skill_name']) && $_REQUEST['skill_name']!='')
	{
		$filename =  sanitize_title($_REQUEST['skill_name']);
	}
	else
	{
		$skill_name = get_option('swks_skill_name',true);
		$filename = sanitize_title($skill_name);
	}
	
	$user_id = get_option('shoutworks_user_id',true);
	if($user_id > 0)
	{
		$filename = $filename."-".$user_id;	
	}
	
	$upload_dir = wp_upload_dir();
	$path = trailingslashit( $upload_dir['basedir'] );
	$small_url = "";
	$large_url = "";
	if(copy("https://shoutworks.com/resources/shoutworks_small.png", $path."$filename-small.png"))
	{
		$small_url = trailingslashit( $upload_dir['baseurl'] ).$filename."-small.png";
	}
	if(copy("https://shoutworks.com/resources/shoutworks_large.png", $path."$filename-large.png"))
	{
		copy($path."$filename-large.png", $path."$filename-original.png");
		$large_url = trailingslashit( $upload_dir['baseurl'] ).$filename."-large.png";
	}
	$arr['status'] = "success";
	$arr['small_url'] = $small_url;
	$arr['large_url'] = $large_url;
	echo json_encode($arr);
	exit;
		
}

function removeFilesFromAWS($oldfile,$filetype)
{
	$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/removefiles';
	//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/removefiles';
	
	$args = array(
		'method' => 'POST',
		'timeout' => 115,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => ['Content-Type' =>'application/json'],
		'body' => json_encode([
			'old_file' =>  $oldfile,	
			'file_type' =>  $filetype,	
		]),
		'cookies' => []
	);

	$response = wp_remote_post($remote_url, $args);
	
	if($response)
	{
		 if (is_wp_error($response)) {	 
		 }
		 else
		 {
			 
		}
	 }
}

add_filter( 'the_title', 'wpse_75691_trim_words' );

function wpse_75691_trim_words( $title )
{
    $cur_pt = get_post_type();
    if( 'notify' == $cur_pt ) {   
		return wp_trim_words( $title, 100, '' );
    } else {
		return $title;
    }
}

function custom_excerpt_length( $length ) {
	$cur_pt = get_post_type();
	if( 'notify' == $cur_pt )
		return 10;
	else
		return $length;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

add_action( 'publish_notify', 'swks_pushlish_notify_hook' );
function swks_pushlish_notify_hook( $post_id ) {
	
	swks_send_alexa_notification($post_id);

}

add_action( 'publish_future_notify', 'swks_pushlish_future_post_hook' );
function swks_pushlish_future_post_hook( $post_id ) {
	swks_send_alexa_notification($post_id);
}

function swks_send_alexa_notification($post_id)
{
	if( get_option('swks_alexa_notify') == "yes" )
	{
		$user_id = get_option('shoutworks_user_id',true);
		$skill_name = get_option('swks_skill_name',true);
		if($skill_name!='') 
		{
			$slug = sanitize_title($skill_name);
		}
		else
		{
			$slug ="shoutworks";
		}
			
		if($user_id!='')
			$filename =  $slug."-".$user_id."-notification";
		else
			$filename = $slug."-notification";
			
		$json_file = $filename.".json";	
		$upload_dir = wp_upload_dir();

		$path = trailingslashit( $upload_dir['basedir'] );
		$file_path = $path."/".$json_file;
		
		$notifi_data['skill_id']=get_option('swks_skill_id');
		$notifi_data['message_id']=uniqid();
		$dt = get_post_field('post_modified_gmt', $post_id);
		if($dt=='')
		{
			$dt = get_post_field('post_date_gmt', $post_id);
		}
		$dtArr = explode(" ",$dt);
		$notifi_data['message_date']=$dtArr[0]."T".$dtArr[1]."Z";
		$notifi_data['message_title']=get_the_title($post_id);
		$notifi_data['message_body']=strip_tags(get_post_field('post_excerpt', $post_id));
		$isnotify = get_option('swks_alexa_notify',true);
		if($isnotify=="no" || $isnotify =="")
			$isnotify= false;
		else
			$isnotify = true;

		$notifi_data['enabled']=$isnotify;
		file_put_contents($file_path,json_encode($notifi_data));
		
		$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/sendNotification';
		//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/sendNotification';
		$args = array(
			'method' => 'POST',
			'timeout' => 115,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
        	'headers' => ['Content-Type' =>'application/json', 'Authorization' => base64_encode(get_option('pro_shoutworks_license_key'))],
			'body' => json_encode([
				'skill_name' => $skill_name,
				'file' =>  json_encode($notifi_data),
				'file_name' =>  $filename
			]),
			'cookies' => []
		);

		$response = wp_remote_post($remote_url, $args);
		update_post_meta($post_id,'alexa',$response);
		$disable_ids = get_option('disable_front_post_ids');
		$disable_ids[]=$post_id;
		update_option('disable_front_post_ids',array_unique($disable_ids),true);
		
	}	
}

/* disable notify custom post on front end */
add_action(
    'template_redirect',
    function () {
        if (is_singular('notify')) {
           global $wp_query;
           $wp_query->posts = [];
           $wp_query->post = null;
           $wp_query->set_404();
           status_header(404);
           nocache_headers();
        }
    }
);

/* update title */
add_action('wp_ajax_sw_update_title','sw_update_title_callback');
add_action('wp_ajax_nopriv_sw_update_title','sw_update_title_callback');
function sw_update_title_callback()
{
	if(isset($_REQUEST['title']) && $_REQUEST['title']!='' && isset($_REQUEST['type']) && $_REQUEST['type']!='')
	{
		if($_REQUEST['type']=="blog")
		{
			update_option("swks_blog_title",$_REQUEST['title']);
			swks_generate_json();
			echo "1";
		}
		if($_REQUEST['type']=="quote")
		{
			update_option("swks_quote_title",$_REQUEST['title']);
			swks_generate_json();
			echo "1";
		}
		if($_REQUEST['type']=="flash")
		{
			update_option("swks_flash_title",$_REQUEST['title']);
			swks_generate_json();
			echo "1";
		}
		if($_REQUEST['type']=="deal")
		{
			update_option("swks_deal_title",$_REQUEST['title']);
			swks_generate_json();
			echo "1";
		}
		if($_REQUEST['type']=="notify")
		{
			update_option("swks_notify_title",$_REQUEST['title']);
			echo "1";
		}
		if($_REQUEST['type']=="podcast")
		{
			update_option("swks_podcast_title",$_REQUEST['title']);
			echo "1";
		}
		exit;
		
	}
	else
	{
		echo "0";
		exit;	
	}
}

function change_post_menu_label() {
    global $menu;
    global $submenu;
    $title = get_option("swks_blog_title","Blogs");
    $menu[5][0] = $title;
    $submenu['edit.php'][5][0] = $title;
    $submenu['edit.php'][10][0] = 'Add '.$title;
    echo '';
}

function change_post_object_label() {

	global $wp_post_types;
	$title = get_option("swks_blog_title","Blogs");
	$labels = &$wp_post_types['post']->labels;
	$labels->name = $title;
	$labels->singular_name = $title;
	$labels->add_new = 'Add '.$title;
	$labels->add_new_item = 'Add '.$title;
	$labels->edit_item = 'Edit '.$title;
	$labels->new_item = $title;
	$labels->view_item = 'View '.$title;
	$labels->search_items = 'Search '.$title;
	$labels->not_found = 'No '.$title.' found';
	$labels->not_found_in_trash = 'No '.$title.' found in Trash';
}
add_action( 'admin_init', 'change_post_object_label' );
add_action( 'admin_menu', 'change_post_menu_label' );

/* update skill id in database on create skill response */
add_action('wp_ajax_sw_update_skill_id','swks_update_skill_id_callback');
add_action('wp_ajax_nopriv_sw_update_skill_id','swks_update_skill_id_callback');
function swks_update_skill_id_callback()
{
	if(isset($_REQUEST['skill_id']) && $_REQUEST['skill_id']!='')
	{
		update_option('swks_skill_id',$_REQUEST['skill_id']);
		$arr['status'] = "success";
		echo json_encode($arr);
		exit;
	}	
}

//swks_get_credentials_from_skill_id();
function swks_get_credentials_from_skill_id()
{
	$curl = curl_init();
	$skillid = get_option('swks_skill_id');
	echo $url = "https://api.amazonalexa.com/v1/skills/$skillid/credentials";
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  
	));

	$response = curl_exec($curl);
	print_r($response);
	curl_close($curl);
	echo $response;

}

function skws_update_feed_name()
{
	$feed_names[ 'blog_title' ]   = get_option('swks_blog_title');
	$feed_names[ 'notify_title' ] = get_option('swks_notify_title');
	$feed_names[ 'flash_title' ]  = get_option('swks_flash_title');
	$feed_names[ 'quote_title' ]  = get_option('swks_quote_title');
	$feed_names[ 'deal_title' ]   = get_option('swks_deal_title');
	$feed_names[ 'podcast_title' ]= get_option('swks_podcast_title');
                        
	$feed_names[ 'skill_name' ] =  get_option('swks_skill_name',true);
	$feed_names[ 'invocation_name' ] =  get_option('swks_skill_invocation');
	$remote_url = 'https://shoutworks.com/wp-json/shoutworx/v1/update_feed_name';
	//$remote_url = 'https://shout.works/wp-json/shoutworx/v1/uploadjson';
	$args = array(
		'method' => 'POST',
		'timeout' => 115,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => ['Content-Type' =>'application/json', 'Authorization' => base64_encode(get_option('pro_shoutworks_license_key'))],
		'body' => json_encode( $feed_names ),
		'cookies' => []
	);

	$response = wp_remote_post($remote_url, $args);
	//print_r($response);
	if($response)
	{
		 if (is_wp_error($response)) {	 
		 }
		 else
		 {
			 
		}
	 }
}
?>
