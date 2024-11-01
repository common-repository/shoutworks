<?php 

/* update plan when user upgrade plan it will request on site with API call and upate plan*/
/* {site_url}/wp-json/shoutworks/v1/updateplan 
 * http://localhost/shoutworks/wp-json/shoutworks/v1/updateplan?plan=PLAN_499
 * */
 
class swks_rest_api{
	public function __construct()
    {
		add_action( 'rest_api_init',array($this,'swks_update_shoutworks_plan'));
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
		add_action( 'elementor/editor/after_save', array( $this, 'save_elemento_content' ) );
	}
	
	public function swks_update_shoutworks_plan() {
		register_rest_route( 'shoutworks/v1', '/updateplan', [
			'methods' => 'GET',
			'callback' =>  array($this,'updateUserplan')
		]);
	}

	public function updateUserplan( WP_REST_Request $request ) {
		$parameters = $request->get_params();
		/* plan values : FREE or PLAN_499 or PLAN_999 */
		$plan = $parameters['plan'];
		if (!$plan) {
			return new WP_REST_Response( [
				"status" => "Invalid"
			], 400);
		}
		else
		{
			update_option('swks_plan',$plan);
			swks_generate_json();
			return new WP_REST_Response(["succes"=>1,"success_message"=>"Plan upgraded successfully."]);
		}
	}

	public function save_post( $post_id, $post, $update ){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	        return;

	    # Not sure if necessary when using save_post_POST-TYPE
	    if ( 'revision' == $post->post_type )
	        return; 

	    // Only set for post_type = post!
	    if ( !in_array( $post->post_type , array( 'flash-briefings', 'quotes', 'deals', 'post' ) ) ) {
	        return;
	    }
	    $type = $post->post_type;
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
			$data->post_content = preg_replace("/\s+/", " ", $content);
			generate_text_voice( $data );
	    	wp_reset_postdata();
	    }
	}

	public function save_elemento_content( $post_id ){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	        return;

		$post = get_post( $post_id );

	    # Not sure if necessary when using save_post_POST-TYPE
	    if ( 'revision' == $post->post_type )
	        return; 

	    // Only set for post_type = post!
	    if ( !in_array( $post->post_type , array( 'flash-briefings', 'quotes', 'deals', 'post' ) ) ) {
	        return;
	    }
		$type = $post->post_type;
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
			$data->post_content = preg_replace("/\s+/", " ", $content);
			generate_text_voice( $data );
	    	wp_reset_postdata();
	    }
	}

}