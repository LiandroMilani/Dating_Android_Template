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

    $page_id = "gdpr";

    $css_files = array("my.css");
    $page_title = $LANG['page-gdpr']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_header.inc.php");

    ?>

<body class="about-page">


    <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . "/common/site_topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <?php

                    if (file_exists($LANG['lang-code'].".inc.php")) {

                        include_once($LANG['lang-code'].".inc.php");

                    } else {

                        include_once("en.inc.php");
                    }
                ?>

            </div>

        </div>

    </div>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
    ?>


</body
</html>