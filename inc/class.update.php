<?php
class FMUpdater
{

    public $fmp_installed_version;

    public $url;

    public function __construct($version)
    {

        // $this->url = '138.197.174.191/index.php/fmp-version-query';

        // $this->fmp_installed_version = $version;

        // if(!function_exists('curl_init')){
        //add_action( 'admin_notices', array(&$this, 'curl_not_available_notification') );
        // } else{

        // Request to remote server.
        //add_action( 'admin_notices', array(&$this, 'check_for_update') );

        // }

    }

    public function check_for_update()
    {
        $params = array(
            'id' => 119,
            'version' => $this->fmp_installed_version,
            'domain' =>  $_SERVER['SERVER_NAME'],
            'ip' =>  $_SERVER['SERVER_ADDR'],
        );

        $defaults = array(
            CURLOPT_URL => $this->url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        ob_start();
        curl_exec($ch);
        $response = ob_get_contents();
        ob_end_clean();
        $response = json_decode($response);
        curl_close($ch);
        //auto::  pr($response);
        if ($response->status != 'ok') $this->fmp_update_notification();
    }

    public function curl_not_available_notification()
    {
?>
        <div class="notice notice-error is-dismissible point-notice">
            <p>You need to activate <em>PHP_CURL</em> for <b>File Manager Pro</b> to work properly.</p>
        </div>
    <?php
    }

    public function fmp_update_notification()
    {
    ?>
        <div class="notice notice-error is-dismissible point-notice">
            <p>Your copy of <b>File Manager Pro</b> needs to be <a href='http://giribaz.com/my-account/downloads/'>updated</a>.</p>
        </div>
<?php
    }
}
