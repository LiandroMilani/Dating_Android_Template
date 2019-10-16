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

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    $account = new account($dbo, auth::getCurrentUserId());
    $profile = new profile($dbo, auth::getCurrentUserId());

    $profileInfo = $profile->getVeryShort();

    $account->setLastActive();

    $items_all = $profileInfo['likesCount'];
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $profile->getFans($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::peopleCardviewItem($value, $LANG, true, $value['age'], $LANG['label-select-age'], "red");
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Likes.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['html2'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "likes";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-likes']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body class="width-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/likes"><span class="tab active"><?php echo $LANG['tab-likes']; ?></span></a>
                            <a href="/account/liked"><span class="tab"><?php echo $LANG['tab-liked']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                        $result = $profile->getFans(0);

                        $items_loaded = count($result['items']);

                        if ($items_loaded == 0) {

                            ?>

                                <header class="top-banner info-banner empty-list-banner">


                                </header>

                            <?php
                        }
                    ?>

                </div>

            </div>

            <div class="main-content cardview-content">

                <div class="standard-page cardview-container">

                    <?php

                    if ($items_loaded != 0) {

                        ?>

                        <div class="cardview">

                            <?php

                                foreach ($result['items'] as $key => $value) {

                                    draw::peopleCardviewItem($value, $LANG, true, $value['age'], $LANG['label-select-age'], "red");
                                }
                            ?>
                        </div>

                        <?php
                    }
                    ?>

                </div>
            </div>

            <div class="main-content profile-info-content loading-banner-content <?php if ($items_all <= 20) echo "gone";  ?>">

                <div class="standard-page loading-banner-container">

                    <?php

                    if ($items_all > 20) {

                        ?>

                        <header class="top-banner loading-banner">

                            <div class="prompt">
                                <button onclick="Likes.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                            </div>

                        </header>

                        <?php
                    }
                    ?>

                </div>
            </div>

        </div>

    </div>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            window.Likes || ( window.Likes = {} );

            Likes.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/account/likes/',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')){

                            $("div.cardview").append(response.html);
                        }

                        if (response.hasOwnProperty('html2')){

                            $('header.loading-banner').remove();

                            $("div.loading-banner-container").append(response.html2);
                            $("div.loading-banner-content").removeClass("gone");
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

        <script type="text/javascript" src="/js/chat.js"></script>

</body>
</html>