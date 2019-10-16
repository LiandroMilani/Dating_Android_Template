<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class api extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    static function printError($error_code, $error_description = "unknown")
    {
        $result = array("error" => true,
                        "error_code" => $error_code,
                        "error_description" => $error_description);

        echo json_encode($result);
        exit;
    }
}
