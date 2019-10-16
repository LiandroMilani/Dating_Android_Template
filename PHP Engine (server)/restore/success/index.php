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

    $page_id = "restore_success";

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

                    <h1><?php echo $LANG['label-success']; ?>!</h1>

                    <div class="opt-in">
                        <label for="user_receive_digest">
                            <b><?php echo $LANG['label-password-reset-success']; ?></span></b>
                        </label>
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