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

    if (isset($_GET['id'])) {

        $profile_id = isset($_GET['id']) ? $_GET['id'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $profile = new profile($dbo, $profile_id);

        $profile->setRequestFrom(auth::getCurrentUserId());
        $profileInfo = $profile->get();

    } else {

        header("Location: /");
        exit;
    }

    if ($profileInfo['error'] === true) {

        header("Location: /");
        exit;
    }

    $myPage = false;

    if ($profileInfo['id'] == auth::getCurrentUserId()) {

        $page_id = "my-profile";

        $account = new account($dbo, $profileInfo['id']);
        $account->setLastActive();
        unset($account);

        $myPage = true;

    } else {

        $page_id = "profile";

        if ($profileInfo['ghost'] == 0) {

            $guests = new guests($dbo, $profileInfo['id']);
            $guests->setRequestFrom(auth::getCurrentUserId());

            $guests->add(auth::getCurrentUserId());
        }


    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("stubs/profile.php");
        exit;
    }

    $profileCoverUrl = $profileInfo['normalCoverUrl'];

    if (strlen($profileCoverUrl) == 0) {

        $profileCoverUrl = "/img/cover_none.png?x=1";
    }

    auth::newAuthenticityToken();

    $page_id = "profile";

    $css_files = array("my.css", "account.css");
    $page_title = $profileInfo['fullname']." | ".APP_TITLE;

    include_once($_SERVER['DOCUMENT_ROOT']."/common/site_header.inc.php");

?>

<body class="width-page">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/site_topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">


                <div class="profile_cover" style="background-image: url(<?php echo $profileCoverUrl; ?>); background-position: <?php echo $profileInfo['coverPosition']; ?>">

                    <?php

                    if ($myPage) {

                        ?>

                        <div class="profile_add_cover">
                            <span class="cover_button" onclick="Profile.changePhoto('<?php echo $LANG['action-change-photo']; ?>'); return false;" ><?php echo $LANG['action-change-photo']; ?></span>
                            <span class="cover_button" onclick="Profile.changeCover('<?php echo $LANG['action-change-image']; ?>'); return false;" ><?php echo $LANG['page-profile-upload-cover']; ?></span>
                        </div>

                        <?php
                    }
                    ?>
                </div>

                <div id="addon_block">
                    <?php

                    if (auth::isSession() && $myPage) {

                        ?>

                        <a href="/account/settings/" class="flat_btn noselect"><?php echo $LANG['action-edit-profile']; ?></a>

                        <?php
                    }

                    if (!$myPage) {

                        ?>

                            <?php

                                if ($profileInfo['friend']) {

                                    ?>
                                        <a onclick="Friends.remove('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-remove-from-friends']; ?></a>
                                    <?php

                                } else {

                                    if ($profileInfo['follow']) {

                                        ?>
                                            <a onclick="Profile.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-cancel-friend-request']; ?></a>
                                        <?php

                                    } else {

                                        ?>
                                            <a onclick="Profile.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"  class="flat_btn noselect friends-btn" ><?php echo $LANG['action-add-to-friends']; ?></a>
                                        <?php
                                    }
                                }
                            ?>

                            <?php

                                if (!$profileInfo['myLike']) {

                                    ?>

                                    <a onclick="Profile.like('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect like-btn">Like</a>

                                    <?php

                                }
                            ?>

                            <?php

                                if (auth::getCurrentProMode() == 0 && auth::getCurrentFreeMessagesCount() < 1) {

                                    ?>
                                        <a onclick="Profile.getProModeBox('<?php echo APP_NAME; ?>'); return false;" class="flat_btn noselect"><?php echo $LANG['action-send-message']; ?></a>
                                    <?php

                                } else {

                                    if ($profileInfo['allowMessages'] == 1 || ($profileInfo['allowMessages'] == 0 && $profileInfo['friend'])) {

                                        ?>

                                            <a href="/chat.php/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" style="" class="flat_btn noselect"><?php echo $LANG['action-send-message']; ?></a>

                                        <?php
                                    }
                                }

                            ?>

                            <a onclick="Profile.getReportBox('<?php echo $profileInfo['id']; ?>', '<?php echo $LANG['page-profile-report']; ?>'); return false;" class="flat_btn noselect"><?php echo $LANG['action-report']; ?></a>

                            <?php

                                if ($profileInfo['blocked']) {

                                    ?>
                                        <a onclick="Profile.unblock('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect block-btn"><?php echo $LANG['action-unblock']; ?></a>
                                    <?php

                                } else {

                                    ?>
                                        <a onclick="Profile.block('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect block-btn"><?php echo $LANG['action-block']; ?></a>
                                    <?php
                                }
                            ?>

                        <?php
                    }
                    ?>
                </div>

                <div class="profile-content standard-page">

                    <div class="user-info">

                        <div class="">

                            <a href="javascript: void(0)" data-img="<?php echo $profileInfo['bigPhotoUrl']; ?>" class="profile_img_wrap">
                                <span alt="Photo" class="profile-user-photo user_image profile-user-photo-bg" style="background-image: url('<?php if ( strlen($profileInfo['bigPhotoUrl']) != 0 ) { echo $profileInfo['bigPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>') "></span>
                            </a>

                            <div class="basic-info">
                                <h1>
                                    <?php echo $profileInfo['fullname']; ?>
                                    <?php

                                        if ($profileInfo['verify'] == 1) {

                                            ?>
                                                <span class="page_verified" original-title="<?php echo $LANG['label-account-verified']; ?>"></span>
                                            <?php
                                        }
                                    ?>
                                </h1>

                                <h4 style="margin: 0">@<?php echo $profileInfo['username']; ?></h4>

                                <?php

                                    if ($profileInfo['online']) {

                                        ?>
                                            <span class="info-item info-item-online">Online</span>
                                        <?php

                                    } else {

                                        if ($profileInfo['lastAuthorize'] == 0) {

                                            ?>
                                                <span class="info-item info-item-online">Offline</span>
                                            <?php

                                        } else {

                                            ?>
                                                <span class="info-item info-item-online"><?php echo $profileInfo['lastAuthorizeTimeAgo']; ?></span>
                                            <?php
                                        }
                                    }
                                ?>

                            </div>

                        </div>
                    </div>



                    <!--   <div class="profile-content standard-page"> END-->
                </div>

            </div>

            <div class="main-content profile-info-content">

                <div class="standard-page">

                    <?php

                        include_once("stubs/profile_info_content.inc.php");
                    ?>
                </div>
            </div>

        </div>

    </div>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/site_footer.inc.php");
        ?>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            $(document).on("click", "span.user_image", function() {

                var url = $(this).parent().attr("data-img");

                if (url.length != 0) {

                    $.colorbox({maxWidth:"80%", maxHeight:"80%", href:url, title: "", photo: true});
                }

                return false;
            });

            window.Friends || ( window.Friends = {} );

            Friends.remove = function (friend_id, access_token) {

                $("a.friends-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/remove.php',
                    data: 'friend_id=' + friend_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("a.friends-btn").remove();
                            $("div#addon_block").prepend(response.html);
                        }
                    },
                    error: function(xhr, type){

                        $("a.friends-btn").show();
                    }
                });
            };

            window.Profile || ( window.Profile = {} );

            Profile.getReportBox = function(user_id, title) {

                var url = "/ajax/profile/method/report.php/?action=get-box&user_id=" + user_id;
                $.colorbox({width:"450px", href: url, title: title, fixed:true});
            };

            Profile.getProModeBox = function(title) {

                var url = "/addons/promode/";
                $.colorbox({width:"450px", href: url, title: title, fixed:true});
            };

            Profile.sendReport = function (profile_id, reason, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/report.php',
                    data: 'profile_id=' + profile_id + "&reason=" + reason + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        $.colorbox.close();

                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.sendRequest = function (profile_id, access_token) {

                $("a.friends-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/friends/method/sendRequest.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("a.friends-btn").remove();
                            $("div#addon_block").prepend(response.html);
                        }
                    },
                    error: function(xhr, type){

                        $("a.friends-btn").show();
                    }
                });
            };

            Profile.block = function (profile_id, access_token) {

                $("a.block-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/block.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("a.block-btn").remove();
                            $("div#addon_block").append(response.html);
                        }
                    },
                    error: function(xhr, type){

                        $("a.block-btn").show();
                    }
                });
            };

            Profile.unblock = function (profile_id, access_token) {

                $("a.block-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/unblock.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        if (response.hasOwnProperty('html')) {

                            $("a.block-btn").remove();
                            $("div#addon_block").append(response.html);
                        }
                    },
                    error: function(xhr, type){

                        $("a.block-btn").show();
                    }
                });
            };

            Profile.like = function (profile_id, access_token) {

                $("a.like-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/like.php',
                    data: 'profile_id=' + profile_id + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("a.like-btn").remove();
                    },
                    error: function(xhr, type){

                        $("a.like-btn").show();
                    }
                });
            };

            Profile.changePhoto = function(title) {

                var url = "/ajax/profile/method/uploadPhoto.php/?action=get-box";
                $.colorbox({width:"450px", href: url, title: title, overlayClose: false, fixed:true, onComplete: function(){

                    $('.cover_input').upload({
                        name: 'uploaded_file',
                        method: 'post',
                        enctype: 'multipart/form-data',
                        action: '/ajax/profile/method/uploadPhoto.php',
                        onComplete: function(text) {

                            var response = JSON.parse(text);

                            if (response.hasOwnProperty('error')) {

                                if (response.error === false) {

                                    if (response.hasOwnProperty('bigPhotoUrl')) {

                                        $('span.user_image').css("background-image", "url(\"" + response.bigPhotoUrl + "\")");
                                        $('a.profile_img_wrap').attr("data-img", response.bigPhotoUrl);

                                        PhotoExists = true;

                                        $.colorbox.close();
                                    }
                                }
                            }

                            $("div.file_loader_block").hide();
                            $("div.file_select_block").show();
                        },
                        onSubmit: function() {

                            $("div.file_select_block").hide();
                            $("div.file_loader_block").show();
                        }
                    });
                }});
            };

            Profile.changeCover = function(title) {

                var url = "/ajax/profile/method/uploadCover.php/?action=get-box";
                $.colorbox({width:"450px", href: url, title: title, overlayClose: false, fixed:true, onComplete: function(){

                    $('.cover_input').upload({
                        name: 'uploaded_file',
                        method: 'post',
                        enctype: 'multipart/form-data',
                        action: '/ajax/profile/method/uploadCover.php',
                        onComplete: function(text) {

                            var response = JSON.parse(text);

                            if (response.hasOwnProperty('error')) {

                                //alert(response.normalCoverUrl);

                                if (response.error === false) {

                                    if (response.hasOwnProperty('normalCoverUrl')) {

                                        $('div.profile_cover').css("background-image", "url(" + response.normalCoverUrl + ")");
                                        $('div.profile_cover').css("background-position", "0px 0px");

                                        CoverExists = true;

                                        $.colorbox.close();
                                    }
                                }
                            }

                            $("div.file_loader_block").hide();
                            $("div.file_select_block").show();
                        },
                        onSubmit: function() {

                            $("div.file_select_block").hide();
                            $("div.file_loader_block").show();
                        }
                    });
                }});
            };

        </script>

</body>
</html>