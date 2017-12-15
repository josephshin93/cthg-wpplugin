<?php
/*
Plugin Name: JBS Categories Display
Description: This is a plugin to create a shortcode that will display links to all the categories that apply to a specific post (which includes posts and pages if the 'Post Tags and Categories for Pages' plugin is being using) in a hierarchical form. This shortcode will also display all parent categories of the categories that apply to the specificed post as well. The 'post_id' attribute for the shortcode must be included, the post_id of a page or post can be seen by in the url when editing a page or post as 'post.php?post=number' where number is the post id. An example looks something like this: [jbscategories post_id=109]
Author: Joseph Shin
Version: 1.0
*/

function jbs_categories_shortcode( $atts, $content = null ) {
    $a = shortcode_atts(array(
        'post_id' => 0
    ),$atts);

    if ($a['post_id'] == 0) {
    	return 'No Categories for this Page/Post';
    } else {
		$categories = get_the_terms($a['post_id'], 'category');
		if (!$categories) {
			return 'No Categories for this Page/Post';
		}
		$parent_categories = array();
		$includes = array();
		foreach ($categories as $cat) {
			$includes[$cat->term_id] = $cat;
			$parent_categories = array_merge($parent_categories, get_ancestors($cat->term_id, 'category'));
		}
		foreach ($parent_categories as $cat) {
			$includes[$cat] = get_term($cat);
		}

		$all_categories = get_categories(array('hide_empty' => 0));
		$excludes = array();
		foreach ($all_categories as $cat) {
			if (!array_key_exists($cat->term_id, $includes)) {
				array_push($excludes, $cat->term_id);
			}
		} 

		ob_start();
		?>

		<style> 
			.jbs-categories {
				padding: 10px 0;
			}
			.jbs-categories li {
				list-style-type: none;
			}
		</style>
		
		<div class="jbs-categories">
			<?php wp_list_categories(array('exclude' => $excludes, 'hide_empty' => 0, 'title_li' => __( '' ))); ?>
		</div>

		<?php


		return ob_get_clean();
    }
}
function jbs_add_shortcode() {
	add_shortcode('jbscategories', 'jbs_categories_shortcode');	
}

add_action('plugins_loaded', 'jbs_add_shortcode');
