<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    $page_id = "update";

    include_once($_SERVER['DOCUMENT_ROOT']."/core/initialize.inc.php");

    $update = new update($dbo);
    $update->addColumnToChatsTable();
    $update->addColumnToChatsTable2();

    $update->addColumnToAdminsTable();

    $update->addColumnToUsersTable15();

    $update->addColumnToGalleryTable1();
    $update->addColumnToGalleryTable2();
    $update->addColumnToGalleryTable3();

    $update->addColumnToUsersTable1();
    $update->addColumnToUsersTable2();
    $update->addColumnToUsersTable3();
    $update->addColumnToUsersTable4();
    $update->addColumnToUsersTable5();

    // For version 2.7

    $update->addColumnToUsersTable6();

    // Only For version 2.8

    $update->updateUsersTable();

    // For version 3.0

    $update->addColumnToUsersTable7();
    $update->addColumnToUsersTable8();
    $update->addColumnToUsersTable9();
    $update->addColumnToUsersTable10();

    // For version 3.1

    $update->addColumnToUsersTable11();
    $update->addColumnToUsersTable12();

    // For version 3.2

    $update->addColumnToUsersTable14();

    // For version 3.4

    $update->addColumnToMessagesTable1();

    // For version 3.5

    $update->addColumnToUsersTable16(); // add field sex_orientation
    $update->addColumnToUsersTable17(); // add field u_age
    $update->addColumnToUsersTable18(); // add field u_height
    $update->addColumnToUsersTable19(); // add field u_weight

    $update->addColumnToUsersTable20();
    $update->addColumnToUsersTable21();
    $update->addColumnToUsersTable22();

    // For version 3.6

    $update->addColumnToUsersTable23();
    $update->addColumnToUsersTable24();
    $update->addColumnToUsersTable25();

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
    unset($settings);

    // For version 3.7

    $settings = new settings($dbo);
    $settings->createValue("allowSeenTyping", 1);
    unset($settings);

    $update->addColumnToUsersTable26();

    // For version 3.8

    $settings = new settings($dbo);
    $settings->createValue("allowRewardedAds", 1); //Default allow rewarded ads
    unset($settings);

    // For version 4.1

    $update->addColumnToGalleryTable4();

    // Add standard feelings

    $feelings = new feelings($dbo);

    if ($feelings->db_getMaxId() < 1) {

        for ($i = 1; $i <= 12; $i++) {

            $feelings->db_add(APP_URL."/feelings/".$i.".png");

        }
    }

    // Add standard stickers

    $stickers = new sticker($dbo);

    if ($stickers->db_getMaxId() < 1) {

        for ($i = 1; $i < 28; $i++) {

            $stickers->db_add(APP_URL."/stickers/".$i.".png");

        }
    }

    unset($stickers);

    unset($update);

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

                    <div class="success-container" style="margin-top: 15px;">
                        <ul>
                            <b>Success!</b>
                            <br>
                            Your MySQL version: <?php echo $dbo->query('select version()')->fetchColumn(); ?>
                            <br>
                            Database refactoring success!
                        </ul>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
    ?>

</body>
</html>