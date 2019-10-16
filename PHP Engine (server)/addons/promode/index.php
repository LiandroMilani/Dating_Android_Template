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

    ?>

    <div class="box-body">
        <?php echo $LANG['label-pro-mode-alert']; ?>
    </div>

    <div class="box-footer">
        <div class="controls">
            <button onclick="$.colorbox.close(); return false;" class="primary_btn red"><?php echo $LANG['action-close']; ?></button>
        </div>
    </div>
