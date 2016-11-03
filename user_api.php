<?php

include_once "config.php";
include_once 'usercheck.php';


if ($login) {
    include ('steamauth/userInfo.php');
    if (isset($_POST["type"]) && !empty($_POST["type"])) {
        switch ($_POST["type"]) {
            case "remove_tsID":
                $tsIds = $db->query("SELECT users.steam_id, steam_teamspeak.ts_uid, steam_teamspeak.added FROM users JOIN steam_teamspeak ON users.steam_id = steam_teamspeak.steam_id WHERE users.steam_id = '" . $steamprofile['steamid'] . "';");

                if (!empty($tsIds) || $admin) {
                    $db->query("DELETE FROM steam_teamspeak WHERE ts_uid = '" . $db->real_escape_string($_POST["ts3uid"]) . "';");
                    echo "OK!";
                }
                break;

            case "promote":
                if ($admin) {
                    $db->query("UPDATE users SET admin = '1' WHERE steam_id = '" . $db->real_escape_string($_POST["steamid"]) . "';");
                    echo "OK!";
                }
                break;

            case "demote":
                if ($admin) {
                    $db->query("UPDATE users SET admin = '0' WHERE steam_id = '" . $db->real_escape_string($_POST["steamid"]) . "';");
                    echo "OK!";
                }
                break;
        }
    } else {
        echo "WRONG 2";
    }
} else {
    echo "WRONG 1";
}
