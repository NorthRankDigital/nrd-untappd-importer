<?php

/**
 * @package NRDUntappdImporter
 */

namespace Inc\Api\External;

class UntappdApi
{

  public $settings;
  public $api_base_url;
  public $api_credentials;

  public function __construct()
  {
    $this->api_base_url = 'https://business.untappd.com/api/v1/';
  }  

  public function getMenuItems($menu_id)
  {

    $api_endpoint = $this->api_base_url . 'menus/' . $menu_id . '?full=true';
    $api_response = $this->getRequest($api_endpoint);

    if (isset($api_response['error'])) {
      return $api_response;
    }

    if (isset($api_response['menus']) && count($api_response['menus']) == 0) {
      return ['error' => 'Menu has no items.'];
    }

    $result = [];
    $menuID = $api_response['menu']['id'];
    $sections = $api_response['menu']['sections'];

    foreach ($sections as $section) {
      $sectionName = $section['name'];
      $items = $section['items'];

      foreach ($items as $item) {
        $containers = '';
        $lastElement = end($item['containers']);
        foreach($item['containers'] as $container)
        {
          $containers .= $container['name'];
          if ($container !== $lastElement) {
            $containers .= ', ';
          }
        }
        $result[] = [
          'menuID' => $menuID,
          'sectionName' => $sectionName,
          'name' => $item['name'],
          'description' => $item['description'],
          'style' => $item['style'],
          'abv' => $item['abv'],
          'containers' => $containers,
          'untappd_item_id' => $item['id']
        ];
      }
    }

    return $result;

  }

  public function getMenus()
  {
    $menu_response = [];
    $location_response = $this->getLocationID();

    if(isset($location_response['error'])) {
      return $location_response;
    }

    $api_endpoint = $this->api_base_url . 'locations/' . $location_response['location_id'] . '/menus';
    $api_response = $this->getRequest($api_endpoint);

    if (isset($api_response['error'])) {
      return $api_response;
    }   
    
    if (count($api_response['menus']) == 0)
    {
      return ['error' => 'No menus setup.'];
    }

    foreach($api_response['menus'] as $value) {
      $menu_response[$value['id']] = $value['name'];
    }

    return $menu_response;
  } 
  
  public function getLocationID() 
  {
    $api_endpoint = $this->api_base_url .'locations';
    $api_response = $this->getRequest( $api_endpoint );

    if(isset($api_response['error'])) {
      return $api_response;
    }

    if(isset($api_response['locations']) && count($api_response['locations']) > 0)
    {
      return ['location_id' => $api_response['locations'][0]['id']];
    }
    
    $error_message = 'No locations found';
    return ['error' => $error_message];
  }

  public function loadCredentials()
  {
    $options = get_option('nrd_untappd_importer', array());

    if (count($options) == 0 || strlen($options['nrdui_untappd_email']) < 8 || strlen($options['nrdui_untappd_api']) < 8) {
      $this->api_credentials = '';
    } else {
      $this->api_credentials = base64_encode($options['nrdui_untappd_email'] . ':' . $options['nrdui_untappd_api']);
    }
  }

  public function getRequest(string $url)
  {
    $this->loadCredentials();
    $error_message = '';

    if($this->api_credentials == '')
    {
      $error_message = 'Invalid Credentials';
      return ['error' => $error_message];
    }

    $request_headers = array('Authorization' => 'Basic ' . $this->api_credentials );
    $request_url = $url;
    $request_response = wp_remote_request( $request_url, array(
      'method' => 'GET',
      'headers' => $request_headers
    ) );
    

    // Check for HTTP request errors
    if (is_wp_error($request_response)) {
      $error_message = $request_response->get_error_message();
      update_option('nrd_untappd_importer_api_creds', 0);
      return ['error' => $error_message];
    }

    $body = wp_remote_retrieve_body($request_response);
    $data = json_decode($body, true);

    // Check for JSON decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
      $error_message = json_last_error_msg();
      update_option('nrd_untappd_importer_api_creds', 0);
      return ['error' => $error_message];
    }

    // Check for an error field in the JSON response
    if (isset($data['error'])) {
      // $error_message = $data['error']['detail'];
      $error_message = $data['error']['detail'];
      update_option('nrd_untappd_importer_api_creds', 0);
      return ['error' => $error_message];
    }

    return $data;
  }
}