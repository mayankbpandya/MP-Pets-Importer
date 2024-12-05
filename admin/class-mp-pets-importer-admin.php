<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.mayankpandya.com/
 * @since      1.0.0
 *
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/admin
 * @author     Mayank Pandya <mayankbpandya@hotmail.com>
 */
class Mp_Pets_Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mp-pets-importer-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mp-pets-importer-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
            $this->plugin_name,
            'mp_import_pets',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
        );
	}

	/**
    * Setting page HTML 
    */
	public function mp_pp_integration_page_callback() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/mp-pets-importer-admin-display.php';
	}
	
	/**
    * Petpoint Call API 
    */
	public function API_call_to_import_pets() {
		$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
		
		// The base Web Services URL for all Petango Web Services calls
		$urlWSBase      = API_ENDPOINT;
		$urlWSAuthKey = AUTH_KEY;

		// Reference to the current script to develop a complete url
		$selfURL        =  basename(get_permalink()); 

		// Set the error variable to No by default stating there is no error
		$error = "No";
		
		$urlWSComplete  = $this->createAdoptableSearch($urlWSBase,$urlWSAuthKey);

		// HTTP GET command to obtain the data
		$outputWS = file_get_contents($urlWSComplete);
			
		//If outputWS is not a boolean FALSE value
		if ($outputWS !== false){
			// Convert the output to human readable XML
			$xmlWS = simplexml_load_string($outputWS);
			
			// Convert the output to a PHP Array
			//$xmlWSArray = json_decode(json_encode((array)simplexml_load_string($outputWS)),1);
			
			// If the output is not XML, display descriptive error messages
			if ($xmlWS === false) {
				foreach(libxml_get_errors() as $error) {
					$error_detail =  "<br>". $error->message;
				}
				$this->error_log("Failed loading XML: :<br>$error_detail");
			}
		}
		else {
			$this->error_log("The following URL resulted in a FALSE output:<br>$urlWSComplete");
		}
		
		$log_file = fopen($plugin_dir."/petpoint_api_test_resp1.txt", "a") or die("Unable to open file!");
		$txt =  date("F j, Y, g:i a");  
		$txt .= $urlWSBase;
		$txt .= $urlWSAuthKey;
		$txt .= print_r($xmlWS,true);
		fwrite($log_file, "\n". $txt);
		fwrite($log_file, "\n". "============================================================" );
		fclose($log_file);
	
		//Sync posts
		$this->outputAdoptableSearch($urlWSBase,$urlWSAuthKey,$xmlWS);
    }
	
	/**
    * Petpoint Call API To Get List Adopted Pets
    */
	public function API_call_to_find_adopted_pets() {
		
		$adopt_dt = date("F j, Y, g:i a");
		$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
		$log_file = fopen($plugin_dir."/adopted_api_response.txt", "a") or die("Unable to open file!");
		$txt =  "\n=======================".date("F j, Y, g:i a", strtotime($adopt_dt))."=====================================\n";
		$txt .=  "\nAdopted Pets called\n";
		
		
		// The base Web Services URL for all Petango Web Services calls
		$urlWSBase      = API_ENDPOINT;
		$urlWSAuthKey = AUTH_KEY;

		// Reference to the current script to develop a complete url
		//$selfURL        =  basename(get_permalink()); 

		// Set the error variable to No by default stating there is no error
		$error = "No";
		$urlWSComplete  = $this->createAdoptionSearch($urlWSBase,$urlWSAuthKey);
		
		// HTTP GET command to obtain the data
		$outputWS = file_get_contents($urlWSComplete);
		//If outputWS is not a boolean FALSE value
		if ($outputWS !== false){
			$txt .= "\nThe URL Called $urlWSComplete\n";
			// Convert the output to human readable XML
			$xmlWS = simplexml_load_string($outputWS);
				
			// If the output is not XML, display descriptive error messages
			if ($xmlWS === false) {
				foreach(libxml_get_errors() as $error) {
					$error_detail =  "<br>". $error->message;
				}
				$this->error_log("Failed loading XML: :<br>$error_detail");
			}
			// If Output WS has produced a boolean FALSE
		}
		else {
			$txt .= "\nThe URL Resulted False $urlWSComplete\n";
			$this->error_log("The following URL resulted in a FALSE output ".date("F j, Y, g:i a")."\n:$urlWSComplete");
		}
		
		//Sync posts
		fwrite($log_file, "\n". $txt);
		fwrite($log_file, "\n". "_______" );
		fclose($log_file);


		$this->outputAdoptedSearch($urlWSBase,$urlWSAuthKey,$xmlWS,$adopt_dt);
		$this->API_call_to_find_adopted_pets_prev_date();
    }

	/**
    * Petpoint Call API To Get List Adopted Pets For Previous Date
    */
	public function API_call_to_find_adopted_pets_prev_date() {

	if(date('H:i:s') >= date('H:i:s',strtotime("12 AM"))  && date('H:i:s') <= date('H:i:s',strtotime("02 AM")) ){
		
		// between times
		$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
		
		// The base Web Services URL for all Petango Web Services calls
		$urlWSBase      = API_ENDPOINT;
		$urlWSAuthKey = AUTH_KEY;

		// Reference to the current script to develop a complete url
		$selfURL        =  basename(get_permalink()); 

		// Set the error variable to No by default stating there is no error
		$error = "No";
		
		
		$urlWSCompleteOUT = "";
	
		// Input details can be found at
		// https://pethealth.force.com/community/s/article/Webservices-information-guide#adoptable-search
		
		$speciesID 		    = "0"; 			//All Species
		$sex 			    = "A"; 			//All animal genders
		$ageGroup 		    = "All";		//All age groups
		$location		    = "";			//Location string
		$site			    = "";			//Site string
		$stageID		    = "";			//Stage ID string
		$onHold			    = "A";			//Animals on HOLD or Not on HOLD
		$orderBy		    = "Name";		//Sort by Animal Name
		$primaryBreed	    = "All";		//All Breeds (Primary)
		$secondaryBreed     = "All";		//All Breeds (Secondary)
		$specialNeeds	    = "A";			//Special Needs
		$noDogs			    = "A";			//Can live with dogs
		$noCats			    = "A";			//Can live with cats
		$noKids			    = "A";			//Can live with kids
		$date = date('m/d/Y');
		$adoption_dt = date( 'm/d/Y', strtotime( $date . ' -1 day' ) );
		
		$urlWSComplete   = $urlWSBase . "AdoptionList?authKey=$urlWSAuthKey"; 	//Initial URL build
		$urlWSComplete   = "$urlWSComplete&adoptionDate=$adoption_dt"; 			        //Add Species Date to URL
		$urlWSComplete   = "$urlWSComplete&siteID="; 			        //Add Species Date to URL
		
		
		
		//$urlWSComplete  = $this->createAdoptionSearch($urlWSBase,$urlWSAuthKey);
		
		// HTTP GET command to obtain the data
		$outputWS = file_get_contents($urlWSComplete);

			
	//If outputWS is not a boolean FALSE value
	if ($outputWS !== false){

		// Convert the output to human readable XML
		$xmlWS = simplexml_load_string($outputWS);
        
        // Convert the output to a PHP Array
        $xmlWSArray = json_decode(json_encode((array)simplexml_load_string($outputWS)),1);
		

		// If the output is not XML, display descriptive error messages
		if ($xmlWS === false) {
			
			 
			
			foreach(libxml_get_errors() as $error) {
			
				$error_detail .=  "<br>". $error->message;
		
			}

			$this->error_log("Failed loading XML: :<br>$error_detail");

		} 

	// If Output WS has produced a boolean FALSE
	}
    else {
			$this->error_log("The following URL resulted in a FALSE output:<br>$urlWSComplete");
	}

		//Sync posts
		$this->outputAdoptedSearch($urlWSBase,$urlWSAuthKey,$xmlWS,$adoption_dt);
	} else {
		// not between times
	
	}

}

	/**
    * Petpoint Save Auth key
    */
    public function mp_pp_save_auth_key() {
		
        $return_array = array(
            "status" => false,
            "message" => "fail"
            );

        if(!isset($_REQUEST['value']) || $_REQUEST['value']=="") {
            $return_array["message"] = "auth key is null";
            die(wp_json_encode($return_array));
        }
		
		 // For auth key
        $update_api_key = update_option( 'mp_pp_auth_key', $_REQUEST['value'] );
        if($update_api_key==true) {
			$return_array["status"] = true;
            $return_array["message"] = "success";
        }

        die(wp_json_encode($return_array));
	}

	public function createAdoptableSearch($urlWSBaseIN,$urlWSAuthKeyIN) {
    
		$urlWSCompleteOUT = "";
	
		// Input details can be found at
		// https://pethealth.force.com/community/s/article/Webservices-information-guide#adoptable-search
		
		$speciesID 		    = "0"; 			//All Species
		$sex 			    = "A"; 			//All animal genders
		$ageGroup 		    = "All";		//All age groups
		$location		    = "";			//Location string
		$site			    = "";			//Site string
		$stageID		    = "";			//Stage ID string
		$onHold			    = "A";			//Animals on HOLD or Not on HOLD
		$orderBy		    = "";		//Sort by Animal Name
		$primaryBreed	    = "All";		//All Breeds (Primary)
		$secondaryBreed     = "All";		//All Breeds (Secondary)
		$specialNeeds	    = "A";			//Special Needs
		$noDogs			    = "A";			//Can live with dogs
		$noCats			    = "A";			//Can live with cats
		$noKids			    = "A";			//Can live with kids
		
		$urlWSCompleteOUT   = $urlWSBaseIN . "AdoptableSearch?authKey=$urlWSAuthKeyIN"; 	//Initial URL build
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&speciesID=$speciesID"; 			        //Add Species ID to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&sex=$sex"; 						        //Add Gender to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&ageGroup=$ageGroup";				        //Add Age Group to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&location=$location";				        //Add Location to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&site=$site";						        //Add Site to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&stageID=$stageID";					        //Add Stage ID to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&onHold=$onHold";					        //Add On Hold Status to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&orderBy=$orderBy";					        //Add Output Order to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&primaryBreed=$primaryBreed";		        //Add Primary Breed to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&secondaryBreed=$secondaryBreed";	        //Add Secondary Breed to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&specialNeeds=$specialNeeds";		        //Add Special Needs to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&noDogs=$noDogs";					        //Add No Dogs Value to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&noCats=$noCats";					        //Add No Cats Value to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&noKids=$noKids";					        //Add No Kids Value to URL
	
		return $urlWSCompleteOUT;
	
	}
	public function createAdoptionSearch($urlWSBaseIN,$urlWSAuthKeyIN) {
    
		$urlWSCompleteOUT = "";
	
		// Input details can be found at
		// https://pethealth.force.com/community/s/article/Webservices-information-guide#adoptable-search
		
		$speciesID 		    = "0"; 			//All Species
		$sex 			    = "A"; 			//All animal genders
		$ageGroup 		    = "All";		//All age groups
		$location		    = "";			//Location string
		$site			    = "";			//Site string
		$stageID		    = "";			//Stage ID string
		$onHold			    = "A";			//Animals on HOLD or Not on HOLD
		$orderBy		    = "Name";		//Sort by Animal Name
		$primaryBreed	    = "All";		//All Breeds (Primary)
		$secondaryBreed     = "All";		//All Breeds (Secondary)
		$specialNeeds	    = "A";			//Special Needs
		$noDogs			    = "A";			//Can live with dogs
		$noCats			    = "A";			//Can live with cats
		$noKids			    = "A";			//Can live with kids
		$adoption_dt = date('m/d/Y');
		
		$urlWSCompleteOUT   = $urlWSBaseIN . "AdoptionList?authKey=$urlWSAuthKeyIN"; 	//Initial URL build
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&adoptionDate=$adoption_dt"; 			        //Add Species Date to URL
		$urlWSCompleteOUT   = "$urlWSCompleteOUT&siteID="; 			        //Add Species Date to URL
		
	
		return $urlWSCompleteOUT;
	
	}

	public function outputAdoptableSearch($selfURLIN,$urlWSAuthKeyIN,$xmlWSIN) {
		// Count the results of the XML array
		$xmlArrayCount = count($xmlWSIN);
		$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
		
		$log_file = fopen($plugin_dir."/petpoint_api_test_resp2.txt", "a") or die("Unable to open file!");
		$txt =  date("F j, Y, g:i a");
		$txt .= "COUNT". $xmlArrayCount;
		fwrite($log_file, "\n". $txt);
		fwrite($log_file, "\n". "============================================================" );
		fclose($log_file);
	
		// Sets the counter to zero to use to loop through array count
		$counter = 0;
		$pet_id_array = array();
		
		// Print API Response.
		$log_file_1 = fopen($plugin_dir_o."/petpoint_api_response_new.txt", "a") or die("Unable to open file!");
		$txt_1 =  "\n=======================".date("F j, Y, g:i a")."=====================================\n";
		// If the counter value is less than the xml Array Count
		while ($counter < $xmlArrayCount-1) {
			$txt_1 .= "\n". "===========\n" ;
			$txt_1 .= "\n inside while";
			foreach ($xmlWSIN->XmlNode[$counter]->adoptableSearch as $output) {
				//$txt_1 .= "\n".print_r($output,true);
				
				// Mandatory Fields that will always have a value
				$xmlAnimalID        = $output->ID;
				
				$txt_1 .= "\n Animal Id $xmlAnimalID";
				$xmlAnimalDetailsLink = $selfURLIN . '/AdoptableDetails?authkey='. $urlWSAuthKeyIN . '&animalID=' . $xmlAnimalID;
			
				
				$get_animal_details = file_get_contents($xmlAnimalDetailsLink);
				//$txt_1 .= "\n animal details: ".print_r($get_animal_details,true);
				
				if ($get_animal_details !== false){
					
					$xmlWS = simplexml_load_string($get_animal_details);
					
					$xmlWSArray = json_decode(json_encode((array)simplexml_load_string($get_animal_details)),1);
				
					//$txt_1 .= "\n XMLS Array ".print_r($xmlWSArray);
					
					if($xmlWS === false) {
						// If the output is not XML, display descriptive error messages
						$txt_1 .= "\n Failed loading XML";
						foreach(libxml_get_errors() as $error) {
							$txt_1 .= "\n".$error->message;
							//echo "<br>", $error->message;
						}
					}
				}
				else {
					$txt_1 .= "\n The following URL resulted in a FALSE output: $xmlAnimalDetailsLink";
					// echo "The following URL resulted in a FALSE output:<br>$urlWSComplete";
				}
				if(!empty($xmlWSArray)){
					// $txt_1 .= print_r($xmlWSArray,true);
					
					
					$pet_id_array[] = $xmlWSArray['ID'];
					$animal_id = $xmlWSArray['ID'];  //get_post_meta( $id, 'animal_id', true ); 
					$post_title = $xmlWSArray['AnimalName'];
					$pets_content = $xmlWSArray['Dsc'];
					if(!empty($pets_content)){
						$description = str_replace($post_title,'<strong>'.$post_title.'</strong>',$pets_content);
					}else{
						$description = '';
					}
					
					if($xmlWSArray['Species'] == "Cat"){
						$species = 'cats';
					}else if($xmlWSArray['Species'] == "Dog"){
						$species = 'dogs';
					}
					$pets_exits_args = array(
						'post_status' => array('publish','draft'),
						'post_type' => array('cats','dogs'),
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'animal_id',
								'compare' => '=',
								'value' => $animal_id
							),
						),
					);
					//If animal exists then update the data
					$wp_check_if_pets_exits = new WP_Query( $pets_exits_args );
					if ( $wp_check_if_pets_exits->have_posts() ) {
						while ( $wp_check_if_pets_exits->have_posts() ) {
							$wp_check_if_pets_exits->the_post();
							$postid = get_the_ID();
							$poststatus =  'publish';
							//if($xmlWSArray['OnHold'] == 'Yes'){
								//$poststatus =  'draft';
							//}
							$wp_pets_update_post_data = array(
								'ID' => $postid,
								'post_title' => $post_title,
								'post_name' => sanitize_title( $post_title ),
								'post_type' => $species,
								'post_content' => $description,
								'post_status' => $poststatus,
							);
							$txt_1 .= "\n Post Update with ID $postid";
							wp_update_post( $wp_pets_update_post_data );
							$this->update_pet_post_meta( $xmlWSArray, $postid);
							$attachments = get_children( array(
								'post_parent'    => $postid,
								'post_type'      => 'attachment',
								'numberposts'    => 1, // show all -1
								'post_status'    => 'inherit',
								'post_mime_type' => 'image',
								'order'          => 'ASC',
								'orderby'        => 'menu_order ASC'
								)
							);
							foreach ( $attachments as $attachment_id => $attachment ) {
								wp_delete_attachment( $attachment_id,true );
							}
							//UPDATE IMAGES
							$gal_arr_up = array();
							if(!empty($xmlWSArray['Photo1'])){
								if( $xmlWSArray['Photo1'] != 'https://g.petango.com/shared/Photo-Not-Available-cat.gif' 
									&& $xmlWSArray['Photo1'] != 'https://g.petango.com/shared/Photo-Not-Available-dog.gif'){
									if($photo_1_id = $this->upload_image($xmlWSArray['Photo1'],$postid,'post_img')){
										set_post_thumbnail( $postid, $photo_1_id );
										$gal_arr_up[] = $photo_1_id;
									}
								}
							}
							if(!empty($xmlWSArray['Photo2']) 
							&& $gal_arr = $this->upload_image($xmlWSArray['Photo2'],$postid,'gallery')){
								$gal_arr_up[] = $gal_arr;
							}
							if(!empty($xmlWSArray['Photo3']) 
							&& $gal_arr = $this->upload_image($xmlWSArray['Photo3'],$postid,'gallery')){
								$gal_arr_up[] = $gal_arr;
							}
							//Video
							if(!empty($xmlWSArray['VideoID']) 
							&& $gal_arr = $this->upload_image($xmlWSArray['VideoID'],$postid,'gallery')){
								$gal_arr_up[] = $gal_arr;
							}
							//Featured Image
							// if(!empty($xmlWSArray['BannerURL'])){
							// 	//$this->upload_image($xmlWSArray['BannerURL'],$postid,'post_img');
							// }
							update_field('pt_adopt_img_gallery',$gal_arr_up,$postid);
							update_field( 'pt_is_adopted', 0, $postid );
							update_field( 'pt_adopted_date', "", $postid );
					
					}
					wp_reset_postdata();
				}else{
					//If animal not exists then add
					/* Pets post insert array */
					$poststatus =  'publish';
					// if($xmlWSArray['OnHold'] == 'Yes'){
					// 	$poststatus =  'draft';
					//  }
					$wp_pets_post_data = array(
						'post_title' => $post_title,
						'post_status' => $poststatus,
						'post_type' => $species,
						'post_author' => 1,
						'post_content' => $description,
					);

					$wp_pet_post_id = wp_insert_post($wp_pets_post_data); 
					$gal_arr = array();
					$this->update_pet_post_meta( $xmlWSArray, $wp_pet_post_id);

					$txt_1 .= "\n Post Insert with ID $wp_pet_post_id";

					//$is_adop = get_field( 'pt_is_adopted', $wp_pet_post_id );
					//if((empty($is_adop) || $is_adop == '') && $is_adop != false){
						
					//}
					if(!empty($xmlWSArray['Photo1'])){
						if( $xmlWSArray['Photo1'] != 'https://g.petango.com/shared/Photo-Not-Available-cat.gif' 
							&& $xmlWSArray['Photo1'] != 'https://g.petango.com/shared/Photo-Not-Available-dog.gif'){
								if($photo_1_id = $this->upload_image($xmlWSArray['Photo1'],$wp_pet_post_id,'post_img')){
									set_post_thumbnail( $wp_pet_post_id, $photo_1_id );
									$gal_arr[] = $photo_1_id;
								}
							}
						}
						if(!empty($xmlWSArray['Photo2']) 
						&& $gal_arr2 = $this->upload_image($xmlWSArray['Photo2'],$wp_pet_post_id,'gallery')){
							$gal_arr[] = $gal_arr2;
						}
						if(!empty($xmlWSArray['Photo3']) 
						&& $gal_arr2 = $this->upload_image($xmlWSArray['Photo3'],$wp_pet_post_id,'gallery')){
							$gal_arr[] = $gal_arr2;
						}
						//Video
						if(!empty($xmlWSArray['VideoID']) 
						&& $gal_arr2 = $this->upload_image($xmlWSArray['VideoID'],$wp_pet_post_id,'gallery')){
							$gal_arr[] = $gal_arr2;
						}
						update_field('pt_adopt_img_gallery',$gal_arr,$wp_pet_post_id);
						update_field( 'pt_is_adopted', 0, $wp_pet_post_id );
					}
				}
			} //end foreach
			// Increment Counter
			$counter++;
		}  // end while
		fwrite($log_file_1, "\n". $txt_1);
		fwrite($log_file_1, "\n". "============================================================\n" );
		fwrite($log_file_1, "\n". "============================================================\n" );
		fclose($log_file_1);
		//If animal status not adopted and not in adoption list then make draft
		$adopt_pets_exits_args = array(
			'post_status' => 'publish',
			'post_type' => array('cats','dogs'),
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => 'pt_is_adopted',
					'value'   => 0,
					'compare' => '=',
				),
				array(
					'key'     => 'pt_id',
					'value'   => $pet_id_array,
					'compare' => 'NOT IN',
				),
			),
		);
		$wp_check_if_adopt_pets_exits = new WP_Query( $adopt_pets_exits_args ); 
		if ( $wp_check_if_adopt_pets_exits->have_posts() ){
			while ( $wp_check_if_adopt_pets_exits->have_posts() ){
				$wp_check_if_adopt_pets_exits->the_post();
				$post__id = get_the_ID();
				wp_update_post(
					array(
						'ID'    =>  $post__id,
						'post_status'   =>  'draft'
						)
					);
			}
			wp_reset_postdata();
		}
		//If adopted animal status draft then make publish
		$adopted_pets_exits_args = array(
			'post_status' => 'draft',
			'post_type' => array('cats','dogs'),
			'posts_per_page' => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => 'pt_is_adopted',
					'value'   => 1,
					'compare' => '=',
				),
			),
		);
		$wp_check_if_adopted_pets_exits = new WP_Query( $adopted_pets_exits_args );
		if ( $wp_check_if_adopted_pets_exits->have_posts() ){
			while ( $wp_check_if_adopted_pets_exits->have_posts() ) {
				$wp_check_if_adopted_pets_exits->the_post();
				$post__id = get_the_ID();
				wp_update_post(
					array(
						'ID'    =>  $post__id,
						'post_status'   =>  'publish'
					)
				);
			}
			wp_reset_postdata();
		}
	}
	public function outputAdoptedSearch($selfURLIN,$urlWSAuthKeyIN,$xmlWSIN,$adoption_dt) {
		// Count the results of the XML array
		
		$xmlArrayCount = count($xmlWSIN);
		$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
		$log_file = fopen($plugin_dir."/adopted_api_response.txt", "a") or die("Unable to open file!");
		$txt = '';
		//$txt =  "\n=======================".date("F j, Y, g:i a", strtotime($adoption_dt))."=====================================\n";
		if(!isset($xmlWSIN->XmlNode) || empty($xmlWSIN->XmlNode) ){
			$txt .=  "\nNo Adopted Pets\n";
			fwrite($log_file, "\n". $txt);
			fwrite($log_file, "\n". "_______" );
			fclose($log_file);
			return;
		}

		// Sets the counter to zero to use to loop through array count
		$counter = 0;
		$pet_id_array = array();

		
		
		// If the counter value is less than the xml Array Count
		while ($counter < $xmlArrayCount-1) {
			$txt .= "\n". "===============\n" ;
			$txt .= "\n inside while $counter";
			// echo "<br/>inside whilte $counter";
			foreach ($xmlWSIN->XmlNode[$counter]->adoption as $output) {
				// Set default value of non-mandatory fields equal to "Not Defined"
				// $xmlSecondaryBreed = $xmlSpecialNeeds = $xmlNoDogs = $xmlNoCats = $xmlNoKids = "Not Defined";
				$xmlWSArray = "";
				// Mandatory Fields that will always have a value
				$xmlAnimalID        = $output->AnimalID;
				$txt .= "\n Animal Id $xmlAnimalID";
				// echo "<br/>Animal Id $xmlAnimalID";
				$xmlAnimalDetailsLink = $selfURLIN . '/AdoptionDetails?authkey='. $urlWSAuthKeyIN . '&animalID=' . $xmlAnimalID;
				
				// HTTP GET command to obtain the data
				$get_animal_details = file_get_contents($xmlAnimalDetailsLink);
				
				//If outputWS is not a boolean FALSE value
				if ($get_animal_details !== false){
					// Convert the output to human readable XML
					$xmlWS = simplexml_load_string($get_animal_details);
					
					// Convert the output to a PHP Array
					$xmlWSArray = json_decode(json_encode((array)simplexml_load_string($get_animal_details)),1);
					
					// If the output is not XML, display descriptive error messages
					if ($xmlWS === false) {
						$txt .= "\n Failed loading XML";
						// echo "Failed loading XML: ";	
						foreach(libxml_get_errors() as $error) {
							$txt .= "\n Failed loading $error->message";
						}
					}
					// If Output WS has produced a boolean FALSE
				}
				else {
					echo "The following URL resulted in a FALSE output:<br> $xmlAnimalDetailsLink";
					$txt .= "\nThe following URL resulted in a FALSE output:<br> $xmlAnimalDetailsLink";
				}
				
				if(!empty($xmlWSArray)){
					//Print log for each adopted pets
						$txt .= print_r($xmlWSArray,true);
						

						$pet_id_array[] = $xmlWSArray['ID'];
						$animal_id = $xmlWSArray['AnimalID'];  //get_post_meta( $id, 'animal_id', true ); 
						$adoption_date = date('m/d/Y', strtotime($xmlWSArray['AdoptionDate']));
						//Get pet by animal ID
						$pets_exits_args = array(
							'post_status' => array('publish','draft'),
							'post_type' => array('cats','dogs'),
							'posts_per_page' => -1,
							'meta_query'     => array(
								'relation' => 'AND',
								array(
									'key'     => 'pt_id',
									'compare' => '=',
									'value' => $animal_id
								),
							),
						);
						$wp_check_if_pets_exits = new WP_Query( $pets_exits_args );
						if ( $wp_check_if_pets_exits->have_posts() ){
							while ( $wp_check_if_pets_exits->have_posts() ) {
								$wp_check_if_pets_exits->the_post();
								$postid = get_the_ID();
								// echo 'Post found with: '.$postid;
								if(!get_field('pt_is_adopted', $postid)){
									update_field( 'pt_is_adopted', 1, $postid );
									update_field( 'pt_adopted_date', $adoption_date, $postid );
								}
								wp_update_post(
									array(
									'ID'    =>  $postid,
									'post_status'   =>  'publish'
									)
								);
							}
							wp_reset_postdata();
						}
					}else{
						$txt .= "\n empty result";
					}
			} //end foreach
				// Increment Counter
				$counter++;
		}  // end while
			fwrite($log_file, "\n". $txt);
			fwrite($log_file, "\n". "============================================================" );
			fwrite($log_file, "\n". "============================================================" );
			fclose($log_file);
	}
