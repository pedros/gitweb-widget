<?php
/* 
   Plugin Name: Gitweb Widget 
   Version: 0.0.1
   Plugin URI: http://psilva.ath.cx/gitweb/?p=gitweb-widget.git
   Description: Show git projects made public via a gitweb instance in Wordpress
   Author: Pedro Silva <pedro.alex.silva@gmail.com>
   Author URI: http://psilva.ath.cx

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or 
   (at your option) any later version. 

   This program is distributed in the hope that it will be useful, 
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details. 

   You should have received a copy of the GNU General Public License
   along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * GitwebWidget Class
 */
class GitwebWidget extends WP_Widget {

  function fetch_project_index($url) {
    // create curl resource 
    $ch = curl_init();

    // set url 
    curl_setopt($ch, CURLOPT_URL, $url); 

    //return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

    // $output contains the output string 
    $repos = preg_grep("/\S+/", preg_split("/\n/", curl_exec($ch)));

    // close curl resource to free up system resources 
    curl_close($ch);

    foreach ($repos as &$repo) {
      $repo = preg_split("/\s+/", $repo);
    }

    return $repos;
  }

  /** constructor */
  function GitwebWidget() {
    parent::WP_Widget(false, $name = 'Gitweb Widget');
  }

  /** @see WP_Widget::widget */
  function widget($args, $instance) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    
    echo $before_widget;
    if ( $title ) {
      echo $before_title . $title . $after_title;
    }
    
    $base_url = $instance['url'];
    $name = preg_replace('/\.git$/', '', $base_url);

    $repos = GitwebWidget::fetch_project_index($base_url.'/?a=project_index');
    sort($repos);
    echo '<ul>';
    foreach ($repos as &$repo) {
      echo '<li><a href="'.$base_url.'/?p='.$repo[0].';a=summary'.'">'.$repo[0].'</a></li>'."\n";
    }
    echo '</ul>';
    echo $after_widget;
  }

  /** @see WP_Widget::update */
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['url'] = strip_tags($new_instance['url']);

    return $instance;
  }

  /** @see WP_Widget::form */
  function form($instance) {
    $title = esc_attr($instance['title']);
    $url = esc_attr($instance['url']);
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Url:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" /></label></p>
<?php 

  }
} // class GitwebWidget

// register GitwebWidget widget
add_action('widgets_init', create_function('', 'return register_widget("GitwebWidget");'));

?>
