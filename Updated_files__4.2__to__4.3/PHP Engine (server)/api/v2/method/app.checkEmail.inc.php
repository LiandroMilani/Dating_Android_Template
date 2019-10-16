<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/config/api.inc.php");

if (!empty($_POST)) {

    $email = isset($_POST['email']) ? $_POST['email'] : '';

    $email = helper::clearText($email);
    $email = helper::escapeText($email);

    $result = array("error" => true);

    if (!$helper->isEmailExists($email)) {

        $result = array("error" => false);
    }

    echo json_encode($result);
    exit;
}
