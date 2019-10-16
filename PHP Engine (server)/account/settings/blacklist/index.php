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

    $profile = new profile($dbo, auth::getCurrentUserId());

    $blacklist = new blacklist($dbo);
    $blacklist->setRequestFrom(auth::getCurrentUserId());

    $items_all = $blacklist->myActiveItemsCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $blacklist->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::blackListItem($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="BlackList.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "blacklist";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-blacklist']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body class="width-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="content-list-page">

                    <div class="standard-page" style="padding-bottom: 0">

                    <h1><?php echo $LANG['page-blacklist']; ?></h1>

                        <div class="tab-container">
                            <nav class="tabs">
                                <a href="/account/settings"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                <a href="/account/settings/password"><span class="tab"><?php echo $LANG['label-password']; ?></span></a>
                                <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                <a href="/account/settings/blacklist"><span class="tab active"><?php echo $LANG['label-blacklist']; ?></span></a>
                                <a href="/account/settings/deactivation"><span class="tab"><?php echo $LANG['action-deactivation-profile']; ?></span></a>

                            </nav>
                        </div>

                    </div>

                    <?php

                    $result = $blacklist->get(0);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>

                            <ul class="cards-list content-list">

                                <?php

                                    foreach ($result['items'] as $key => $value) {

                                        draw::blackListItem($value, $LANG, $helper);
                                    }
                                ?>

                            </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner empty-list-banner">

                        </header>

                        <?php
                    }
                    ?>

                    <?php

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="BlackList.moreItems('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

            window.BlackList || ( window.BlackList = {} );

            BlackList.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/settings/blacklist',
                    data: 'itemId=' + offset + "&loaded=" + inbox_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('header.loading-banner').remove();

                        if (response.hasOwnProperty('html')){

                            $("ul.content-list").append(response.html);
                        }

                        inbox_loaded = response.inbox_loaded;
                        inbox_all = response.inbox_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
                    }
                });
            };

            BlackList.remove = function(id, profile_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/unblock.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('li.card-item[data-id=' + id + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

        <script type="text/javascript" src="/js/chat.js"></script>

</body>
</html>

<?php

    function draw($item, $LANG, $helper) {

        $time = new language(NULL, $LANG['lang-code']);

        ?>

        <li class="collection-item avatar" data-id="<?php echo $item['id']; ?>">
            <img src="<?php if (strlen($item['blockedUserPhotoUrl']) != 0) { echo $item['blockedUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>" alt="" class="circle">
            <span class="title"><?php echo $item['blockedUserUsername']; ?></span>
            <p>
            <span>@<?php echo $item['blockedUserUsername']; ?></span>
            <br>
            <span class="time"><?php echo $time->timeAgo($item['createAt']); ?></span>
            </p>
            <a href="#!" onclick="BlackList.remove('<?php echo $item['id']; ?>', '<?php echo $item['blockedUserId']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="secondary-content"><i class="material-icons">delete</i></a>
        </li>

        <?php
    }

?>