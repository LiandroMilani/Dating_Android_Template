<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class moderator extends db_connect
{
	private $requestFrom = 0;
    private $language = 'en';

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    private function getMaxId()
    {
        $stmt = $this->db->prepare("SELECT MAX(id) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getAllCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function post($accountId)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE users SET accountPostModerateAt = (:accountPostModerateAt), accountModerateAt = 0, accountRejectModerateAt = 0 WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accountPostModerateAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                "error" => false,
                "error_code" => ERROR_SUCCESS);

            $notify = new notify($this->db);
            $notify->removeNotify($accountId, 0, NOTIFY_TYPE_ACCOUNT_APPROVE, $accountId);
            $notify->removeNotify($accountId, 0, NOTIFY_TYPE_ACCOUNT_REJECT, $accountId);
            unset($notify);
        }

        return $result;
    }

    public function approve($accountId)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE users SET accountModerateAt = (:accountModerateAt), accountRejectModerateAt = 0 WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accountModerateAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                "error" => false,
                "error_code" => ERROR_SUCCESS);

            $gcm = new gcm($this->db, $accountId);
            $gcm->setData(GCM_NOTIFY_ACCOUNT_APPROVE, "You account is approved.", $accountId);
            $gcm->send();

            $notify = new notify($this->db);
            $notify->createNotify($accountId, 0, NOTIFY_TYPE_ACCOUNT_APPROVE, $accountId);
            unset($notify);
        }

        return $result;
    }

    public function reject($accountId)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE users SET accountRejectModerateAt = (:accountRejectModerateAt) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":accountRejectModerateAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                "error" => false,
                "error_code" => ERROR_SUCCESS);

            $gcm = new gcm($this->db, $accountId);
            $gcm->setData(GCM_NOTIFY_ACCOUNT_REJECT, "You account is rejected.", $accountId);
            $gcm->send();

            $notify = new notify($this->db);
            $notify->createNotify($accountId, 0, NOTIFY_TYPE_ACCOUNT_REJECT, $accountId);
            unset($notify);
        }

        return $result;
    }

    public function getNotModerated($itemId = 0, $language = 'en')
    {
        if ($itemId == 0) {

            $itemId = time();
        }

        $result = array("error" => false,
            "error_code" => ERROR_SUCCESS,
            "itemId" => $itemId,
            "items" => array());

        $stmt = $this->db->prepare("SELECT id, accountPostModerateAt FROM users WHERE accountModerateAt = 0 AND accountRejectModerateAt = 0 AND accountPostModerateAt < (:accountPostModerateAt) ORDER BY accountPostModerateAt DESC LIMIT 20");
        $stmt->bindParam(':accountPostModerateAt', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);

                    array_push($result['items'], $profile->getVeryShort());

                    $result['itemId'] = $row['accountPostModerateAt'];

                    unset($profile);
                }
            }
        }

        return $result;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}
