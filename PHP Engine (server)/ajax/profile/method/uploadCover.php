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

        $act = isset($_GET['action']) ? $_GET['action'] : '';

        switch ($act) {

            case "get-box": {

                ?>

                <div class="box-body">
                    <div class="msg" style="margin-top: 0">
                        <?php echo $LANG['label-image-upload-description']; ?>
                    </div>

                    <div class="file_loader_block" style=""></div>

                    <div class="file_select_block" style="">
                        <div style="" class="file_select_btn cover_input button blue"><?php echo $LANG['action-select-file-and-upload']; ?></div>
                    </div>

                </div>

                <div class="box-footer">
                    <div class="controls">
                        <button onclick="$.colorbox.close(); return false;" class="primary_btn red"><?php echo $LANG['action-cancel']; ?></button>
                    </div>
                </div>

                <?php

                exit;
            }

            default: {

                break;
            }
        }
    }

    $response = array("error" => true);

    if (isset($_FILES['uploaded_file']['name'])) {

        $response['error'] = true;
        $response['message'] = 'Could not move the file!';

        $imgLib = new imglib($dbo);
        $response = $imgLib->createCover($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
        unset($imgLib);

        if ($response['error'] === false) {

            $account = new account($dbo, auth::getCurrentUserId());
            $account->setCover($response);
        }
    }

    echo json_encode($response);
