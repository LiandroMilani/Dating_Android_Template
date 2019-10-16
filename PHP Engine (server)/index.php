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

        header("Location: /profile.php?id=".auth::getCurrentUserId());
        exit;
    }

    $page_id = "main";

    $css_files = array("homepage.css?x=29", "my.css");
    $page_title = APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");
?>

<body class="home first-page">

<?php

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
?>

    <div class="content-page homepage">

        <div class="wrap" style="padding: 0;">

            <div class="homepage-section-1">
                <div class="homepage-section-content">
                    <h1 class="homepage-section-headline">Create your own <?php echo APP_NAME; ?> App now!</h1>
                    <p class="homepage-section-description"><?php echo $LANG['main-page-prompt-app']; ?></p>
                    <div class="homepage-cta homepage-spacing">
                        <a href="<?php echo GOOGLE_PLAY_LINK; ?>">
                            <img src="/img/googleplay.png"/>
                        </a>
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

<?php

    function draw($profile, $LANG, $helper) {

        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

            <div class="col s12 m2">
                <div class="card">
                    <div class="card-image">
                        <a href="javascript:void(0)">
                            <img src="<?php echo $profilePhotoUrl; ?>">
                        </a>
                    </div>
                </div>
            </div>

        <?php
    }

    function drawFake($count = 7) {

        if ($count > 7) $count = 7;

        for ($i = 1; $i < $count; $i++) {

            ?>
                <div class="col s12 m2">
                    <div class="card">
                        <div class="card-image">
                            <a href="javascript:void(0)">
                                <img src="/img/<?php echo $i; ?>.jpg">
                            </a>
                        </div>
                    </div>
                </div>
            <?php
        }
    }