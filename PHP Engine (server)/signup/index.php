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
    $user_email = '';
    $user_fullname = '';
    $gender = 2;
    $age = 0;
    $sex_orientation = 0;
    $user_referrer = 0;

    $error = false;
    $error_message = array();

    if (!empty($_POST)) {

        $error = false;

        $user_username = isset($_POST['username']) ? $_POST['username'] : '';
        $user_fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $user_password = isset($_POST['password']) ? $_POST['password'] : '';
        $user_email = isset($_POST['email']) ? $_POST['email'] : '';
        $user_referrer = isset($_POST['referrer']) ? $_POST['referrer'] : 0;
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $gender = isset($_POST['gender']) ? $_POST['gender'] : 2;
        $age = isset($_POST['age']) ? $_POST['age'] : 0;
        $sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

        $user_referrer = helper::clearInt($user_referrer);

        $user_username = helper::clearText($user_username);
        $user_fullname = helper::clearText($user_fullname);
        $user_password = helper::clearText($user_password);
        $user_email = helper::clearText($user_email);

        $user_username = helper::escapeText($user_username);
        $user_fullname = helper::escapeText($user_fullname);
        $user_password = helper::escapeText($user_password);
        $user_email = helper::escapeText($user_email);

        $gender = helper::clearInt($gender);
        $age = helper::clearInt($age);
        $sex_orientation = helper::clearInt($sex_orientation);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_token = true;
            $error_message[] = $LANG['msg-error-unknown'];
        }

        if (!helper::isCorrectLogin($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-incorrect'];
        }

        if ($helper->isLoginExists($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-taken'];
        }

        if (!helper::isCorrectFullname($user_fullname)) {

            $error = true;
            $error_fullname = true;
            $error_message[] = $LANG['msg-fullname-incorrect'];
        }

        if (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_password = true;
            $error_message[] = $LANG['msg-password-incorrect'];
        }

        if (!helper::isCorrectEmail($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-incorrect'];
        }

        if ($helper->isEmailExists($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-taken'];
        }

        if ($age > 110 || $age < 18) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-age-incorrect'];
        }

        if ($gender > 2 || $gender < 0) {

            // 0 = male
            // 1 = female
            // 2 = secret

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-gender-incorrect'];
        }

        if ($sex_orientation > 4 || $sex_orientation < 1) {

            // 0 = undefined

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-sex-orientation-incorrect'];
        }

        if (!$error) {

            $account = new account($dbo);

            $result = array();
            $result = $account->signup($user_username, $user_fullname, $user_password, $user_email, $gender, 2000, 1, 1, $age, $sex_orientation, $LANG['lang-code']);

            if ($result['error'] === false) {

                $clientId = 0; // Desktop version

                $auth = new auth($dbo);
                $access_data = $auth->create($result['accountId'], $clientId);

                if ($access_data['error'] === false) {

                    auth::setSession($access_data['accountId'], $user_username, $user_fullname, "", 0, 0, 150, $account->getAccessLevel($access_data['accountId']), $access_data['accessToken']);
                    auth::updateCookie($user_username, $access_data['accessToken']);

                    $language = $account->getLanguage();

                    $account->setState(ACCOUNT_STATE_ENABLED);

                    $account->setLastActive();

                    // refsys

                    if ($user_referrer != 0) {

                        $ref = new refsys($dbo);
                        $ref->setRequestFrom($account->getId());
                        $ref->setReferrer($user_referrer);

                        $ref->setReferralsCount($user_referrer, $ref->getReferralsCount($user_referrer));

                        $ref->addSignupBonus($user_referrer);

                        unset($ref);
                    }

                    //Facebook connect

                    if (isset($_SESSION['oauth']) && $_SESSION['oauth'] === 'facebook' && $helper->getUserIdByFacebook($_SESSION['oauth_id']) == 0) {

                        $account->setFacebookId($_SESSION['oauth_id']);

                        $time = time();
                        $fb_id = $_SESSION['oauth_id'];

                        $img = @file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture?type=large');
                        $file =  TEMP_PATH.$time.".jpg";
                        @file_put_contents($file, $img);

                        $imglib = new imglib($dbo);
                        $response = $imglib->createPhoto($file, $file);
                        unset($imglib);

                        if ($response['error'] === false) {

                            $account->setPhoto($response);
                        }

                        unset($_SESSION['oauth']);
                        unset($_SESSION['oauth_id']);
                        unset($_SESSION['oauth_name']);
                        unset($_SESSION['oauth_email']);
                        unset($_SESSION['oauth_link']);

                    } else {

                        $account->setFacebookId("");
                    }

                    header("Location: /profile.php/?id=".$access_data['accountId']);
                    exit;
                }

            } else {

                $error = true;
                $error_message[] = "You can not create multi-accounts!";
            }
        }
    }

    if (isset($_SESSION['oauth']) && empty($user_username) && empty($user_email)) {

        $user_fullname = $_SESSION['oauth_name'];
        $user_email = $_SESSION['oauth_email'];
    }

    auth::newAuthenticityToken();

    $page_id = "signup";

    $css_files = array("my.css");
    $page_title = $LANG['page-signup']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");
?>

<body class="signup-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">
            <div class="main-content">

                <div class="standard-page">
                    <h1><?php echo $LANG['page-signup']; ?></h1>
                    <p><?php echo $LANG['label-signup-sub-title']; ?></p>

                    <form accept-charset="UTF-8" action="/signup/" class="custom-form" id="signup-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                        <?php

                        if (isset($_SESSION['oauth'])) {

                            ?>

                            <div class="opt-in">
                                <label for="user_receive_digest">
                                    <?php

                                    $headers = get_headers('https://graph.facebook.com/'.$_SESSION['oauth_id'].'/picture',1);

                                    if (isset($headers['Location'])) {

                                        $url = $headers['Location']; // string

                                        ?>

                                        <img src="<?php echo $url; ?>" alt="" style="width: 50px; float: left">

                                        <?php

                                    } else {

                                        ?>

                                        <img src="\img\profile_default_photo.png" alt="" style="width: 50px; float: left">

                                        <?php
                                    }
                                    ?>

                                    <div style="padding-left: 60px;">
                                        <b><a target="_blank" href="https://www.facebook.com/app_scoped_user_id/<?php echo $_SESSION['oauth_id']; ?>"><?php echo $_SESSION['oauth_name']; ?></a></b>
                                        <span><?php echo $LANG['label-authorization-with-facebook']; ?></span>
                                        <br>
                                        <a href="/facebook"><?php echo $LANG['action-back-to-default-signup']; ?></a>
                                    </div>
                                </label>
                            </div>

                            <?php

                        } else {

                            if (FACEBOOK_AUTHORIZATION) {

                                ?>

                                <p>
                                    <a class="fb-icon-btn fb-btn-large btn-facebook" href="/facebook/signup">
                                        <span class="icon-container">
                                            <i class="icon icon-facebook"></i>
                                        </span>
                                        <span><?php echo $LANG['action-signup-with'] . " " . $LANG['label-facebook']; ?></span>
                                    </a>
                                </p>

                                <?php
                            }
                        }

                        ?>

                        <div class="errors-container" style="<?php if (!$error) echo "display: none"; ?>">
                            <p class="title"><?php echo $LANG['label-errors-title']; ?></p>
                            <ul>
                                <?php

                                foreach ($error_message as $key => $value) {

                                    echo "<li>{$value}</li>";
                                }
                                ?>
                            </ul>
                        </div>

                        <input id="username" name="username" placeholder="<?php echo $LANG['label-username']; ?>" required="required" size="30" type="text" value="<?php echo $user_username; ?>">
                        <input id="fullname" name="fullname" placeholder="<?php echo $LANG['label-fullname']; ?>" required="required" size="30" type="text" value="<?php echo $user_fullname; ?>">
                        <input id="password" name="password" placeholder="<?php echo $LANG['label-password']; ?>" required="required" size="30" type="password" value="">
                        <input id="email" name="email" placeholder="<?php echo $LANG['label-email']; ?>" required="required" size="48" type="text" value="<?php echo $user_email; ?>">

                        <div class="opt-in">
                            <select name="age" id="age" style="margin-bottom: 15px; width: 100%">

                                <option disabled value="0" <?php if ($age < 18) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-age']; ?></option>

                                <?php

                                for ($i = 18; $i <= 110; $i++) {

                                    if ($i == $age) {

                                        echo "<option value=\"$i\" selected=\"selected\">$i</option>";

                                    } else {

                                        echo "<option value=\"$i\">$i</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="opt-in">
                            <select name="gender" id="gender" style="margin-bottom: 15px; width: 100%">
                                <option value="2" <?php if ($gender != SEX_FEMALE && $gender != SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-secret']; ?></option>
                                <option value="0" <?php if ($gender == SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-male']; ?></option>
                                <option value="1" <?php if ($gender == SEX_FEMALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-female']; ?></option>
                            </select>
                        </div>

                        <div class="opt-in">
                            <select name="sex_orientation" id="sex_orientation" style="margin-bottom: 15px; width: 100%">
                                <option disabled value="0" <?php if ($sex_orientation == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-sex-orientation']; ?></option>
                                <option value="1" <?php if ($sex_orientation == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-1']; ?></option>
                                <option value="2" <?php if ($sex_orientation == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-2']; ?></option>
                                <option value="3" <?php if ($sex_orientation == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-3']; ?></option>
                                <option value="4" <?php if ($sex_orientation == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-4']; ?></option>
                            </select>
                        </div>

                        <div class="opt-in">
                            <label for="referrer">
                                <?php echo $LANG['label-signup-invite']; ?>
                            </label>
                        </div>

                        <input style="margin-bottom: 15px;" id="referrer" name="referrer" placeholder="<?php echo $LANG['label-user-id']; ?>" size="8" type="number" value="<?php echo $user_referrer; ?>">

                        <div class="opt-in">
                            <label for="user_receive_digest">
                                <b><?php echo $LANG['label-signup-confirm']; ?></b>
                                <a style="font-size: 0.8rem;" href="/terms"><?php echo $LANG['page-terms']; ?></a>
                            </label>
                        </div>

                        <input class="submit-button red" name="commit" type="submit" value="<?php echo $LANG['action-signup']; ?>">
                    </form>
                </div>

            </div>
        </div>

        <aside class="sidebar-column">
            <div class="register-prompt sidebar-block">
                <h3><?php echo $LANG['label-existing-account']; ?></h3>
                <a href="/login" class="button blue"><?php echo $LANG['action-login']; ?></a>
            </div>
        </aside>

    </div>


    <?php

        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_footer.inc.php");
    ?>

</body>
</html>