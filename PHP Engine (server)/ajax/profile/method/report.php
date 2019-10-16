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

    if (isset($_GET['action'])) {

        $user_id = helper::clearInt($_GET['user_id']);

        ?>

        <div class="box-body">
            <div class="msg" style="margin-top: 0">
                <?php echo $LANG['page-profile-report-sub-title']; ?>
            </div>
            <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $user_id; ?>', '0', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
            <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $user_id; ?>', '1', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
            <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $user_id; ?>', '2', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
            <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $user_id; ?>', '3', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-4']; ?></a>
        </div>

        <div class="box-footer">
            <div class="controls">
                <button onclick="$.colorbox.close(); return false;" class="primary_btn red"><?php echo $LANG['action-close']; ?></button>
            </div>
        </div>

        <?php

        exit;
    }

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : 0;

        $reason = isset($_POST['reason']) ? $_POST['reason'] : 0;

        $profile_id = helper::clearInt($profile_id);

        $reason = helper::clearInt($reason);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $profile = new profile($dbo, $profile_id);
        $profile->setRequestFrom(auth::getCurrentUserId());

        if ($reason >= 0 && $reason < 4) {

            $result = $profile->reportAbuse($reason);
        }

        echo json_encode($result);
        exit;
    }
