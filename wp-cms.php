<?php
/*
Plugin Name: WP-CMS
Plugin URI: http://www.gilbertpellegrom.co.uk/projects/wp-cms/
Description: Makes the Wordpress admin section act more like a CMS.
Version: 2.1
Author: Gilbert Pellegrom
Author URI: http://www.gilbertpellegrom.co.uk

==== VERSION HISTROY ====
V1.0 	- Release Version
V1.1 	- Added blog functionality and settings page. Fixes to submenu.
V1.2 	- Changes to code structure. Added wp-cms.class.php. Changed the
		  way that the main menu's are redefined.
V1.2.1 	- Bug Fix: Logout url set to demo.gilbertpellegrom.co.uk page fixed.
V2.0 	- Major upgrade to the new Wordpress 2.7 UI.
V2.1 	- Fixed "home" page creation when pages already exist. Fixed <?php tag
		  bug. Fixed Plugins and Tools menu's.

==== COPYRIGHT ====
Copyright 2009  Gilbert Pellegrom  (email : drummermanny@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
====================
*/

require("wp-cms.class.php");
global $wp_cms;
$wp_cms = &New WpCms;

add_action('admin_menu', 'wp_cms');
add_action('admin_head', array( &$wp_cms, 'update_css' ), 100 );
add_action('admin_head', array( &$wp_cms, 'add_javascript' ) );
register_activation_hook(__FILE__, array( &$wp_cms, 'activate' ) );

function wp_cms() {	
	//Add options page
	add_options_page('WP-CMS Settings', 'WP-CMS', 8, __FILE__, 'wp_cms_settings');
}

function wp_cms_settings(){
	global $wpdb;
	
	if(isset($_POST['blog_func'])){
		if($_POST['blog_func'] == 1){
			//Set Latest Posts on front page
			update_option('show_on_front', 'posts');
			update_option('blog_functionality', 1);
		} else {
			//Check for pages
			$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='page'");
			if(sizeof($pages) < 1){
				$wpdb->query("INSERT INTO $wpdb->posts (id, post_title, post_date, post_name, post_content, post_type) VALUES ('', 'Home', '".date('Y-m-d H:i:s')."', 'home', 'Hi and thanks for using WP-CMS! You can delete this page if you want or change the WP-CMS settings to have a blog again.', 'page')");
			}
			//Set static page
			if(get_option('page_on_front') == ''){
				$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type='page'");
				update_option('page_on_front', $id);
			} 
			update_option('show_on_front', 'page');
			update_option('page_for_posts', '');
			//Turn off Blog functionality
			update_option('blog_functionality', 0);
		}
		?>
		<script type="text/javascript">
		window.location = "<?php echo $_SERVER['PHP_SELF'].'?page=wp-cms/wp-cms.php&update=true'; ?>";
		</script>
		<?php
	}

	if($_GET['update']) echo '<div class="updated"><p><strong>'.__('Settings saved').'</strong></p></div>';
	
	$blog = get_option('blog_functionality');
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br/></div>
		<h2>WP-CMS Settings</h2>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page=wp-cms/wp-cms.php'; ?>">
		<table class="form-table">
			<tbody>
				<tr>
					<th>Blog Functionality</th>
					<td>
						<p><input id="blog-on" type="radio" name="blog_func" value="1" <?php if($blog == 1){ echo 'checked="checked"'; } ?>  />
						<label for="blog-on">Yes I would like to have a blog on my website.</label></p>
						<p><input id="blog-off" type="radio" name="blog_func" value="0" <?php if($blog == 0){ echo 'checked="checked"'; } ?>  />
						<label for="blog-off">No I just want to use pages.</label></p>
						<p style="color:#999;">Remember to check your <a href="options-reading.php">Reading Settings</a> after changing these options.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="Save Changes" class="button-primary" /></p>
		</form>
	</div>
	<?php
}
?>