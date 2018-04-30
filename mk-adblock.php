<?php
/*
Plugin Name: MK Adblock
Description: If your visitors uses Adblock, hide your elements, videos and show alert block!
Plugin URI: https://mustafakucuk.net/
Author: Mustafa KÜÇÜK
Author URI: https://mustafakucuk.net/mk-adblock
Version: 1.0
License: GNU
*/

class MK_Adblock{

  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'MK_Adblock_Menu' ) );
    add_action( 'wp_footer', array( $this, 'MK_Adblock_Display' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'MK_Adblock_Admin_Enqueue' ) );
    add_action( 'wp_footer', array( $this, 'MK_Adblock_Enqueue' ) );
    register_activation_hook( __FILE__, array( $this, 'MK_Adblock_Default_Settings' ) );    
    define( 'mk_adblock_version', '1.0' );
    define( 'mk_adblock_sr', 'https://wordpress.org/support/plugin/mk-adblock' );
  }
  
  public function MK_Adblock_Menu()
  {
    add_menu_page( 'MK Adblock Settings', 'MK Adblock', 'manage_options', 'mk-adblock', array( $this, 'MK_Adblock_Settings_Page' ) );
  }

  public function MK_Adblock_Admin_Enqueue(){
    wp_enqueue_style( 'mk_adblock_admin_style', plugins_url( 'assets/css/mk_adblock_admin.css', __FILE__ ));
  }
  
  public function MK_Adblock_Enqueue(){
    wp_enqueue_style( 'mk_adblock_style', plugins_url( 'assets/css/mk_adblock.css', __FILE__ ));
    wp_enqueue_script( 'mk_adblock', plugins_url( 'assets/js/mk_adblock.js', __FILE__ ), array( 'jquery' ) );
  }

  public function MK_Adblock_Default_Settings(){
    add_option('mk_adblock_title', 'Oops!');
    add_option('mk_adblock_description', 'Adblock detected, please close adblock for see content.');
  }

  public function MK_Adblock_Settings_Page()
  {
    if( $_POST ) {
      if ( !isset( $_POST['mk_adblock_update'] ) || !wp_verify_nonce( $_POST['mk_adblock_update'], 'mk_adblock_update' ) ) {
        echo '<div class="mkNotice">Error! Please try again.</div>';
      }else{
        $mk_adblock_title   = sanitize_text_field( $_POST["mk_adblock_title"] );
        $mk_adblock_description = sanitize_text_field( $_POST["mk_adblock_description"] );
        $mk_adblock_elements = sanitize_text_field( $_POST["mk_adblock_elements"] );
        $mk_adblock_hide_video = sanitize_text_field( $_POST["mk_adblock_hide_video"] );
        $mk_adblock_required = sanitize_text_field( $_POST["mk_adblock_required"] );
        update_option( 'mk_adblock_title', $mk_adblock_title );
        update_option( 'mk_adblock_description', $mk_adblock_description );
        update_option( 'mk_adblock_elements', $mk_adblock_elements );
        update_option( 'mk_adblock_hide_video', $mk_adblock_hide_video );
        update_option( 'mk_adblock_required', $mk_adblock_required );
        echo '<div class="mkNotice">Settings Saved!</div>';
      }
    }  
    ?>
    <div class="wrap wrapLeft">
    <div id="wpnlh_navbar"><span> MK Adblock <small><?php echo mk_adblock_version; ?></small></span></div>
    <div id="wpnlh_content">
      <div id="wpnlh_content_block">
        <form method="post">
          <label for="mk_adblock_title">Block Title: </label>
          <input type="text" class="mkInput" id="mk_adblock_title" placeholder="Title" name="mk_adblock_title" value="<?php echo get_option('mk_adblock_title'); ?>"/>
          <br>
          <label for="mk_adblock_description">Block Description: </label>
          <textarea type="text" class="mkTextarea" id="mk_adblock_description" placeholder="Description" name="mk_adblock_description"><?php echo get_option('mk_adblock_description'); ?></textarea>
          <br>
          <label for="mk_adblock_elements">Elements to hide: <small>#ex or .ex - Separate with commas</small></label>
          <input type="text" class="mkInput" id="mk_adblock_elements" placeholder="Ex: #comments,.relatedPosts" name="mk_adblock_elements" value="<?php echo get_option('mk_adblock_elements'); ?>"/>
          <br>
          <input type="checkbox" id="mk_adblock_hide_video" name="mk_adblock_hide_video" <?php echo ( get_option('mk_adblock_hide_video') == 'on' ? 'checked' : '' ); ?>/>
          <label for="mk_adblock_hide_video">Hide Videos?</label>
          <br>
          <input type="checkbox" id="mk_adblock_required" name="mk_adblock_required" <?php echo ( get_option('mk_adblock_required') == 'on' ? 'checked' : '' ); ?>/>
          <label for="mk_adblock_required">Required?</label>
          <br>
          <br>
          <?php wp_nonce_field( 'mk_adblock_update', 'mk_adblock_update' ); ?>
          <input type="submit" id="submit" name="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
        </form>
      </div>
    </div>
    </div>
    <div class="wrap wrapRight">
    <div id="wpnlh_navbar"><span> MK Adblock <small><?php echo mk_adblock_version; ?></small></span></div>
    <div id="wpnlh_content">
      <div id="wpnlh_content_block">
        For your plugin and theme needs: <a href="mailto:hello@mustafakucuk.net">hello@mustafakucuk.net</a>
        <br>
        <hr>
        <div class="title">Links</div>
        <a href="<?php echo mk_adblock_sr; ?>/reviews/#new-post" target="_blank">Add Review</a>
        <br>
        <a href="<?php echo mk_adblock_sr; ?>/" target="_blank">Support</a>
        <hr>
      </div>
    </div>
    </div>
    <?php
  }

  public function MK_Adblock_Display()
  {
    ?>
    <script>
      jQuery(document).ready(function(){
        function adBlockDetected() {
          var MK_Adblock = '<div class="mk_adblock">';
          <?php echo( get_option( 'mk_adblock_required' ) != 'on' ? 'MK_Adblock += \'<div class="mk_adblock_close">X</div>\'' : '' ); ?>

          MK_Adblock += '<div class="mk_adblock_title"><?php echo get_option('mk_adblock_title'); ?></div>';
          MK_Adblock += '<div class="mk_adblock_desc"><?php echo get_option('mk_adblock_description'); ?></div>';
          MK_Adblock += '</div>';
          <?php
            if( get_option( 'mk_adblock_hide_video' ) == 'on' ){
              echo"jQuery( 'iframe' ).wrap( '<div class=\"mk_adblock_iframe\"></div>' );";
            }
          ?>
          jQuery( "<?php echo ( get_option( 'mk_adblock_hide_video' ) == 'on' ? '.mk_adblock_iframe,' : '' ).get_option('mk_adblock_elements'); ?>" ).wrap('<div class="mk_adblock_content"></div>').hide();
          jQuery( '.mk_adblock_content' ).append(MK_Adblock);
          jQuery( '.mk_adblock_close' ).on( 'click', function(){
            jQuery(this).parent().hide();
            jQuery(this).parent().parent().children().not('.mk_adblock').show();
          })
        }
        var fuckAdBlock = new FuckAdBlock({
          checkOnLoad: true,
          resetOnEnd: true
        });
        fuckAdBlock.onDetected(adBlockDetected);
      });
    </script>
    <?php
  }
}

new MK_Adblock;