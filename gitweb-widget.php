<?php
/* 
   Plugin Name: Gitweb Widget 
   Version: 0.0.1
   Plugin URI: http://git.pedrosilva.pt/?p=gitweb-widget.git
   Description: Show git projects made public via a gitweb instance in Wordpress
   Author: Pedro Silva <pedro.alex.silva@gmail.com>
   Author URI: http://www.pedrosilva.pt/
   License: GPL3

   Copyright 2010 Pedro Silva <pedro.alex.silva@gmail.com>

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

  function fetch_url_as_string( $url ) {

    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, $url ); 
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
    $output = curl_exec( $ch );

    curl_close( $ch );

    return $output;
  }

  function fetch_project_index( $url, $instance ) {

    $repos = preg_grep( '/\S+/', preg_split( '/\n/', self::fetch_url_as_string( $url.'/?a=project_index' ) ) );

    if ( isset( $instance['owner'] ) ) {
      $owner = preg_replace( '/\s+/', '\+', $instance['owner'] );
      $repos = preg_grep( "/$owner/i", $repos );
    }

    foreach ( $repos as &$repo ) {

      $repo = preg_split( '/\s+/', $repo );

      if ( isset( $instance['description'] ) ) {
	$atom_url = $url . '/?p=' . $repo[0] . ';a=atom';
	$atom_xml = self::fetch_url_as_string( $atom_url );
      
	$description = preg_replace( '#.*\<subtitle\>([^<]+)\</subtitle\>.*#si', '$1', $atom_xml );
	$repo[2] = $description;
      }
      else {
	$repo[2] = $repo[0];
      }
    }

    return $repos;
  }

  /** constructor */
  function GitwebWidget() {
    if ( !extension_loaded( 'curl' ) ) {
      if ( !dl( 'curl.so' ) ) {
        exit;
      }
    }
    parent::WP_Widget( false, $name = 'Gitweb Widget' );
  }

  /** @see WP_Widget::widget */
  function widget( $args, $instance ) {
    extract( $args );
    $title = apply_filters( 'widget_title', $instance['title'] );
    
    echo $before_widget;

    if ( $title ) {
      echo $before_title . $title . $after_title;
    }
    
    $base_url = $instance['url'];
    $repos = self::fetch_project_index( $base_url, $instance );

    sort( $repos );

    echo '<ul>';

    foreach ( $repos as &$repo ) {
      $name = preg_replace( '/\.git$/', '', $repo[0] );
      echo '<li><a title="' . $repo[2] . '" href="' . $base_url . '/?p=' . $repo[0] . ';a=summary">' . $name . "</a></li>\n";
    }

    echo '</ul>';

    echo $after_widget;
  }

  /** @see WP_Widget::update */
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['url'] = strip_tags( $new_instance['url'] );
    $instance['owner'] = strip_tags( $new_instance['owner'] );
    $instance['description'] = strip_tags( $new_instance['description'] );

    $instance['url'] = preg_replace( '/\/$/', '', $instance['url'] );

    return $instance;
  }

  /** @see WP_Widget::form */
  function form( $instance ) {
    $title = esc_attr( $instance['title'] );
    $url = esc_attr( $instance['url'] );
    $owner = esc_attr( $instance['owner'] );
    $description = esc_attr( $instance['description'] );
?>

<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

<p><label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('Gitweb URL:'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo $url; ?>" /></label></p>

<p><label for="<?php echo $this->get_field_id( 'owner' ); ?>"><?php _e('Owner (optional):'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'owner' ); ?>" name="<?php echo $this->get_field_name( 'owner' ); ?>" type="text" value="<?php echo $owner; ?>" /></label>

<p><label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e('Fetch project descriptions (slow!):'); ?> <input  id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="checkbox" value="<?php echo $description; ?>" /></label></p>
<?php 

  }
} // class GitwebWidget

// register GitwebWidget widget
add_action( 'widgets_init', create_function( '', 'return register_widget("GitwebWidget");' ) );

?>
