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

    $page_id = "about";

    $css_files = array("my.css");
    $page_title = $LANG['page-about'];

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

    ?>

<body class="about-page">


    <?php
        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <section class="standard-page">
                    <h1><?php echo $LANG['page-about']; ?></h1>
                    <p><?php echo APP_TITLE." ".APP_VERSION." (web version) © ".APP_YEAR; ?></p>
                </section>

                <section class="standard-page">
                    <h1>About Example Section title</h1>

                    <h3>About Example sub-title</h3>

                    <p>About Example text. About Example text. About Example text. About Example text. About Example text.</p>

                </section>

            </div>

        </div>

    </div>

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
    ?>


</body
</html>