public function update_pet_post_meta($xmlWSArray,$post_id){
		update_field( 'pt_adoption_fee', $xmlWSArray['Price'], $post_id );
		update_field( 'pt_adoption_dob', $xmlWSArray['DateOfBirth'], $post_id );
		update_field( 'pt_id', $xmlWSArray['ID'], $post_id );
		update_field( 'pt_sex', $xmlWSArray['Sex'], $post_id );
		update_field( 'pt_breed', $xmlWSArray['PrimaryBreed'], $post_id );
		update_field( 'pt_size', $xmlWSArray['Size'], $post_id );
		update_field( 'pt_weight', $xmlWSArray['BodyWeight'], $post_id ); 
		
		update_field( 'pt_age', $xmlWSArray['Age'], $post_id );
		update_field( 'pt_age_group', $xmlWSArray['AgeGroup'], $post_id );
		update_field( 'pt_arrival_date', $xmlWSArray['LastIntakeDate'], $post_id );
		update_post_meta( $post_id, 'animal_id',$xmlWSArray['ID'] );
		
}

public function upload_image($url, $post_id, $img_type) {
    include_once(ABSPATH . 'wp-admin/includes/admin.php');
    
    $attachmentId = 0; // Initialize attachment ID
    $att = array();    // Initialize attachment information array
    
    if (!empty($url)) {
        $file = array();
        $file['name'] = basename($url); // Extract file name from URL
        $file['tmp_name'] = download_url($url);

        if (is_wp_error($file['tmp_name'])) {
            // Error downloading the file
            $error_message = $file['tmp_name']->get_error_message();
            error_log("Error downloading image: $error_message");
        } else {
            // File downloaded successfully, try to handle the media
            $attachmentId = media_handle_sideload($file, $post_id);

            if (is_wp_error($attachmentId)) {
                // Error handling media file
                $error_message = $attachmentId->get_error_message();
                error_log("Error handling media file: $error_message");

                // Cleanup: delete the temporary file
                @unlink($file['tmp_name']);
            } else {
                // Media file handled successfully
                $image_url = wp_get_attachment_url($attachmentId);
                $att = array(
                    'pid' => $post_id,
                    'url' => $image_url,
                    'file' => $file['name'],
                    'attach_id' => $attachmentId
                );
            }
        }
    }

    return $att['attach_id'];
}
public function Call_to_trash__adopted_pets(){
	
$keet_adopt_pets_count = sanitize_text_field(get_field('keep_latest_adopted', 'option'));


//Trash cats
$cats_exits_args = array(
	'post_status' => array('publish','draft'),
	'post_type' => 'cats',
	'offset' => $keet_adopt_pets_count,
	'posts_per_page' => 100000,
	'orderby' => 'pt_adopted_date',
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'key'     => 'pt_is_adopted',
			'compare' => '=',
			'value' => 1
		),
	),
);

