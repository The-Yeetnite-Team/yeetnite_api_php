<?php
/** @noinspection DuplicatedCode */
require_once 'database.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

if (str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
    $_POST = json_decode(file_get_contents('php://input'), true) ?? array();
else parse_str(file_get_contents('php://input'), $_POST);

if ($_POST['grant_type'] === 'password') {
    $auth = $database->select(array('username', 'id'), 'users', "WHERE username='{$_POST['username']}' AND password='{$_POST['password']}'");
    if (!$auth) {
        http_response_code(400);
        echo json_encode(array(
            'errorCode' => 'errors.com.epicgames.account.invalid_account_credentials',
            'errorMessage' => 'Sorry the account credentials you are using are invalid',
            'numericErrorCode' => 18031,
            'originatingService' => 'com.epicgames.account.public',
            'intent' => 'prod',
            'error_description' => 'Sorry the account credentials you are using are invalid',
            'error' => 'invalid_grant'
        ));
        exit;
    }
    $token_expire = current_zulu_time(strtotime('+8 hours'));
    echo json_encode(
        array(
            'access_token' => bin2hex(random_bytes(16)),
            'expires_in' => 28800,
            'expires_at' => $token_expire,
            'token_type' => 'bearer',
            'refresh_token' => bin2hex(random_bytes(16)),
            'refresh_expires' => 115200,
            'refresh_expires_at' => current_zulu_time(strtotime('+32 hours')),
            'account_id' => $auth[0]['username'],
            'client_id' => 'yeetnite-client',
            'internal_client' => true,
            'client_service' => 'fortnite',
            'displayName' => $auth[0]['username'],
            'app' => 'fortnite',
            'in_app_id' => $auth[0]['id'],
            'device_id' => '1',
            'auth_method' => 'password' // todo check tigase token type
        )
    );
} else {
    // client_credentials or default
    echo json_encode(
        array(
            'access_token' => bin2hex(random_bytes(16)),
            'expires_in' => 28800,
            'expires_at' => '9999-12-02T01:12:00Z',
            'token_type' => 'bearer',
            'refresh_token' => '98f0a7388971a925',
            'refresh_expires' => 28800,
            'refresh_expires_at' => '9999-12-02T01:12:00Z',
            'client_id' => 'yeetnite-client',
            'account_id' => 'Yeetnite',
            'internal_client' => true,
            'client_service' => 'fortnite',
            'device_id' => 'yeetnitedeviceidlol',
            'app' => 'fortnite',
            'in_app_id' => 'Yeetnite'
        )
    );
}
