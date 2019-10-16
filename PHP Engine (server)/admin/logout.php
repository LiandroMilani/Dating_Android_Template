<?php

    /*!
     * ifsoft.co.uk admin engine v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!admin::isSession()) {

        header('Location: /');
        exit;
    }

    if (isset($_GET['access_token'])) {

        $accessToken = (isset($_GET['access_token'])) ? ($_GET['access_token']) : '';
        $continue = (isset($_GET['continue'])) ? ($_GET['continue']) : '/';

        if (admin::getAccessToken() === $accessToken) {

            admin::unsetSession();

            header('Location: '.$continue);
            exit;
        }
    }

    header('Location: /');
    exit;