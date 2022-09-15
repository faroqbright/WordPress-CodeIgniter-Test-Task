<?php
/**
* Plugin Name: Wp Advance Cakes
* Plugin URI: https://www.google.com/
* Description: Using APIs you can manage the cakes
* Version: 1.0
* 
* 
**/





// register jquery and style on initialization
add_action('admin_init', 'wp_adv_cake_register_script');
function wp_adv_cake_register_script() {
    $myCssFileSrc = plugins_url( '/css/wp_adv_cake_admin_style.css', __FILE__ );
    wp_register_style( 'wp_adv_cake_admin_style', $myCssFileSrc);

}

// use the registered jquery and style above
add_action('admin_enqueue_scripts', 'wp_adv_cake_enqueue_style');

function wp_adv_cake_enqueue_style(){
	//style
   	wp_enqueue_style( 'wp_adv_cake_admin_style' );
   	wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js');
   	wp_register_script( "wp-adv-admin-cake", plugins_url( '/js/wp-adv-admin-cake.js', __FILE__ ), array('jquery') );
   	wp_localize_script( 'wp-adv-admin-cake', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
   	wp_enqueue_script( 'wp-adv-admin-cake' );
   	
   
}


add_action("wp_enqueue_scripts","enqueue_theme_side_files");
function enqueue_theme_side_files(){
	wp_register_style( 'wp_adv_cake_theme_style', plugins_url( '/css/wp_adv_cake_theme_style.css', __FILE__ ) );
	wp_enqueue_style( 'wp_adv_cake_theme_style' );
	//jquery 
	wp_enqueue_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js');
	wp_register_script( "wp-adv-cake-js", plugins_url( '/js/wp-adv-cake.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'wp-adv-cake-js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_script( 'wp-adv-cake-js' );
}

function wp_adv_cake_get_json_response($msg='',$status='',$status_code=404,$data=array())
{
	$response = array();
	$response['status'] =$status;
	$response['status_code'] =$status_code;
	$response['msg'] =$msg;
	$response['data'] =$data;
	return json_encode($response);
}

add_action( 'admin_menu', 'extra_post_info_menu' ); 
if( !function_exists("extra_post_info_menu") ) {
	function extra_post_info_menu(){
	    $page_title = 'WordPress Cakes';
	    $menu_title = 'Advance Cakes';
	    $capability = 'manage_options';
	    $menu_slug  = 'advance-product-api';
	    $function   = 'advance_cakes_api_info_page';
	    $icon_url   = 'dashicons-media-code';
	    $position   = 4;
	    add_menu_page( $page_title,$menu_title,$capability,$menu_slug,$function,$icon_url,$position );
	} 
}
if( !function_exists("wp_cake_rest_api_call")){
	function wp_cake_rest_api_call($url,$method,$entityBody='')
	{
		if(''!==$url){
			$headers = array(
				'Content-Type: multipart/form-data'
			);
			$api_url = "https://demo.appcrates.net/codeigniter/codeigniter_4/index.php/api/".$url;
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $api_url);
			if( 'DELETE' == $method ){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			}elseif( 'POST' == $method ){
				curl_setopt($ch, CURLOPT_POST, 1);
				if( '' != $entityBody ){
					curl_setopt($ch, CURLOPT_POSTFIELDS, $entityBody);
				}
			}elseif( 'PUT' == $method ){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				if('' != $entityBody){
					curl_setopt($ch, CURLOPT_POSTFIELDS, $entityBody );
				}
			}
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}
	}
}

add_action("wp_ajax_wp_adv_save_single_page_url", "wp_adv_save_single_page_url_callback");
	
if(!function_exists("wp_adv_save_single_page_url_callback")){
	function wp_adv_save_single_page_url_callback(){
		$single_page_url = $_POST['single_page_url'];
		add_option( 'wp_adv_single_page_url', $single_page_url, '', 'yes' ); 
	}
}
if(!function_exists("get_all_cakes")){

	function get_all_cakes() { 
		$response = json_decode( wp_cake_rest_api_call('cake/get',"GET") );
		$single_page_url = get_option( 'wp_adv_single_page_url' );
		
		if($single_page_url==false){
			echo "<h3 class='text-center text-danger'>Please add single page url in admin side</h3>";
			return false;
		}else{

			ob_start();
			if(!empty($response)){ ?>
				<div class="row wp_adv_all_cakes">
					<?php foreach ($response->data as $key => $cake) { ?>
						<div class="col-12 col-md-4">
							<div class="card" style="width: 18rem;">
							  <img class="card-img-top" src="https://www.cakes.com.pk/assets/cakes/IMG-20191121-WA0000.jpg" alt="Card image cap">
							  <div class="card-body">
							    <h5 class="card-title"><?php echo $cake->name; ?></h5>
							    <h6 class="card-subtitle mb-2 text-muted">Type:<?php echo $cake->type; ?></h6>
							    <p class="card-text">Cake description here</p>
							    <a href="<?php echo $single_page_url;?>?id=<?php echo $cake->id; ?>" class="btn btn-primary">View Cake ($<?php echo $cake->price; ?> )</a>
							  </div>
							</div>
						</div>
				<?php } ?>
				</div>
				<?php
			}
			return ob_get_clean();
		}
		

	}
}
// register shortcode
add_shortcode('wp_adv_all_cakes', 'get_all_cakes');

if( !function_exists("get_a_cake_by_id")){
	function get_a_cake_by_id()
	{

		if(isset($_GET['id'])){
			$cake_id = $_GET['id'];
			$url = 'cake/get_cake_info/'.$cake_id;
			$response = json_decode( wp_cake_rest_api_call($url,"GET") );
			ob_start();
			if( 1 == $response->success && !empty($response->data)  ){ ?>
				<div class="wp_adv_cake">
					<div class="row wp_adv_cake_row">
						<div class="col-12 col-md-4 p-2 single-cake-info">
							<h3 class="bold"><?php echo $response->data->name; ?> </h3>
							<p> Type: <?php echo $response->data->type; ?> </p>
							<h3>$<?php echo $response->data->price; ?></h3>
							<button type="button" class="button wp_adv_cake_green_btn  wp_adv_cake_buy_now" data-toggle="modal" data-target="#wp_adv_cake_buy_now_modal">Buy Now</button>
							
						</div>
						<div class="col-12 col-md-8">
							<img src="https://www.cakes.com.pk/assets/cakes/IMG-20191121-WA0000.jpg">
						</div>
					</div>
				</div>
				<div class="modal fade" id="wp_adv_cake_buy_now_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h2 class="modal-title" id="exampleModalLabel"><?php echo $response->data->name; ?></h2>
				        <p>Type: <?php echo $response->data->type; ?></p>
				        <p>$<?php echo $response->data->price; ?></p>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				      	<i class="fa fa-check-circle text-center wp_adv_cake_buy_tick"></i>
				      	<h3 class="text-center">Thank you for purchasing the cake! Enjoy!</h3>
				        
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				      </div>
				    </div>
				  </div>
				</div>
				

		<?php }else{
			echo "<h3 class='text-center'>No cake found</h3>";
			}
		}else{
			echo "<h3 class='text-center'>Id is not set</h3>";
		}
		 return ob_get_clean();
	}
}

add_shortcode('wp_adv_get_cake', 'get_a_cake_by_id');


if( !function_exists("advance_cakes_api_info_page") ) {
	function advance_cakes_api_info_page(){ 
	    echo "<h1>WordPress Advance Cakes</h1>";
	    $web_url = home_url(); 
	    
	    ?>
	    <h2>WP Advance cakes shortcodes for HTML </h2>
	    <p> Create 2 pages for cakes 1- All cakes 2- single cake</p>
	    <input type="text" name="wp_adv_single_page_url" class="wp_adv_single_page_url"><button id="wp_adv_single_page_url_save">Save</button>
	    <p>Please save single cake page url here to acheive all functionalities of the plugin</p>
	    <div class="row">
	    	<div class="col-12 col-md-6"><h3> 1- [wp_adv_all_cakes] </h3></div>
	    	<div class="col-12 col-md-6"><p>Place this in your page and you can see all cakes in grid format.</p></div>
	    	<div class="col-12 col-md-6"><h3> 2- [wp_adv_get_cake]</h3></div>
	    	<div class="col-12 col-md-6"><p>Place this in a page where you want to show single cake information but remmber to add "id" as a query paramter. for example:  https://your-page-url/?id=1</p> </div>
	    	<div class="col-12 col-md-6"><h3> 3- [wp_adv_search_cake] </h3></div>
	    	<div class="col-12 col-md-6"><p>Place this in your page and where you want to add search cake input.</p></div>
	    </div>
	    
	    <?php
	}
} 

add_shortcode('wp_adv_search_cake', 'wp_adv_search_cake_input');

add_action("wp_ajax_wp_adv_serach_cakes_ajax", "wp_adv_serach_cakes_ajax_callback");
add_action("wp_ajax_nopriv_wp_adv_serach_cakes_ajax", "wp_adv_serach_cakes_ajax_callback");
function wp_adv_serach_cakes_ajax_callback(){
	//$_POST['c_type']
	if(isset($_POST['c_name']) && '' != $_POST['c_name'] ){
		$url = 'cake/search/';
		$body_data = array('search_with' =>  $_POST['c_name']);
		$response = json_decode( wp_cake_rest_api_call($url,"POST",$body_data) );
		if( 1 == $response->success && !empty($response->data)  ){
			$single_page_url = get_option( 'wp_adv_single_page_url' );
			$response->single_page_url = $single_page_url;
			echo wp_adv_cake_get_json_response('Cakes found','success',200,$response);
			die();
		}else{
			echo wp_adv_cake_get_json_response('No cakes found','failure',404);
			die();
		}
	}else{
		echo wp_adv_cake_get_json_response('Input some name ','failure',404);
		die();
	}
}

if( !function_exists("wp_adv_search_cake_input") ) {
	function wp_adv_search_cake_input(){ 
		$single_page_url = get_option( 'wp_adv_single_page_url' );
		if($single_page_url==false){
			echo "<h3 class='text-center text-danger'>Please add single page url in admin side</h3>";
			return false;
		}
		ob_start();
	    ?>
	    <input type="search" id="search_cake_name" name="search_cake_name" placeholder="Enter cake name">
	    
	    <!-- <input type="search" id="search_cake_type" name="search_cake_type" placeholder="Enter cake type"> -->
	    <button id="search_cake_btn">Search</button>
	    <p id="search_cake_error"></p>
	    <?php
	    return ob_get_clean();
	}
} 

?>



