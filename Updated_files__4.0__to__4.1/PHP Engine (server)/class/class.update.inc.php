<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class update extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    function setChatEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE messages charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setGiftsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE gifts charset = utf8mb4, MODIFY COLUMN message VARCHAR(400) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setPhotosEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos charset = utf8mb4, MODIFY COLUMN comment VARCHAR(400) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function addColumnToUsersTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyBirthday INT(6) UNSIGNED DEFAULT 0 after allowCommentReplyGCM");
        $stmt->execute();
    }

    function addColumnToChatsTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats ADD message varchar(800) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after toUserId_lastView");
        $stmt->execute();
    }

    function addColumnToChatsTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats ADD messageCreateAt INT(11) UNSIGNED DEFAULT 0 after message");
        $stmt->execute();
    }

    function setDialogsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function addColumnToAdminsTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE admins ADD access_level INT(11) UNSIGNED DEFAULT 0 after id");
        $stmt->execute();
    }

    // For version 2.0

    function addColumnToUsersTable15()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowPhotosComments SMALLINT(6) UNSIGNED DEFAULT 1 after allowComments");
        $stmt->execute();
    }

    function setImagesCommentsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE images_comments charset = utf8mb4, MODIFY COLUMN comment VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    // For version 2.3

    function addColumnToGalleryTable1()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos ADD itemType int(11) UNSIGNED DEFAULT 0 after accessMode");
        $stmt->execute();
    }

    function addColumnToGalleryTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos ADD previewVideoImgUrl VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after imgUrl");
        $stmt->execute();
    }

    function addColumnToGalleryTable3()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos ADD videoUrl VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after previewVideoImgUrl");
        $stmt->execute();
    }

    // For version 2.6

    function addColumnToUsersTable1()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyInfo SMALLINT(6) UNSIGNED DEFAULT 1 after allowShowMyBirthday");
        $stmt->execute();
    }

    function addColumnToUsersTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyGallery SMALLINT(6) UNSIGNED DEFAULT 1 after allowShowMyInfo");
        $stmt->execute();
    }

    function addColumnToUsersTable3()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyFriends SMALLINT(6) UNSIGNED DEFAULT 1 after allowShowMyGallery");
        $stmt->execute();
    }

    function addColumnToUsersTable4()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyLikes SMALLINT(6) UNSIGNED DEFAULT 1 after allowShowMyFriends");
        $stmt->execute();
    }

    function addColumnToUsersTable5()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyGifts SMALLINT(6) UNSIGNED DEFAULT 1 after allowShowMyLikes");
        $stmt->execute();
    }

    // For version 2.7

    function addColumnToUsersTable6()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD ios_fcm_regid TEXT after gcm_regid");
        $stmt->execute();
    }

    // For version 2.8

    public function updateUsersTable()
    {
        $stmt = $this->db->prepare("UPDATE users SET allowShowMyLikes = 0, allowShowMyGifts = 0, allowShowMyFriends = 0, allowShowMyGallery = 0, allowShowMyInfo = 0");
        $stmt->execute();
    }

    // For version 3.0

    function addColumnToUsersTable7()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD referrer INT(10) UNSIGNED DEFAULT 0 after ios_fcm_regid");
        $stmt->execute();
    }

    function addColumnToUsersTable8()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD credits_to_referrer INT(10) UNSIGNED DEFAULT 0 after referrer");
        $stmt->execute();
    }

    function addColumnToUsersTable9()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD purchases_count INT(10) UNSIGNED DEFAULT 0 after credits_to_referrer");
        $stmt->execute();
    }

    function addColumnToUsersTable10()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD referrals_count INT(10) UNSIGNED DEFAULT 0 after purchases_count");
        $stmt->execute();
    }

    // For version 3.1

    function addColumnToUsersTable11()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD pro INT(10) UNSIGNED DEFAULT 0 after ghost_create_at");
        $stmt->execute();
    }

    function addColumnToUsersTable12()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD pro_create_at INT(10) UNSIGNED DEFAULT 0 after pro");
        $stmt->execute();
    }

    // For version 3.2

    function addColumnToUsersTable14()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD free_messages_count INT(11) UNSIGNED DEFAULT 150 after balance");
        $stmt->execute();
    }

    // For version 3.4

    function addColumnToMessagesTable1()
    {
        $stmt = $this->db->prepare("ALTER TABLE messages ADD seenAt INT(11) UNSIGNED DEFAULT 0 after removeToUserId");
        $stmt->execute();
    }

    // For version 3.5

    function addColumnToUsersTable16()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD sex_orientation INT(10) UNSIGNED DEFAULT 1 after sex");
        $stmt->execute();
    }

    function addColumnToUsersTable17()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD u_age INT(10) UNSIGNED DEFAULT 18 after sex_orientation");
        $stmt->execute();
    }

    function addColumnToUsersTable18()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD u_height INT(10) UNSIGNED DEFAULT 0 after u_age");
        $stmt->execute();
    }

    function addColumnToUsersTable19()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD u_weight INT(10) UNSIGNED DEFAULT 0 after u_height");
        $stmt->execute();
    }

    function addColumnToUsersTable20()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMyAge SMALLINT(6) UNSIGNED DEFAULT 0 after allowShowMyGifts");
        $stmt->execute();
    }

    function addColumnToUsersTable21()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowMySexOrientation SMALLINT(6) UNSIGNED DEFAULT 0 after allowShowMyAge");
        $stmt->execute();
    }

    function addColumnToUsersTable22()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowShowOnline SMALLINT(6) UNSIGNED DEFAULT 0 after allowShowMySexOrientation");
        $stmt->execute();
    }

    function addColumnToUsersTable23()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD last_matches_view INT(10) UNSIGNED DEFAULT 0 after last_friends_view");
        $stmt->execute();
    }

    function addColumnToUsersTable24()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD matches_count INT(11) UNSIGNED DEFAULT 0 after friends_count");
        $stmt->execute();
    }

    function addColumnToUsersTable25()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowMatchesGCM SMALLINT(6) UNSIGNED DEFAULT 1 after allowLikesGCM");
        $stmt->execute();
    }

    function addColumnToUsersTable26()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD feeling INT(10) UNSIGNED DEFAULT 0 after vip_create_at");
        $stmt->execute();
    }

    // For version 4.1

    function addColumnToGalleryTable4()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos ADD itemShowInStream int(11) UNSIGNED DEFAULT 1 after itemType");
        $stmt->execute();
    }
}
