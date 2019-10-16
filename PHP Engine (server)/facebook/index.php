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

    if (auth::isSession()) {

        header("Location: /");
        exit;
    }

    if (isset($_SESSION['oauth']) && $_SESSION['oauth'] === 'facebook') {

        unset($_SESSION['oauth']);
        unset($_SESSION['oauth_id']);
        unset($_SESSION['oauth_name']);
        unset($_SESSION['oauth_email']);
        unset($_SESSION['oauth_link']);

        header("Location: /signup");
        exit;
    }

    header("Location: /");