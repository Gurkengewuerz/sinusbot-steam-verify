<?php
require 'steamauth/steamauth.php';
$login = false;
$admin = false;
if (isset($_SESSION['steamid'])) {
    include ('steamauth/userInfo.php');
    $db->query("INSERT IGNORE INTO users (steam_id, realname, first_login, verify_code) VALUES ('" . $steamprofile['steamid'] . "', '" . $db->real_escape_string($steamprofile['personaname']) . "', '" . time() . "', '" . $random . "')");
    $userdata = $db->query("SELECT * FROM users WHERE steam_id = '" . $steamprofile['steamid'] . "' LIMIT 1;")->fetch_array();
    $tsIds = $db->query("SELECT users.steam_id, steam_teamspeak.ts_uid, steam_teamspeak.added FROM users JOIN steam_teamspeak ON users.steam_id = steam_teamspeak.steam_id WHERE users.steam_id = '" . $steamprofile['steamid'] . "';");

    if (!empty($userdata)) {
        if ($userdata["admin"] == "1") {
            $admin = true;
        }
    }

    if ($steamprofile['steamid'] == $config["api_head_admin"]) {
        $admin = true;
    }
    $login = true;
}