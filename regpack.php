<?php
/**
 * Plugin Name: Regpack
 * Description: Embed Regpack on any page of your website easily with this plugin.
 * Version: 0.1
 * Text Domain: regpack-plugin
 * Author: Regpacks
 */

// register settings
 function regpack_register_settings() {
   add_option( 'regpack_embed_script', '');
   register_setting( 'regpack_options_group', 'regpack_embed_script', 'myplugin_callback' );
}
add_action( 'admin_init', 'regpack_register_settings' );

// create an options page 
function regpack_register_options_page() {
  add_options_page('Regpack Settings', 'Regpack Settings', 'manage_options', 'regpack', 'regpack_options_page');
}
add_action('admin_menu', 'regpack_register_options_page');

function regpack_options_page(){
?>
    <div>
    <h1>Online Registration On Your Website by Regpack</h1>
    <form method="post" action="options.php">
    <?php settings_fields( 'regpack_options_group' ); ?>
    <p>Put registration front and center on your website, not a 3rd party.<br>
     Create a customized registration and online payment flow directly on your website easily with Regpack's
     <a href="https://www.regpacks.com/blog/online-registration-software/" target="_blank"> online registration software.</a></p>
    <p>For current Regpack clients, please head to "Project Settings" in your Regpack account.<br>
    From there, click the "Embedding" tab and copy the embed code for your project in the field below.<br>
    You can read more about this <a href="https://www.regpacks.com/help/project-settings/embedding-regpack/" target="_blank">here</a>.</p>
    <p>Once you save the code below, a shortcode will be created, <strong>[regpack-shortcode]</strong>, that you can then insert on any page or pages on your website.</p>
    <p>If you are NOT a Regpack client, feel free to email <a href="mailto:sales@regpacks.com">sales@regpacks.com.</a><br> to learn more about how you can integrate your 
    <a href="https://www.regpacks.com/" target="_blank">online registration process</a> into your website.</p>
    
    <table>
    <tr valign="top">
    <td>
        <textarea id="regpack_embed_script" name="regpack_embed_script" rows="10" cols="50"/><?php echo get_option('regpack_embed_script'); ?></textarea>
        </td>
    </tr>
    </table>
    <?php  submit_button( 'Save Settings' ); ?>
    </form>
    <div id="regpack-submit-text" style="font-weight:bold"></div>
    </div>
<?php
} 

global $right_format;
$right_format = false;

if ( isset( $_GET['settings-updated'] ) ) {
    // checking the embed code that the user inserted
    //
    global $right_format;
    $embed_code = get_option('regpack_embed_script');
    
    if($embed_code){
        if( preg_match('/^<div id="regpack_iframe_container"><div id="regpack_noscript_div"><a href="https:\/\/www\.regpacks\.com\/">Registration Software<\/a>\| <a href="https:\/\/www\.regpacks\.com\/ ">Online Registration Software<\/a>\| <a href="https:\/\/www\.regpacks\.com\/solutions\/">Registration tools<\/a>\| <a href="https:\/\/www\.regpacks\.com\/solutions\/integrated-payments\/">Online Payment Processing Software<\/a><\/div><script id="regpack_iframe" type="text\/javascript" data-cfasync="false" src="https:\/\/www\.regpack\.com\/reg\/regpack_rp2_iframe\.js\?gid=\d+"><\/script><\/div>$/', $embed_code)){
            add_action( 'admin_notices', 'regpack_success_notice' );
            $right_format = true;
            /*echo '<script type="text/javascript">';
            echo 'console.log("good code: '.$right_format.'");';
            echo '</script>'; */
        }else{
            add_action( 'admin_notices', 'regpack_error_notice' );
            $right_format = false;
            /*echo '<script type="text/javascript">';
            echo 'console.log("bad code: '.$right_format.'");';
            echo '</script>'; */
        }
    }
    
 }

 function regpack_success_notice(){
     ?>
    <div class="updated notice" style="font-weight:bold;">
        <p>The Regpack shortcode has been updated successfully.</p>
    </div>
    <?php
 }

 function regpack_error_notice(){
     ?>
    <div class="updated error" style="font-weight:bold;">
        <p>The embeded code is not in the right format, please check again.</p>
    </div>
    <?php
 }

// add the regpack shortcode
add_shortcode('regpack-shortcode', 'regpack_shortcode_function');

function regpack_shortcode_function($atts){
    global $right_format;
    if($right_format){
        return get_option('regpack_embed_script');
    }else{
        return '';
    }
    
}

//
// once deactivating the plugin, it removes the regpack regpack_shortcode 
function regpack_deactivation() {
    remove_shortcode('regpack-shortcode');
    echo '<script type="text/javascript">';
    echo 'console.log("deactivate regpack");';
    echo '</script>';
}
register_deactivation_hook( __FILE__, 'regpack_deactivation' );


//
//add settings link in plugin list page  
add_filter( 'plugin_action_links_regpack/regpack.php', 'regpack_settings_link' );
function regpack_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'regpack',
		get_admin_url() . 'options-general.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
