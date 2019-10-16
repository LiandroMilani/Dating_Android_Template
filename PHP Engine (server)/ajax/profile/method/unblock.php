<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT'] . "/core/init.inc.php");

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (auth::getAccessToken() === $access_token) {

            $blacklist = new blacklist($dbo);
            $blacklist->setRequestFrom(auth::getCurrentUserId());

            $result = $blacklist->remove($profile_id);

            ob_start();

            ?>
                <a onclick="Profile.block('<?php echo $profile_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="flat_btn noselect block-btn"><?php echo $LANG['action-block']; ?></a>
            <?php

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }
