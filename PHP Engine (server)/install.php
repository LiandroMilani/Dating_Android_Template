<?php

    /*!
    * ifsoft.co.uk v1.0
    *
    * http://ifsoft.co.uk
    * raccoonsquare@gmail.com
    *
    * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
    */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");


    if (admin::isSession()) {

        header("Location: /");
        exit;
    }

    $admin = new admin($dbo);
    $gift = new gift($dbo);
    $stickers = new sticker($dbo);

    if ($admin->getCount() > 0) {

        header("Location: /");
        exit;
    }

    include_once($_SERVER['DOCUMENT_ROOT']."/core/initialize.inc.php");

    $page_id = "install";

    $itemId = 14781822; // Dating App Android = 14781822
                        // Dating App iOS = 19393764
                        // My Social Network Android = 13965025
                        // My Social Network iOS = 19414706

    $error = false;
    $error_message = array();

    $pcode = '';
    $user_username = '';
    $user_fullname = '';
    $user_password = '';
    $user_password_repeat = '';

    $error_token = false;
    $error_username = false;
    $error_fullname = false;
    $error_password = false;
    $error_password_repeat = false;

    if (!empty($_POST)) {

        $error = false;

        $pcode = isset($_POST['pcode']) ? $_POST['pcode'] : '';
        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $user_fullname = isset($_POST['user_fullname']) ? $_POST['user_fullname'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $pcode = helper::clearText($pcode);
        $user_username = helper::clearText($user_username);
        $user_fullname = helper::clearText($user_fullname);
        $user_password = helper::clearText($user_password);
        $user_password_repeat = helper::clearText($user_password_repeat);

        $pcode = helper::escapeText($pcode);
        $user_username = helper::escapeText($user_username);
        $user_fullname = helper::escapeText($user_fullname);
        $user_password = helper::escapeText($user_password);
        $user_password_repeat = helper::escapeText($user_password_repeat);



        if (!helper::isCorrectLogin($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = 'Incorrect username.';
        }

        if (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_password = true;
            $error_message[] = 'Incorrect password.';
        }

        if (!$error) {

            $admin = new admin($dbo);

            // Create admin account

            $result = array();
            $result = $admin->signup($user_username, $user_password, $user_fullname, ADMIN_ACCESS_LEVEL_FULL);

            if ($result['error'] === false) {

                $access_data = $admin->signin($user_username, $user_password);

                if ($access_data['error'] === false) {

                    $clientId = 0; // Desktop version

                    admin::createAccessToken();

                    admin::setSession($access_data['accountId'], admin::getAccessToken(), $access_data['username'], $access_data['fullname'], $access_data['access_level']);

                    // Add standard settings

                    $settings = new settings($dbo);
                    $settings->createValue("admob", 1); //Default show admob
                    $settings->createValue("defaultBalance", 10); //Default balance for new users
                    $settings->createValue("defaultReferralBonus", 10); //Default bonus - referral signup
                    $settings->createValue("defaultFreeMessagesCount", 150); //Default free messages count after signup
                    $settings->createValue("allowFriendsFunction", 1);
                    $settings->createValue("allowSeenTyping", 1);
                    $settings->createValue("allowMultiAccountsFunction", 1);
                    $settings->createValue("allowFacebookAuthorization", 1);
                    $settings->createValue("allowUpgradesSection", 1);
                    $settings->createValue("allowRewardedAds", 1);
                    unset($settings);

                    // Add standard gifts

                    if ($gift->db_getMaxId() < 1) {

                        for ($i = 1; $i < 31; $i++) {

                            $gift->db_add(3, 0, APP_URL."/".GIFTS_PATH.$i.".jpg");

                        }
                    }

                    // Add standard stickers

                    if ($stickers->db_getMaxId() < 1) {

                        for ($i = 1; $i < 28; $i++) {

                            $stickers->db_add(APP_URL."/stickers/".$i.".png");

                        }
                    }

                    // Add standard feelings

                    $feelings = new feelings($dbo);

                    if ($feelings->db_getMaxId() < 1) {

                        for ($i = 1; $i <= 12; $i++) {

                            $feelings->db_add(APP_URL."/feelings/".$i.".png");

                        }
                    }

                    // Redirect to Admin Panel main page

                    header("Location: /admin/main.php");
                    exit;
                }

                header("Location: /install.php");
            }
        }
    }

    auth::newAuthenticityToken();

    $css_files = array("my.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");
?>

<body class="remind-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">
        <div class="main-column">
            <div class="main-content">

                <div class="standard-page">

                    <h1>Warning!</h1>
                    <p>Remember that now Create an account administrator!</p>

                    <div class="errors-container" style="<?php if (!$error) echo "display: none"; ?>">
                        <p class="title"><?php echo $LANG['label-errors-title']; ?></p>
                        <ul>
                            <?php

                            foreach ($error_message as $msg) {

                                echo "<li>{$msg}</li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <form accept-charset="UTF-8" action="/install.php" class="custom-form" id="remind-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <div class="row">
                            <div class="input-field col s12">
                                <p style="font-weight: 600">How to get purchase code you can read here: <a href="http://ifsoft.co.uk/help/how_to_get_purchase_code/" target="_blank">How to get purchase code?</a></p>
                            </div>
                        </div>

                        <input id="pcode" name="pcode" placeholder="Purchase code (NULLED)" required="required" size="60" type="text" value="NULLED">

                        <input id="user_username" name="user_username" placeholder="Username" required="required" size="30" type="text" value="<?php echo $user_username; ?>">
                        <input id="user_fullname" name="user_fullname" placeholder="Fullname" required="required" size="30" type="text" value="<?php echo $user_fullname; ?>">
                        <input id="user_password" name="user_password" placeholder="Password" required="required" size="30" type="password" value="">

                        <div class="login-button">
                            <input name="commit" type="submit" value="Install">
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
    ?>

</body>
</html>