$wp_check_if_cats_exits = new WP_Query( $cats_exits_args ); 

if ( $wp_check_if_cats_exits->have_posts() ) :
			$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
			$log_file = fopen($plugin_dir."/trash_pet_log.txt", "a") or die("Unable to open file!");
			$txt =  "\n=======================".date("F j, Y, g:i a")."=====================================\n";
			$txt .= "=====Cats====\n" ;
		while ( $wp_check_if_cats_exits->have_posts() ) : $wp_check_if_cats_exits->the_post();
		if($keet_adopt_pets_count > 0 && $keet_adopt_pets_count != ""){
			   wp_trash_post( get_the_id() );
				  //Log for trash pets
				
				$txt .= get_the_id() . ' - ' . get_the_title() ;
				$txt .= "\n";
			
		 }
			 
		endwhile;

		$total_cat_post_count 	= wp_count_posts('cats');
		$cat_publish_post_count = $total_cat_post_count->publish;
		$cat_draft_post_count 	= $total_cat_post_count->draft;
		$cat_trash_post_count 	= $total_cat_post_count->trash;
		
		$txt .=  "\n". " Cat Published Post : " . $cat_publish_post_count . " | Cat Dfraft Posts : " . $cat_draft_post_count . " | Cat Thrashed Post : ".$cat_trash_post_count."\n";

		$txt .=  "\n". "============================================================" ;
		fwrite($log_file, "\n". $txt);
				fwrite($log_file, "\n". "============================================================" );
				fclose($log_file);
				error_log( 'Petpoint API error: ' .$err );
		wp_reset_postdata();
	endif;


	//Trash dogs
