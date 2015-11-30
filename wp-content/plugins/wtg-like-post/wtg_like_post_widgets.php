<?php
class MostLikedPostsWidget extends WP_Widget
{
     function MostLikedPostsWidget() {
	     load_plugin_textdomain( 'wtg-like-post', false, 'wtg-like-post/lang' );
          $widget_ops = array('description' => __('Widget to display most liked posts for a given time range.', 'wtg-like-post'));
          parent::WP_Widget(false, $name = __('Most Liked Posts', 'wtg-like-post'), $widget_ops);
     }

     /** @see WP_Widget::widget */
     function widget($args, $instance) {
          global $MostLikedPosts;
          $MostLikedPosts->widget($args, $instance); 
     }
    
     function update($new_instance, $old_instance) {         
          if ( $new_instance['title'] == '' ) {
               $new_instance['title'] = __('Most Liked Posts', 'wtg-like-post');
          }
		
		if ( $new_instance['time_range'] == '' ){
               $new_instance['time_range'] = 'all';
          }
		
          return $new_instance;
     }
    
     function form($instance) {
          global $MostLikedPosts;
		$time_range_array = array(
							'all' => __('All time', 'wtg-like-post'),
							'1' => __('Last one day', 'wtg-like-post'),
							'2' => __('Last two days', 'wtg-like-post'),
							'3' => __('Last three days', 'wtg-like-post'),
							'7' => __('Last one week', 'wtg-like-post'),
							'14' => __('Last two weeks', 'wtg-like-post'),
							'21' => __('Last three weeks', 'wtg-like-post'),
							'1m' => __('Last one month', 'wtg-like-post'),
							'2m' => __('Last two months', 'wtg-like-post'),
							'3m' => __('Last three months', 'wtg-like-post'),
							'6m' => __('Last six months', 'wtg-like-post'),
							'1y' => __('Last one year', 'wtg-like-post')
						);
		
		$show_types = array('most_liked' => __('Most Liked', 'wtg-like-post'), 'recent_liked' => __('Recently Liked', 'wtg-like-post'));
          ?>
		<p>
               <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wtg-like-post'); ?>:<br />
               <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title'];?>" /></label>
          </p>		
		<p>
               <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show', 'wtg-like-post'); ?>:<br />
               <input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" style="width: 40px;" value="<?php echo $instance['number'];?>" /></label>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('time_range'); ?>"><?php _e('Time range', 'wtg-like-post'); ?>:<br />
			<select name="<?php echo $this->get_field_name('time_range'); ?>" id="<?php echo $this->get_field_id('time_range'); ?>">
			<?php
			foreach ( $time_range_array as $time_range_key => $time_range_value ) {
				$selected = ($time_range_key == $instance['time_range']) ? 'selected' : '';
				echo '<option value="' . $time_range_key . '" ' . $selected . '>' . $time_range_value . '</option>';
			}
			?>
			</select>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('show_count'); ?>"><input type="checkbox" id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" value="1" <?php if($instance['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show like count', 'wtg-like-post'); ?></label>
          </p>
		<input type="hidden" id="wtg-most-submit" name="wtg-submit" value="1" />	   
          <?php
     }
}

class WtgMostLikedPosts
{
     function WtgMostLikedPosts() {
          add_action( 'widgets_init', array(&$this, 'init') );
     }
    
     function init() {
          register_widget("MostLikedPostsWidget");
     }
     
