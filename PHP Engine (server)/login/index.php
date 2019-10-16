<?php

    /*!
	 * ifsoft engine v1.0
	 *
	 * http://ifsoft.com.ua, http://ifsoft.co.uk
	 * raccoonsquare@gmail.com
	 *
	 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
	 */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (auth::isSession()) {

        header("Location: /");
        exit;
    }

    $user_username = '';

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_username = helper::clearText($user_username);
        $user_password = helper::clearText($user_password);

        $user_username = helper::escapeText($user_username);
        $user_password = helper::escapeText($user_password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            $access_data = array();

            $account = new account($dbo);

            $access_data = $account->signin($user_username, $user_password);

            unset($account);

            if ($access_data['error'] === false) {

                $account_fullname = $access_data['fullname'];
                $account_photo_url = $access_data['photoUrl'];
                $account_verify = $access_data['verify'];
                $account_pro_mode = $access_data['pro'];
                $account_free_messages_count = $access_data['free_messages_count'];

                $account = new account($dbo, $access_data['accountId']);

                switch ($account->getState()) {

                    case ACCOUNT_STATE_BLOCKED: {

                        break;
                    }

                    case ACCOUNT_STATE_DISABLED: {

                        break;
                    }

                    default: {

                        $account->setState(ACCOUNT_STATE_ENABLED);

                        $clientId = 0; // Desktop version

                        $auth = new auth($dbo);
                        $access_data = $auth->create($access_data['accountId'], $clientId);

                        if ($access_data['error'] === false) {

                            auth::setSession($access_data['accountId'], $user_username, $account_fullname, $account_photo_url, $account_verify, $account_pro_mode, $account_free_messages_count, $account->getAccessLevel($access_data['accountId']), $access_data['accessToken']);
                            auth::updateCookie($user_username, $access_data['accessToken']);

                            unset($_SESSION['oauth']);
                            unset($_SESSION['oauth_id']);
                            unset($_SESSION['oauth_name']);
                            unset($_SESSION['oauth_email']);
                            unset($_SESSION['oauth_link']);

                            $account->setLastActive();

                            header("Location: /");
                        }
                    }
                }

            } else {

                $error = true;
            }
        }
    }

    auth::newAuthenticityToken();

    $page_id = "login";

    $css_files = array("my.css");
    $page_title = $LANG['page-login']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");
?>

<body class="login-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content" role="main">

                <div class="standard-page">

                    <h1><?php echo $LANG['page-login']; ?></h1>

                    <?php

                    if (FACEBOOK_AUTHORIZATION) {

                        ?>

                        <p>
                            <a class="fb-icon-btn fb-btn-large btn-facebook" href="/facebook/login">
                                    <span class="icon-container">
                                        <i class="icon icon-facebook"></i>
                                    </span>
                                <span><?php echo $LANG['action-login-with']." ".$LANG['label-facebook']; ?></span>
                            </a>
                        </p>
                        <?php
                    }
                    ?>

                    <form accept-charset="UTF-8" action="/login/" class="custom-form" id="login-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <div class="errors-container" style="<?php if (!$error) echo "display: none"; ?>">
                            <p class="title"><?php echo $LANG['label-errors-title']; ?></p>
                            <ul>
                                <li><?php echo $LANG['msg-error-authorize']; ?></li>
                            </ul>
                        </div>

                        <input id="username" name="user_username" placeholder="<?php echo $LANG['label-username']; ?>" required="required" size="30" type="text" value="<?php echo $user_username; ?>">
                        <input id="password" name="user_password" placeholder="<?php echo $LANG['label-password']; ?>" required="required" size="30" type="password" value="">

                        <div class="login-button">
                            <input style="margin-right: 10px" class="submit-button red" name="commit" type="submit" value="<?php echo $LANG['action-login']; ?>">
                            <a href="/remind" class="help"><?php echo $LANG['action-forgot-password']; ?></a>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <aside class="sidebar-column">
            <div class="register-prompt sidebar-block">
                <h3><?php echo $LANG['label-missing-account']; ?></h3>
                <a href="/signup" class="button blue"><?php echo $LANG['action-join']; ?></a>
            </div>
        </aside>

    </div>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
    ?>

</body>
</html>