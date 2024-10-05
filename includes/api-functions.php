<?php
class Aic_Api {
  private $artworks;

  public function __construct() {
    $this->artworks = [];
  }

  function aic_fetch_artworks($search_val) {
    $this->artworks = [];

    $api_url = 'https://api.artic.edu/api/v1/artworks/search?q=' . urlencode($search_val) . '&fields=id,title,image_id,artist_title,date_start&limit=20';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $error_msg = curl_error($ch);
      curl_close($ch);
      return "cURL error: $error_msg";
    }

    curl_close($ch);

    $data = json_decode($response, true);

    foreach ($data['data'] as $artwork) {
      $this->artworks[] = array(
        'id'            => $artwork['id'],
        'title'         => $artwork['title'],
        'artist_name'   => $artwork['artist_title'],
        'image'         => 'https://www.artic.edu/iiif/2/' . urlencode($artwork['image_id']) . '/full/843,/0/default.jpg',
        'creation_date' => $artwork['date_start'],
      );
    }

    return $this->artworks;
  }

  function aic_fetch_artwork_by_id($id) {
    $api_url = 'https://api.artic.edu/api/v1/artworks/' . urlencode($id) . '?fields=id,title,image_id,artist_title,date_start';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $error_msg = curl_error($ch);
      curl_close($ch);
      return "cURL error: $error_msg";
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['data'])) {
      return array(
        'id'          => $data['data']['id'],
        'title'       => $data['data']['title'],
        'artist_name' => $data['data']['artist_title'],
        'image'       => 'https://www.artic.edu/iiif/2/' . urlencode($data['data']['image_id']) . '/full/843,/0/default.jpg',
        'creation_date' => $data['data']['date_start'],
      );
    }

    return null;
  }
  function aic_get_artworks() {
    return $this->artworks;
  }
}
