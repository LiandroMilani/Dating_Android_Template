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

$result = array("error" => true);

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if (isset($_FILES['uploaded_file']['name'])) {

        $response = array();

        $imgLib = new imglib($dbo);
        $response = $imgLib->createMyPhoto($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);

        if ($response['error'] === false) {

            $result = array("error" => false,
                            "originPhotoUrl" => $response['originPhotoUrl'],
                            "normalPhotoUrl" => $response['normalPhotoUrl'],
                            "previewPhotoUrl" => $response['previewPhotoUrl']);
        }

        unset($imgLib);
    }

    echo json_encode($result);
    exit;
}
