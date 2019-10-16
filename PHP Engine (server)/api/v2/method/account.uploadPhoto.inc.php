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

$response = array("error" => true);

if (!empty($_POST)) {

    // reading other post parameters
    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    if (isset($_FILES['uploaded_file']['name'])) {

        // make error flag true
        $response['error'] = true;
        $response['message'] = 'Could not move the file!';

        $imglib = new imglib($dbo);
        $response = $imglib->createPhoto($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
        unset($imglib);
    }

    echo json_encode($response);
}