$dogs_exits_args = array(
	'post_status' => array('publish','draft'),
	'post_type' => 'dogs',
	'offset' => $keet_adopt_pets_count,
	'posts_per_page' => 100000,
	'orderby' => 'pt_adopted_date',
	'meta_query'     => array(
		'relation' => 'AND',
		array(
			'key'     => 'pt_is_adopted',
			'compare' => '=',
			'value' => 1
		),
	),
);

$wp_check_if_dogs_exits = new WP_Query( $dogs_exits_args ); 

if ( $wp_check_if_dogs_exits->have_posts() ) :
			$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
			$log_file = fopen($plugin_dir."/trash_pet_log.txt", "a") or die("Unable to open file!");
			$txt =  "\n=======================".date("F j, Y, g:i a")."=====================================\n";
			$txt .= "=====Dogs====\n" ;
		while ( $wp_check_if_dogs_exits->have_posts() ) : $wp_check_if_dogs_exits->the_post();
		if($keet_adopt_pets_count > 0 && $keet_adopt_pets_count != ""){
			   wp_trash_post( get_the_id() );
				  //Log for trash pets
				$txt .= get_the_id() . ' - ' . get_the_title() ;
				$txt .= "\n";
			
		 }
			 
		endwhile;

		$total_dog_post_count 	= wp_count_posts('dogs');
		$dog_publish_post_count = $total_dog_post_count->publish;
		$dog_draft_post_count 	= $total_dog_post_count->draft;
		$dog_trash_post_count 	= $total_dog_post_count->trash;

		$txt .=  "\n". " Dog Published Post : " . $dog_publish_post_count . " | Dog Dfraft Posts : " . $dog_draft_post_count . " | Dog Thrashed Post : ".$dog_trash_post_count."\n"; 

		$txt .=  "\n". "============================================================" ;
		fwrite($log_file, "\n". $txt);
				fwrite($log_file, "\n". "============================================================" );
				fclose($log_file);
				error_log( 'Petpoint API error: ' .$err );
		wp_reset_postdata();
	endif;

}
public function print_api_response($pets_array){
	// Print API Response.
	$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
	$log_file = fopen($plugin_dir."/petpoint_api_response.txt", "a") or die("Unable to open file!");
	$txt =  date("F j, Y, g:i a");  
	$txt .= print_r($pets_array,true);
	fwrite($log_file, "\n". $txt);
	fwrite($log_file, "\n". "============================================================" );
	fclose($log_file);
	error_log( 'Petpoint API error: ' .$err );
}

public function error_log($error_message){
	$plugin_dir = WP_PLUGIN_DIR . '/mp-pets-importer';
	$log_file = fopen($plugin_dir."/petpoint_log.txt", "a") or die("Unable to open file!");
	$txt =  date("F j, Y, g:i a");  
	$txt = $error_message;
	fwrite($log_file, "\n". $txt);
	fwrite($log_file, "\n". "============================================================" );
	fclose($log_file);
	error_log( 'Petpoint API error: ' .$err );
}
public function mp_admin_menu() {

    add_menu_page( 
        __( 'Pet Point Integration', 'mp-pp-integration' ), 
        __( 'Pet Point Integration', 'mp-pp-integration' ), 
        'manage_options', 
        'mp-pp-integration', 
        array(new Mp_Pets_Importer_Admin('mp-pets-importer','1.0.0'), 'mp_pp_integration_page_callback')
    );
}

}