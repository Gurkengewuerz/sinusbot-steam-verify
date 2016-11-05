<?php

include_once "config.php";


$data = array(
    "error" => "none",
    "success" => false
);

if (isset($_GET["api_key"]) && !empty($_GET["api_key"])) {
    if ($_GET["api_key"] == $config["api_private_key"]) {
        switch ($_GET["type"]) {
            case "verify":
                if (isset($_GET["verify_code"]) && !empty($_GET["verify_code"])) {
                    if (isset($_GET["client_id"]) && !empty($_GET["client_id"])) {
                        $codeData = $db->query("SELECT * FROM users WHERE verify_code = '" . $db->real_escape_string($_GET["verify_code"]) . "' LIMIT 1;")->fetch_array();
                        if (count($codeData) > 0) {
                            $data["steamid"] = $codeData["steam_id"];
                            $userdata = $db->query("SELECT * FROM steam_teamspeak RIGHT JOIN users ON users.steam_id = steam_teamspeak.steam_id WHERE steam_teamspeak.ts_uid='" . $db->real_escape_string($_GET["client_id"]) . "' AND users.steam_id = '" . $codeData["steam_id"] . "';");
                            if ($userdata->fetch_row() >= 1) {
                                $data["error"] = "Already verified this ID";
                            } else {
                                $data["success"] = true;
                                $data["name"] = $codeData["realname"];
                                $db->query("INSERT INTO steam_teamspeak (steam_id, ts_uid, added) VALUES ('" . $codeData["steam_id"] . "', '" . $db->real_escape_string($_GET["client_id"]) . "', '" . time() . "')");
                            }
                        } else {
                            $data["error"] = "Wrong No Verify Code";
                        }
                    } else {
                        $data["error"] = "No Client ID set";
                    }
                } else {
                    $data["error"] = "No Verify Code set";
                }
                break;

            case "getSteamIDbyClient":
                if (isset($_GET["client_id"]) && !empty($_GET["client_id"])) {
                    $userdata = $db->query("SELECT * FROM steam_teamspeak RIGHT JOIN users ON users.steam_id = steam_teamspeak.steam_id WHERE steam_teamspeak.ts_uid='" . $db->real_escape_string($_GET["client_id"]) . "';");
                    if (count($userdata->fetch_array()) > 0) {
                        $data["success"] = true;
                        $data["steamids"] = array();
                        foreach ($userdata as $key) {
                            $data["steamids"][$key["steam_id"]] = $key["realname"];
                        }
                    } else {
                        $data["error"] = "No SteamID found";
                    }
                } else {
                    $data["error"] = "No Client ID set";
                }
                break;

            case "getClientsBySteamID":
                if (isset($_GET["steam_id"]) && !empty($_GET["steam_id"])) {
                    $userdata = $db->query("SELECT * FROM steam_teamspeak RIGHT JOIN users ON users.steam_id = steam_teamspeak.steam_id WHERE users.steam_id='" . $db->real_escape_string($_GET["steam_id"]) . "';");
                    $steamdata = $db->query("SELECT * FROM users WHERE steam_id='" . $db->real_escape_string($_GET["steam_id"]) . "' LIMIT 1;");
                    if (count($userdata->fetch_array()) > 0) {
                        $data["success"] = true;
                        $data["name"] = $steamdata->fetch_array()["realname"];
                        $data["teamspeakids"] = array();
                        foreach ($userdata as $key) {
                            $data["teamspeakids"][str_replace(" ", "+", $key["ts_uid"])] = $key["added"];
                        }
                    } else {
                        $data["error"] = "No Client found";
                    }
                } else {
                    $data["error"] = "No Client ID set";
                }
                break;

            default:
                $data["error"] = "Unknown action";
                break;
        }
    } else {
        $data["error"] = "wrong API Key";
    }
} else {
    $data["error"] = "No API Key set";
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
