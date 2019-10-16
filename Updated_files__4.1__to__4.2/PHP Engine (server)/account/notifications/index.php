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

    if (isset($_GET['action'])) {

        $notifications = new notify($dbo);
        $notifications->setRequestFrom(auth::getCurrentUserId());

        $notifications_count = $notifications->getNewCount($profile->getLastNotifyView());

        echo $notifications_count;
        exit;
    }

    $profile->setLastActive();

    $profile->setLastNotifyView();

    $notifications = new notify($dbo);
    $notifications->setRequestFrom(auth::getCurrentUserId());

    $items_all = $notifications->getAllCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $notifyId = isset($_POST['notifyId']) ? $_POST['notifyId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $notifyId = helper::clearInt($notifyId);
        $loaded = helper::clearInt($loaded);

        $result = $notifications->getAll($notifyId);

        $items_loaded = count($result['notifications']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['notifications'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Notifications.moreItems('<?php echo $result['notifyId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "notifications";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-notifications-likes']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-notifications']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['page-notifications-description']; ?>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $notifications->getAll(0);

                    $items_loaded = count($result['notifications']);

                    if ($items_loaded != 0) {

                        ?>

                            <ul class="cards-list content-list">

                                <?php

                                    foreach ($result['notifications'] as $key => $value) {

                                        draw($value, $LANG, $helper);
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
                                    <button onclick="Notifications.moreItems('<?php echo $result['notifyId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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

            window.Notifications || ( window.Notifications = {} );

            Notifications.moreItems = function (offset) {

                $.ajax({
                    type: 'POST',
                    url: '/account/notifications/',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('header.loading-banner').remove();

                        if (response.hasOwnProperty('html')){

                            $("ul.content-list").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                    }
                });
            };

            window.Friends || ( window.Friends = {} );

            Friends.acceptRequest = function (id, friend_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/acceptRequest.php',
                    data: 'friend_id=' + friend_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('li.card-item[data-id=' + id + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

            Friends.rejectRequest = function (id, friend_id, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/rejectRequest.php',
                    data: 'friend_id=' + friend_id + "&access_token=" + access_token,
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

    function draw($notify, $LANG, $helper)
    {
        $time = new language(NULL, $LANG['lang-code']);
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($notify['fromUserPhotoUrl']) != 0) {

            $profilePhotoUrl = $notify['fromUserPhotoUrl'];
        }

        switch ($notify['type']) {

            case NOTIFY_TYPE_LIKE: {

                ?>

                    <li class="card-item classic-item default-item" data-id="<?php echo $notify['id']; ?>">
                        <div class="card-body">
                            <span class="card-header">
                                <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/></a>
                                <span title="" class="card-notify-icon like"></span>
                                <?php if ($notify['fromUserOnline']) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                                <div class="card-content">
                                    <span class="card-title">
                                        <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><?php echo  $notify['fromUserFullname']; ?></a>
                                        <?php

                                            if ($notify['fromUserVerified'] == 1) {

                                                ?>
                                                    <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                                <?php
                                            }
                                        ?>
                                        <span class="sub-title"><?php echo $LANG['label-notify-profile-like']; ?></span>
                                    </span>
                                    <span class="card-username">@<?php echo  $notify['fromUserUsername']; ?></span>
                                    <span class="card-counter black"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                                    <span class="card-action">
                                        <a href="/account/likes" class="card-act active"><?php echo $LANG['action-view']; ?> »</a>
                                    </span>
                                </div>
                            </span>
                        </div>
                    </li>

                <?php

                break;
            }

            case NOTIFY_TYPE_FOLLOWER: {

                ?>

                    <li class="card-item classic-item default-item" data-id="<?php echo $notify['id']; ?>">
                        <div class="card-body">
                            <span class="card-header">
                                <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/></a>
                                <span title="" class="card-notify-icon friend-request"></span>
                                <?php if ($notify['fromUserOnline']) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                                <div class="card-content">
                                    <span class="card-title">
                                        <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><?php echo  $notify['fromUserFullname']; ?></a>
                                        <?php

                                            if ($notify['fromUserVerified'] == 1) {

                                                ?>
                                                    <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                                <?php
                                            }
                                        ?>
                                        <span class="sub-title"><?php echo $LANG['label-notify-request-to-friends']; ?></span>
                                    </span>
                                    <span class="card-username">@<?php echo  $notify['fromUserUsername']; ?></span>
                                    <span class="card-counter black"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                                    <span class="card-action">
                                        <a class="card-act negative" href="javascript:void(0)" onclick="Friends.rejectRequest('<?php echo $notify['id']; ?>', '<?php echo $notify['fromUserId']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-reject']; ?></a>
                                        <a class="card-act active" href="javascript:void(0)" onclick="Friends.acceptRequest('<?php echo $notify['id']; ?>', '<?php echo $notify['fromUserId']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-accept']; ?></a>
                                    </span>
                                </div>
                            </span>
                        </div>
                    </li>

                <?php

                break;
            }

            case NOTIFY_TYPE_GIFT: {

                ?>

                    <li class="card-item classic-item default-item" data-id="<?php echo $notify['id']; ?>">
                        <div class="card-body">
                            <span class="card-header">
                                <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/></a>
                                <span title="" class="card-notify-icon gift"></span>
                                <?php if ($notify['fromUserOnline']) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                                <div class="card-content">
                                    <span class="card-title">
                                        <a href="/profile.php/?id=<?php echo $notify['fromUserId']; ?>"><?php echo  $notify['fromUserFullname']; ?></a>
                                        <?php

                                            if ($notify['fromUserVerified'] == 1) {

                                                ?>
                                                    <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                                <?php
                                            }
                                        ?>
                                        <span class="sub-title"><?php echo $LANG['label-new-gift']; ?></span>
                                    </span>
                                    <span class="card-username">@<?php echo  $notify['fromUserUsername']; ?></span>
                                    <span class="card-counter black"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                                </div>
                            </span>
                        </div>
                    </li>

                <?php

                break;
            }

            case NOTIFY_TYPE_MEDIA_APPROVE: {

                ?>

                <li class="card-item classic-item default-item" data-id="<?php echo $notify['id']; ?>">
                    <div class="card-body">
                            <span class="card-header">
                                <a href="javascript:void(0)"><img class="card-icon" src="/img/def_photo.png"/></a>
                                <span title="" class="card-notify-icon approved"></span>
                                <div class="card-content">
                                    <span class="card-title">
                                        <a href="javascript:void(0)"><?php echo APP_NAME; ?></a>
                                        <span class="sub-title"><?php echo $LANG['label-notify-media-approve']; ?></span>
                                    </span>
                                    <span class="card-counter black"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                                </div>
                            </span>
                    </div>
                </li>

                <?php

                break;
            }

            case NOTIFY_TYPE_MEDIA_REJECT: {

                ?>

                <li class="card-item classic-item default-item" data-id="<?php echo $notify['id']; ?>">
                    <div class="card-body">
                            <span class="card-header">
                                <a href="javascript:void(0)"><img class="card-icon" src="/img/def_photo.png"/></a>
                                <span title="" class="card-notify-icon rejected"></span>
                                <div class="card-content">
                                    <span class="card-title">
                                        <a href="javascript:void(0)"><?php echo APP_NAME; ?></a>
                                        <span class="sub-title"><?php echo $LANG['label-notify-media-reject']; ?></span>
                                    </span>
                                    <span class="card-counter black"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                                </div>
                            </span>
                    </div>
                </li>

                <?php

                break;
            }

            default: {


                break;
            }
        }
    }

?>