     function widget($args, $instance = array() ) {
		global $wpdb;
		extract($args);
	    
		$title = $instance['title'];
		$show_count = $instance['show_count'];
		$time_range = $instance['time_range'];
		//$show_type = $instance['show_type'];
		$order_by = 'ORDER BY like_count DESC, post_title';
		
		if( (int)$instance['number'] > 0 ) {
			$limit = "LIMIT " . (int)$instance['number'];
		}
		
		$widget_data  = $before_widget;
		$widget_data .= $before_title . $title . $after_title;
		$widget_data .= '<ul class="wtg-most-liked-posts">';
	
		$show_excluded_posts = get_option('wtg_like_post_show_on_widget');
		$excluded_post_ids = explode(',', get_option('wtg_like_post_excluded_posts'));
		
		if( !$show_excluded_posts && count( $excluded_post_ids ) > 0 ) {
                        $where = "AND post_id NOT IN (" . implode(',', array_map('absint', explode(',', get_option('wtg_like_post_excluded_posts')))) . ")";
		}
		
		if ( $time_range != 'all' ) {
			$last_date = GetWtgLastDate($time_range);
                        $where .= $wpdb->prepare(" AND date_time >= %s", $last_date);
		}
		
		//getting the most liked posts
		$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wtg_like_post` L, {$wpdb->prefix}posts P ";
		$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0 $where GROUP BY post_id $order_by $limit";
		$posts = $wpdb->get_results($query);

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				$post_title = stripslashes($post->post_title);
				$permalink = get_permalink($post->post_id);
				$like_count = $post->like_count;
				
				$widget_data .= '<li><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a>';
				$widget_data .= $show_count == '1' ? ' (' . $like_count . ')' : '';
				$widget_data .= '</li>';
			}
		} else {
			$widget_data .= '<li>';
			$widget_data .= __('No posts liked yet.', 'wtg-like-post');
			$widget_data .= '</li>';
		}
   
		$widget_data .= '</ul>';
		$widget_data .= $after_widget;
   
		echo $widget_data;
     }
}

$MostLikedPosts = new WtgMostLikedPosts();

//recently like posts
class RecentlyLikedPostsWidget extends WP_Widget
{
     function RecentlyLikedPostsWidget() {
	     load_plugin_textdomain( 'wtg-like-post', false, 'wtg-like-post/lang' );
          $widget_ops = array('description' => __('Widget to show recently liked posts.', 'wtg-like-post'));
          parent::WP_Widget(false, $name = __('Recently Liked Posts', 'wtg-like-post'), $widget_ops);
     }

     function widget($args, $instance) {
          global $RecentlyLikedPosts;
          $RecentlyLikedPosts->widget($args, $instance); 
     }
    
     function update($new_instance, $old_instance) {         
          if($new_instance['title'] == ''){
               $new_instance['title'] = __('Recently Liked Posts', 'wtg-like-post');
          }
         
          if($new_instance['number'] == ''){
               $new_instance['number'] = 10;
          }
         
          return $new_instance;
     }
    
     function form($instance) {
          global $RecentlyLikedPosts;
          ?>
		<p>
               <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wtg-like-post'); ?>:<br />
               <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title'];?>" /></label>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of entries to show', 'wtg-like-post'); ?>:<br />
               <input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" style="width: 40px;" value="<?php echo $instance['number'];?>" /> <small>(<?php echo __('Default', 'wtg-like-post'); ?> 10)</small></label>
          </p>
		<input type="hidden" id="wtg-recent-submit" name="wtg-submit" value="1" />	   
          <?php
     }
}

class RecentlyLikedPosts
{
     function RecentlyLikedPosts() {
          add_action( 'widgets_init', array(&$this, 'init') );
     }
    
     function init() {
          register_widget("RecentlyLikedPostsWidget");
     }
     
     function widget( $args, $instance = array() ) {
		global $wpdb;
		extract($args);
		
		$recent_id = array();
		$where = '';
		$title = $instance['title'];
		$number = $instance['number'];
		
		$widget_data  = $before_widget;
		$widget_data .= $before_title . $title . $after_title;
		$widget_data .= '<ul class="wtg-most-liked-posts wtg-user-liked-posts">';
	
		$show_excluded_posts = get_option('wtg_like_post_show_on_widget');
		$excluded_post_ids = explode(',', get_option('wtg_like_post_excluded_posts'));
		
		if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
                        $where = "AND post_id NOT IN (" . implode(',', array_map('absint', explode(',', get_option('wtg_like_post_excluded_posts')))) . ")";
		}
		
		// Get the post IDs recently voted
		$recent_ids = $wpdb->get_col("SELECT DISTINCT(post_id) FROM `{$wpdb->prefix}wtg_like_post`
							    WHERE value > 0 $where GROUP BY post_id ORDER BY MAX(date_time) DESC");

		if(count($recent_ids) > 0) {
                        $where = "AND post_id IN(" . implode(",", array_map('absint', $recent_ids)) . ")";
			
			// Getting the most liked posts
			$query = $wpdb->prepare("SELECT post_id, post_title FROM `{$wpdb->prefix}wtg_like_post` L, {$wpdb->prefix}posts P 
					WHERE L.post_id = P.ID AND post_status = 'publish' $where GROUP BY post_id
                                        ORDER BY FIELD(post_id, " . implode(",", array_map('absint', $recent_ids)) . ") ASC LIMIT %d", $number);

			$posts = $wpdb->get_results($query);
		 
			if(count($posts) > 0) {
				foreach ($posts as $post) {
					$post_title = stripslashes($post->post_title);
					$permalink = get_permalink($post->post_id);
					
					$widget_data .= '<li><a href="' . $permalink . '" title="' . $post_title . '">' . $post_title . '</a></li>';
				}
			}
		} else {
			$widget_data .= '<li>';
			$widget_data .= __('No posts liked yet.', 'wtg-like-post');
			$widget_data .= '</li>';
		}

		$widget_data .= '</ul>';
		$widget_data .= $after_widget;

		echo $widget_data;
     }
}

$RecentlyLikedPosts = new RecentlyLikedPosts();
?>
