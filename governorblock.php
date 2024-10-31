<?php 

/*
Plugin Name: School Governor Block by iTCHYROBOT
Description: Activate the plugin then head to the School URN config page and add your School URN.
Version: 1.0
Requires at least: 5.4.2
Requires PHP: 7.2.34
Tested up to: 5.6
Author: iTCHYROBOT - Rob Adams, Scott Thornburn
Author URI: https://www.itchyrobot.co.uk/
License: GPLv2 
*/

class itchyrobot_edtech {

    public function itchyrobot_governor_render_callback( $attributes, $content ) {

        $htmloutput = "";

        $urn = itchyrobot_edtech_admin_get_option('urn');

        if(is_null($urn)){
            return $htmloutput = "No School URN Set";
        }

        $url = "https://get-information-schools.service.gov.uk/Establishments/Establishment/Details/".$urn;

        $output = wp_remote_retrieve_body( wp_remote_get( $url ) );

        if ($output == "") {
            return "No content detected";
        }

        $htmloutput = $this->itchyrobot_processHTML($output);

        return $htmloutput;
    }



    private function itchyrobot_processHTML($html) {

        $html = str_replace ( '"governors-section"', '"governors-section" style="display:none;"', $html);
        $html = str_replace ( '"expanding-content-trigger"', '"governors-section" style="display:none;"', $html);
        
        // a new dom object
        @$dom = new domDocument; 
        $dom->strictErrorChecking = false;
        $dom->recover = true;
        // discard white space
        $dom->preserveWhiteSpace = false;
        // set error level
        $internalErrors = libxml_use_internal_errors(true);

        // load the html into the object
        $dom->loadHTML($html); 

        // Restore error level
        libxml_use_internal_errors($internalErrors);

        //get element by id
        $governoroutput = $dom->getElementById('school-governance');

        if(!$governoroutput)
        {
            die("Element not found");
        }

        $governors = @$dom->saveHTML($governoroutput);
        return  $governors;
    }


    public function itchyrobot_governor_block() {
        wp_register_script(
            'itchyrobot-governor',
            plugins_url( 'block.js', __FILE__ ),
            array('wp-editor', 'wp-blocks', 'wp-element', 'wp-data' ), '2.0.8'
        );
        
        wp_register_style( 'itchyrobot-governor-css', plugins_url( 'block.css', __FILE__ ), '', '1.0.2');

        register_block_type( 'itchyrobot-governor/ablock-governor', array(
            'editor_script' => 'itchyrobot-governor',
            'editor_style' => 'itchyrobot-governor-css',
            'style' => 'itchyrobot-governor-css',
            'render_callback' => array($this, 'itchyrobot_governor_render_callback')
        ) );
    }

    public function itchyrobot_governor_settings() {
        if ( ! defined( 'CMB2_LOADED' ) ) {
            require_once 'CMB2/init.php';
        }
        require_once 'governor_settings.php';
    }

    public function __construct() {
        add_action('init', array($this, 'itchyrobot_governor_settings'), 5);
        add_action( 'init', array($this, 'itchyrobot_governor_block') );
    }
}

new itchyrobot_edtech();