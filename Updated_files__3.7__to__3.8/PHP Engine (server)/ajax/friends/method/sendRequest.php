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
    }

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $blacklist = new blacklist($dbo);
        $blacklist->setRequestFrom($profile_id);

        if (!$blacklist->isExists(auth::getCurrentUserId())) {

            $profile = new profile($dbo, $profile_id);
            $profile->setRequestFrom(auth::getCurrentUserId());

            $result = $profile->addFollower(auth::getCurrentUserId());

            ob_start();

            if ($result['follow']) {

                ?>
                    <a onclick="Profile.sendRequest('<?php echo $profile_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-cancel-friend-request']; ?></a>
                <?php

            } else {

                ?>
                    <a onclick="Profile.sendRequest('<?php echo $profile_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-add-to-friends']; ?></a>
                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }
