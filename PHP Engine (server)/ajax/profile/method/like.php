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

        $profileId = isset($_POST['profile_id']) ? $_POST['profile_id'] : 0;

        $profileId = helper::clearInt($profileId);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (auth::getAccessToken() === $access_token) {

            $profile = new profile($dbo, $profileId);
            $profile->setRequestFrom(auth::getCurrentUserId());

            $result = $profile->like(auth::getCurrentUserId());
        }

        echo json_encode($result);
        exit;
    }
