<?php

require 'vendor/autoload.php';

function get_token() {

$session = new SpotifyWebAPI\Session(
    '2f5cb6750e264181ac417723c3865b02',
    '631069a535da4e51905312599b1caf31'
);

$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();
return $accessToken;

}
?>