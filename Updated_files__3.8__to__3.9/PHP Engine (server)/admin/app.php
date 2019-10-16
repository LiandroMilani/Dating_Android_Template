<?php

    /*!
     * ifsoft.co.uk admin engine v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: /admin/login.php");
        exit;
    }

    $stats = new stats($dbo);
    $settings = new settings($dbo);
    $admin = new admin($dbo);

    $allowRewardedAds = 1;

    $allowSeenTyping = 1;

    $allowFacebookAuthorization = 1;
    $allowMultiAccountsFunction = 1;

    $defaultFreeMessagesCount = 150;
    $defaultReferralBonus = 10;
    $defaultBalance = 10;

    if (!empty($_POST)) {

        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowRewardedAds_checkbox = isset($_POST['allowRewardedAds']) ? $_POST['allowRewardedAds'] : '';

        $allowSeenTyping_checkbox = isset($_POST['allowSeenTyping']) ? $_POST['allowSeenTyping'] : '';

        $allowFacebookAuthorization_checkbox = isset($_POST['allowFacebookAuthorization']) ? $_POST['allowFacebookAuthorization'] : '';
        $allowMultiAccountsFunction_checkbox = isset($_POST['allowMultiAccountsFunction']) ? $_POST['allowMultiAccountsFunction'] : '';

        $defaultFreeMessagesCount = isset($_POST['defaultFreeMessagesCount']) ? $_POST['defaultFreeMessagesCount'] : 150;
        $defaultReferralBonus = isset($_POST['defaultReferralBonus']) ? $_POST['defaultReferralBonus'] : 10;
        $defaultBalance = isset($_POST['defaultBalance']) ? $_POST['defaultBalance'] : 10;

        if ($authToken === helper::getAuthenticityToken() && !APP_DEMO) {

            if ($allowRewardedAds_checkbox === "on") {

                $allowRewardedAds = 1;

            } else {

                $allowRewardedAds = 0;
            }

            if ($allowSeenTyping_checkbox === "on") {

                $allowSeenTyping = 1;

            } else {

                $allowSeenTyping = 0;
            }

            if ($allowFacebookAuthorization_checkbox === "on") {

                $allowFacebookAuthorization = 1;

            } else {

                $allowFacebookAuthorization = 0;
            }

            if ($allowMultiAccountsFunction_checkbox === "on") {

                $allowMultiAccountsFunction = 1;

            } else {

                $allowMultiAccountsFunction = 0;
            }

            $defaultBalance = helper::clearInt($defaultBalance);
            $defaultReferralBonus = helper::clearInt($defaultReferralBonus);
            $defaultFreeMessagesCount = helper::clearInt($defaultFreeMessagesCount);

            if ($defaultBalance < 0) {

                $defaultBalance = 0;
            }

            if ($defaultReferralBonus < 0) {

                $defaultReferralBonus = 0;
            }

            if ($defaultFreeMessagesCount < 0) {

                $defaultFreeMessagesCount = 0;
            }

            $settings->setValue("allowRewardedAds", $allowRewardedAds);

            $settings->setValue("allowSeenTyping", $allowSeenTyping);

            $settings->setValue("allowFacebookAuthorization", $allowFacebookAuthorization);
            $settings->setValue("allowMultiAccountsFunction", $allowMultiAccountsFunction);

            $settings->setValue("defaultBalance", $defaultBalance);
            $settings->setValue("defaultReferralBonus", $defaultReferralBonus);
            $settings->setValue("defaultFreeMessagesCount", $defaultFreeMessagesCount);
        }
    }

    $config = $settings->get();

    $arr = array();

    $arr = $config['allowRewardedAds'];
    $allowRewardedAds = $arr['intValue'];

    $arr = $config['allowSeenTyping'];
    $allowSeenTyping = $arr['intValue'];

    $arr = $config['allowFacebookAuthorization'];
    $allowFacebookAuthorization = $arr['intValue'];

    $arr = $config['allowMultiAccountsFunction'];
    $allowMultiAccountsFunction = $arr['intValue'];

    $arr = $config['defaultBalance'];
    $defaultBalance = $arr['intValue'];

    $arr = $config['defaultReferralBonus'];
    $defaultReferralBonus = $arr['intValue'];

    $arr = $config['defaultFreeMessagesCount'];
    $defaultFreeMessagesCount = $arr['intValue'];

    $page_id = "app";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("mytheme.css");
    $page_title = "App Settings";

    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">

    <div id="main-wrapper">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper">

            <div class="container-fluid">

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/main.php">Home</a></li>
                            <li class="breadcrumb-item active">App Settings</li>
                        </ol>
                    </div>
                </div>

                <?php

                    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_banner.inc.php");
                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">App Settings</h4>
                                <h6 class="card-subtitle">Change application settings</h6>

                                <form action="/admin/app.php" method="post">

                                    <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                    <div class="form-group">
                                        <p>
                                            <input type="checkbox" name="allowRewardedAds" id="allowRewardedAds" <?php if ($allowRewardedAds == 1) echo "checked=\"checked\"";  ?> />
                                            <label for="allowRewardedAds">Allow Rewarded Ads</label>
                                        </p>

                                        <p>
                                            <input type="checkbox" name="allowSeenTyping" id="allowSeenTyping" <?php if ($allowSeenTyping == 1) echo "checked=\"checked\"";  ?> />
                                            <label for="allowSeenTyping">Allow Seen&Typing functions in chat</label>
                                        </p>

                                        <p style="display: none">
                                            <input type="checkbox" name="allowFacebookAuthorization" id="allowFacebookAuthorization" <?php if ($allowFacebookAuthorization == 1) echo "checked=\"checked\"";  ?> />
                                            <label for="allowFacebookAuthorization">Allow registration/authorization via Facebook</label>
                                        </p>

                                        <p>
                                            <input type="checkbox" name="allowMultiAccountsFunction" id="allowMultiAccountsFunction" <?php if ($allowMultiAccountsFunction == 1) echo "checked=\"checked\"";  ?> />
                                            <label for="allowMultiAccountsFunction">Enable creation of multi-accounts</label>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="defaultBalance" class="active">Balance of the user after registration (credits)</label>
                                        <input class="form-control" id="defaultBalance" type="number" size="4" name="defaultBalance" value="<?php echo $defaultBalance; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="defaultReferralBonus" class="active">Number of credits for referral registration</label>
                                        <input class="form-control" id="defaultReferralBonus" type="number" size="4" name="defaultReferralBonus" value="<?php echo $defaultReferralBonus; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="defaultFreeMessagesCount" class="active">Number of free messages for the user</label>
                                        <input class="form-control" id="defaultFreeMessagesCount" type="number" size="4" name="defaultFreeMessagesCount" value="<?php echo $defaultFreeMessagesCount; ?>">
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <button class="btn btn-info text-uppercase waves-effect waves-light" type="submit">Save</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>



            </div> <!-- End Container fluid  -->

            <?php

                include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_footer.inc.php");
            ?>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>