<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/config/api.inc.php");

$response = array();

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : '';
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if (isset($_FILES['uploaded_file']['name'])) {

        // make error flag true
        $response['error'] = true;
        $response['message'] = 'Could not move the file!';

        $imgLib = new imglib($dbo);
        $response = $imgLib->createCover($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
        unset($imgLib);

        if ($response['error'] === false) {

            $account = new account($dbo, $accountId);
            $account->setCover($response);
        }
    }

    echo json_encode($response);
}
