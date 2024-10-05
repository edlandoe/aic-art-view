<?php
if (! defined('ABSPATH')) {
  exit;
}

require_once plugin_dir_path(__FILE__) . '../includes/api-functions.php';

class AIC_Admin_Pages {
  function __construct() {
    add_action('admin_menu', array($this, 'aic_options_page'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
  }

  function enqueue_admin_assets() {
    wp_enqueue_style('aic-admin-styles', plugin_dir_url(__FILE__) . '../assets/admin.css');
    wp_enqueue_script('aic-admin-script', plugin_dir_url(__FILE__) . '../assets/admin.js', array());
  }

  function aic_options_page_HTML() {
    $aic_api = new Aic_Api();
    $search_query = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['search_query'])) {
      $search_query = sanitize_text_field($_POST['search_query']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['create_post_type']) and isset($_POST['selected_artwork'])) {
      $selected_artwork_id = sanitize_text_field($_POST['selected_artwork']);
      $artwork_data = $aic_api->aic_fetch_artwork_by_id($selected_artwork_id);

      if ($artwork_data) {
        $image_url = $artwork_data['image'];

        $sanitized_title = str_replace(' ', '-', $artwork_data['title']);
        $sanitized_title = preg_replace('/[^\w\-]/', '', $sanitized_title);
        $sanitized_title = strtolower($sanitized_title);
        $image_name = $sanitized_title . '.jpg';

        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $image_path = $upload_dir['path'] . '/' . $sanitized_title . '.jpg';
        file_put_contents($image_path, $image_data);

        if (file_exists($image_path)) {
          $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $image_name,
            'post_mime_type' => 'image/jpeg',
            'post_title'     => sanitize_file_name($image_name),
            'post_status'    => 'inherit'
          );

          $attachment_id = wp_insert_attachment($attachment, $image_path);

          require_once(ABSPATH . 'wp-admin/includes/image.php');
          $attachment_data = wp_generate_attachment_metadata($attachment_id, $image_path);
          wp_update_attachment_metadata($attachment_id, $attachment_data);
        }

        $post_data = array(
          'post_title'  => $artwork_data['title'],
          'post_status' => 'publish',
          'post_type'   => 'artwork',
          'meta_input'  => array(
            'artist_name'   => $artwork_data['artist_name'],
            'creation_date' => $artwork_data['creation_date']
          )
        );
      }
      $post_id = wp_insert_post($post_data);

      if (!is_wp_error($post_id)) {
        set_post_thumbnail($post_id, $attachment_id);
        echo '<p>Artwork created sucessfully with ID: ' . esc_html($post_id) . '</p>';
      } else {
        echo '<p>Image file not found: ' . esc_html($image_path) . '</p>';
      }
    }
?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <h3> Welcome to the AIC view, you can search for any artwork in the Art Institute of Chicagos digital library and display it on your site!</h3>
      <form action="" method="POST" class="query-input">
        <input type="text" name="search_query" placeholder="Search for artworks"></input>
        <button type="submit">Fetch Artworks</button>
      </form>

      <form action="" method="POST">
        <div class="artwork-container">
          <?php
          $aic_api->aic_fetch_artworks($search_query);
          $artworks = $aic_api->aic_get_artworks();

          if (isset($artworks) and is_array($artworks)) {
            foreach ($artworks as $artwork) {
          ?>
              <div class="artwork-card" data-artwork-id=<?php echo esc_attr($artwork['id']); ?>>
                <input type="radio" name="selected_artwork" value="<?php echo esc_attr($artwork['id']); ?>" class="artwork-radio">
                <img class="artwork-thumbnail" src="<?php echo $artwork['image']; ?>" />
                <p class="artwork-title"> <?php echo esc_html($artwork['title']); ?></p>
              </div>
          <?php
            }
          } else {
            echo '<p>No Artworks Found</p>';
          }
          ?>
        </div>
        <?php submit_button("Select Artwork", "primary", 'create_post_type'); ?>
      </form>
    </div>
<?php
  }

  function aic_options_page() {
    add_menu_page(
      'AIC Art View',
      'AIC Art View',
      'manage_options',
      'aic',
      [$this, 'aic_options_page_HTML']
    );
  }
}

$aic_admin_pages = new AIC_Admin_Pages();
