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

    $showForm = true;

    $chat_id = 0;
    $user_id = 0;

    $my_info = array();
    $my_profile = new account($dbo, auth::getCurrentUserId());
    $my_info = $my_profile->get();

    $chat_info = array("messages" => array());
    $user_info = array();

    $messages = new messages($dbo);
    $messages->setRequestFrom(auth::getCurrentUserId());

    if (!isset($_GET['chat_id']) && !isset($_GET['user_id'])) {

        header('Location: /');
        exit;

    } else {

        $chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : 0;
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

        $chat_id = helper::clearInt($chat_id);
        $user_id = helper::clearInt($user_id);

        $user = new profile($dbo, $user_id);
        $user->setRequestFrom(auth::getCurrentUserId());
        $user_info = $user->get();
        unset($user);

        if ($user_info['error'] === true) {

            header('Location: /');
            exit;
        }

        $chat_id_test = $messages->getChatId(auth::getCurrentUserId(), $user_id);

        if ($chat_id != 0 && $chat_id_test != $chat_id) {

            header('Location: /');
            exit;
        }

        if ($chat_id == 0) {

            $chat_id = $messages->getChatId(auth::getCurrentUserId(), $user_id);

            if ($chat_id != 0) {

                header('Location: /chat.php/?chat_id='.$chat_id.'&user_id='.$user_id);
                exit;
            }
        }

        if ($chat_id != 0) {

            $chat_info = $messages->get($chat_id, 0);
        }
    }

    if ($user_info['state'] != ACCOUNT_STATE_ENABLED) {

        $showForm = false;
    }

    if ($user_info['allowMessages'] == 0 && $user_info['friend'] === false) {

        $showForm = false;
    }

    if ($my_info['accountModerateAt'] == 0) {

        $showForm = false;
    }

    $blacklist = new blacklist($dbo);
    $blacklist->setRequestFrom($user_info['id']);

    if ($blacklist->isExists(auth::getCurrentUserId())) {

        $showForm = false;
    }

    $items_all = $messages->messagesCountByChat($chat_id);
    $items_loaded = 0;

    $page_id = "chat";

    $css_files = array("my.css", "account.css");
    $page_title = $LANG['page-messages']." | ".APP_TITLE;

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
                        <?php echo $user_info['fullname']; ?>
                    </div>
                </div>

				<div class="content-list-page">

                    <?php

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Messages.more('<?php echo $chat_id ?>', '<?php echo $user_id ?>'); return false;" class="button more loading-button noselect"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                            }

                        ?>

                        <?php

                            $result = $chat_info;

                            $items_loaded = count($result['messages']);

                            if ($items_loaded != 0) {

                                ?>
                                    <ul class="cards-list content-list">
                                <?php

                                foreach (array_reverse($result['messages']) as $key => $value) {

                                    draw::messageItem($value, $LANG, $helper);
                                }

                                ?>
                                    </ul>
                                <?php
                            }

                        ?>

                        <?php

                            if ($items_loaded == 0) {

                                ?>
                                    <header class="top-banner info-banner">

                                        <div class="info">
                                            <h1><?php echo $LANG['label-empty-list']; ?></h1>
                                        </div>

                                    </header>
                                <?php
                            }
                        ?>

                        <?php

                            if ($my_info['accountModerateAt'] == 0) {

                                ?>

                                <header class="top-banner info-banner" style="background: #fff">

                                    <div class="info">
                                        <h1><?php echo $LANG['dlg-moderation-title']; ?></h1>
                                        <h3><?php echo $LANG['dlg-moderation-subtitle']; ?></h3>
                                        <h3><?php echo $LANG['msg-account-not-moderated']; ?></h3>
                                    </div>

                                </header>

                                <?php
                            }

                            if ($showForm) {

                                ?>

                                    <div class="comment_form comment-form standard-page">

                                        <form class="" onsubmit="

                                            <?php

                                                if (auth::getCurrentProMode() == 0 && auth::getCurrentFreeMessagesCount() < 1) {

                                                    ?>
                                                        Messages.showProAlert(); return false;
                                                    <?php

                                                } else {

                                                    ?>
                                                        Messages.create('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;
                                                    <?php
                                                }
                                            ?>

                                        ">
                                            <input type="hidden" name="message_image" value="">
                                            <input class="comment_text" name="message_text" maxlength="340" placeholder="<?php echo $LANG['label-placeholder-message']; ?>" type="text" value="">
                                            <button style="display: inline-block; padding: 7px 16px;" class="primary_btn red comment_send"><?php echo $LANG['action-send']; ?></button>
                                            <a onclick="Messages.changeChatImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/camera.png">
                                            </a>
                                        </form>

                                    </div>

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

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;
            var chat_id = <?php echo $chat_id; ?>;

            if (chat_id != 0) {

                App.chatInit('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>');
            }

            window.Messages || ( window.Messages = {} );

            Messages.showProAlert = function() {

                var url = "/addons/promode/";
                $.colorbox({width:"450px", href: url, title: "<?php echo APP_NAME; ?>", fixed:true});
            };

        </script>


</body>
</html>