<?php
class WpCms {
		
	//Update some of the wp-admin CSS
	function update_css()
	{
		$blog = get_option('blog_functionality');
		?>
		<style type="text/css">
		<?php if($blog != 1){ ?>
		#dashboard_quick_press, 
		#dashboard_recent_drafts, 
		#dashboard_recent_comments,
		#dashboard_right_now .table,
		#dashboard_right_now p.sub		
		{
			display:none;
		}
		<?php } ?>
		#menu-tools a {
			padding-left:25px !important;
		}
		</style>
		<?
	}
	
	//Add the jQuery scripts to wp-admin pages
	function add_javascript()
	{
		$blog = get_option('blog_functionality');
		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			<?php if($blog != 1){ ?>
				//Remove blog related options
				var pages = jQuery('#menu-pages');
				jQuery('#menu-posts').remove();
				jQuery('#menu-comments').remove();
				jQuery('#menu-pages').remove();
				jQuery('#menu-media').before(pages);
				jQuery('#menu-pages').addClass('menu-top-first');
				jQuery('#menu-links').addClass('menu-top-last');
				//Fix favorite actions
				jQuery('#favorite-first').html('<a href="page-new.php">New Page</a>');
				jQuery('#favorite-inside').remove();
				jQuery('#favorite-toggle').remove();
				//Fix media list
				jQuery('td.parent').html('<em style="color:#999;">Only available in blog mode.</em>');
				//Fix reading options
				jQuery('input[name="show_on_front"]:first').after('Unable to Show ');
				jQuery('input[name="show_on_front"]:first').remove();
				jQuery('#page_for_posts').before('<span style="color:#999;">Unable to show posts when Blog mode is disabled.</span>').remove();
			<?php } ?>

			//Update Settings menu if tools/plugins is selected
			if(jQuery('#menu-tools').hasClass('wp-menu-open') || jQuery('#menu-plugins').hasClass('wp-menu-open')){
				jQuery('#menu-settings').addClass('wp-menu-open');
				jQuery('#menu-settings').addClass('wp-has-current-submenu');
				jQuery('#menu-settings a.wp-has-submenu').addClass('wp-menu-open');
				jQuery('#menu-settings a.wp-has-submenu').addClass('wp-has-current-submenu');
				jQuery('#menu-settings a.wp-has-submenu').addClass('current');
			}
			
			//Move tools and plugins menus to Settings
			var menu_tools = jQuery('#menu-tools ul').html();
			jQuery('#menu-tools').remove();
			jQuery('#menu-settings .wp-submenu ul:first').prepend('<li><a href="#">Tools</a><ul id="menu-tools">' + menu_tools + '</ul></li>');
			var menu_plugins = jQuery('#menu-plugins ul').html();
			jQuery('#menu-plugins').remove();
			jQuery('#menu-settings .wp-submenu ul:first').prepend('<li><a href="#">Plugins</a><ul id="menu-tools">' + menu_plugins + '</ul></li>');
		});
		</script>
		<?php
	}
	
	//Initial activation actions
	function activate()
	{
		global $wpdb;
		
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
		
		//Add Blog Functionality Option
		add_option("blog_functionality", 0);
		update_option('blog_functionality', 0);
	}
}
?>