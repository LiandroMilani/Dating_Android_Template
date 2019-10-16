<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class app extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function getUsersPreview($limit = 6)
    {
        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "items" => array());

        $stmt = $this->db->prepare("SELECT id, regtime FROM users WHERE state = 0 AND lowPhotoUrl <> '' ORDER BY regtime DESC LIMIT :lmt");
        $stmt->bindParam(":lmt", $limit, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($row['id']);

                    array_push($result['items'], $profile->getVeryShort());

                    unset($profile);
                }
            }
        }

        return $result;
    }
}
