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

    $profile = new account($dbo, auth::getCurrentUserId());

    $friends = new friends($dbo, auth::getCurrentUserId());
    $friends->setRequestFrom(auth::getCurrentUserId());


    if (isset($_GET['action'])) {

        $friends_count = $friends->getNewCount($profile->getLastFriendsView());

        echo $friends_count;
        exit;
    }

    $profile->setLastActive();

    $profile->setLastFriendsView();

    $items_all = $friends->count();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $friends->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::friendItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                    <header class="top-banner loading-banner">

                        <div class="prompt">
                            <button onclick="Friends.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                        </div>

                    </header>

                <?php

                $result['html2'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "friends";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-friends']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body class="width-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page page-title-content">

                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-friends']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['label-friends-sub-title']; ?>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                        $result = $friends->get(0);

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

                                draw::friendItem($value, $LANG, $helper);
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
                                <button onclick="Friends.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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

            var friends_all = <?php echo $items_all; ?>;
            var friends_loaded = <?php echo $items_loaded; ?>;

        </script>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            window.Friends || ( window.Friends = {} );

            Friends.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/account/friends',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("div.loading-banner-content").addClass("gone");

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