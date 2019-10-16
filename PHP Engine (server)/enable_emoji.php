<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    $page_id = "emoji";

    include_once($_SERVER['DOCUMENT_ROOT']."/core/initialize.inc.php");

    $update = new update($dbo);
    $update->setChatEmojiSupport();
    $update->setDialogsEmojiSupport();
    $update->setPhotosEmojiSupport();
    $update->setImagesCommentsEmojiSupport();
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
                            Your MySQL version: <?php print mysql_get_client_info(); ?>
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