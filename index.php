<?php
include_once "config.php";
include_once "usercheck.php";

function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$random = generateRandomString();
$db->query("UPDATE users SET verify_code = '" . $random . "', realname = '" . $db->real_escape_string($steamprofile['personaname']) . "' WHERE steam_id = '" . $steamprofile['steamid'] . "';");
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">

        <title>Steam Verify</title>

        <!-- Bootstrap core CSS -->
        <link href="css/slate-theme.min.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->


    </head>

    <body>
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="index.php" class="navbar-brand">Steam Verify</a>
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse" id="navbar-main">
                    <ul class="nav navbar-nav">
                        <?php
                        if ($admin) {
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="adminList">Admin <span class="caret"></span></a>
                                <ul class="dropdown-menu" aria-labelledby="themes">
                                    <li><a href="?page=admin/users">User List</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Something</a></li>
                                </ul>
                            </li>
                            <?php
                        }
                        ?>
                        <li>
                            <a href="?page=help">Help</a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <?php
                            if (!$login) {
                                loginbutton("rectangle");
                            } else {
                                ?>
                                <button type="btn btn-primary btn-xs" class="btn btn-primary active"><a href="?logout">Log out</a></button>
                                <?php
                            }
                            ?></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="https://gurkengewuerz.de">Credits</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
            <br>
            <br>
            <br>
            <?php
            switch ($_GET["page"]) {
                case "admin/users":
                    if ($admin) {
                        $userList = $db->query("SELECT * FROM steam_teamspeak RIGHT JOIN users ON users.steam_id = steam_teamspeak.steam_id");
                        $listData = array();
                        foreach ($userList as $user) {
                            if ($listData[$user["steam_id"]]["steamid"] !== NULL) {
                                array_push($listData[$user["steam_id"]]["ts_uid"], $user["ts_uid"]);
                                array_push($listData[$user["steam_id"]]["added"], $user["added"]);
                            } else {
                                $listData[$user["steam_id"]] = array();
                                $listData[$user["steam_id"]]["steamid"] = $user["steam_id"];
                                $listData[$user["steam_id"]]["realname"] = $user["realname"];
                                $listData[$user["steam_id"]]["first_login"] = $user["first_login"];
                                $listData[$user["steam_id"]]["admin"] = $user["admin"];
                                if (empty($user["ts_uid"])) {
                                    $listData[$user["steam_id"]]["ts_uid"] = array();
                                    $listData[$user["steam_id"]]["added"] = array();
                                } else {
                                    $listData[$user["steam_id"]]["ts_uid"] = array($user["ts_uid"]);
                                    $listData[$user["steam_id"]]["added"] = array($user["added"]);
                                }
                            }
                        }
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Steam-Name</th>
                                    <th>First Login</th>
                                    <th>Teamspeak ID(s)</th>
                                    <th>Admin State</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($listData as $singleUser) {
                                    ?>
                                    <tr>
                                        <td><?php echo $singleUser["steamid"]; ?></td>
                                        <td><a href="http://steamcommunity.com/profiles/<?php echo $singleUser["steamid"]; ?>"><?php echo $singleUser["realname"]; ?></a></td>
                                        <td><?php echo date("d.m.Y", $singleUser["first_login"]); ?></td>
                                        <td>
                                            <?php
                                            foreach ($singleUser["ts_uid"] as $tsid) {
                                                echo $tsid . ' <button type="button" class="jq-btn btn btn-xs btn-danger" tsID="' . $tsid . '" btn_type="remove_tsID"><span class="glyphicon glyphicon-remove"></span></button><br>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($singleUser["admin"] == "1") {
                                                ?>
                                                <button type="button" class="jq-btn btn btn-danger" user="<?php echo $singleUser["steamid"]; ?>" btn_type="demote">remove Admin</button>
                                                <?php
                                            } else {
                                                ?>
                                                <button type="button" class="jq-btn btn btn-danger" user="<?php echo $singleUser["steamid"]; ?>" btn_type="promote">add Admin</button>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                if (count($listData) < 1) {
                                    ?>
                                    <tr>
                                        <td>No Entries</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        ?>
                        <h1>You are not an admin.</h1>
                        <?php
                    }
                    break;

                case "help":
                    $code = "your_code";
                    if (isset($_GET["code"]) && !empty($_GET["code"])) {
                        $code = $_GET["code"];
                    }
                    ?>
                    <h2>Verify</h2>
                    <ol>
                        <li>
                            Sign in with Steam over the big gray button on the nav bar.<br>
                            <em>This website only saves your Steam ID and your Steam nickname</em>
                        </li>
                        <li>
                            Join our TeamSpeak Server <a href="ts3server://<?php echo $config["ts_ip"]; ?>"><?php echo $config["ts_ip"]; ?></a>.
                        </li>
                        <li>
                            Find the Bot <em><?php echo $config["bot_name"]; ?></em> and write him "<em>!verify <?php echo $code; ?></em>".
                        </li>
                        <li>
                            If there was no error message you should now be verified.
                        </li>
                    </ol>
                    <br>
                    <br>
                    <h2>Check Verify Status from other Clients</h2>
                    <ol>
                        <li>
                            Get the TeamSpeak UID or Steam ID you want to check.
                        </li>
                        <li>
                            Write the Bot <em><?php echo $config["bot_name"]; ?></em> "<em>!check check_id</em>".
                        </li>
                        <li>
                             If there was no error message you should got a list of clients that are associated with that id.
                        </li>
                    </ol>
                    <?php
                    break;

                default:
                    ?>
                    <div class="container container-table">
                        <div class="row vertical-center-row">
                            <div class="text-center col-md-4 col-md-offset-4">
                                <?php if (!$login) {
                                    ?>
                                    <h1>Please Log in with Steam.</h1>
                                    <?php
                                } else {
                                    ?>
                                    <img src="<?php echo $steamprofile['avatarmedium']; ?>" alt="Steam Avatar" class="img-circle"><br><br>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td>Steam ID</td>
                                                <td><?php echo $steamprofile['steamid']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Nickname</td>
                                                <td><?php echo $steamprofile['personaname']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Joined Steam</td>
                                                <td><?php echo date("d.m.Y", $steamprofile['timecreated']); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="width:10%">Associated with</td>
                                                <td style="padding-right: 25px;">
                                                    <?php
                                                    foreach ($tsIds as $tsId) {
                                                        echo '<div class="row row-bottom-margin">';
                                                        echo $tsId["ts_uid"] . '<br>';
                                                        echo '<div class="pull-right"><em>added: ' . date("d.m.Y H:m", $tsId['added']) . '</em>  <button type="button" class="jq-btn btn btn-xs btn-danger" tsID="' . $tsId["ts_uid"] . '" btn_type="remove_tsID"><span class="glyphicon glyphicon-remove"></span></button></div><br><br>';
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Current Verify ID</td>
                                                <td><a href="?page=help&code=<?php echo $random; ?>"><?php echo $random; ?></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-3.1.1.min.js"><\/script>')</script>
        <script src="js/bootstrap.min.js"></script>

        <script type="text/javascript">
            $(".jq-btn").click(function () {
                $.ajax({
                    url: 'user_api.php',
                    type: "POST",
                    async: true,
                    data: {type: $(this).attr("btn_type"), ts3uid: $(this).attr("tsID"), steamid: $(this).attr("user")},
                    success: function (data) {
                        if (data.includes("OK!")) {
                            location.reload();
                        } else {
                            alert(data);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            });
        </script>
    </body>
</html>
