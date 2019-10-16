<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class draw extends db_connect
{
	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    static function friendItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['friendUserPhoto']) != 0) {

            $profilePhotoUrl = $profile['friendUserPhoto'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/profile.php/?id=<?php echo $profile['friendUserId']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')" src="<?php echo $profilePhotoUrl; ?>"></span>
                    </div>
                </a>



                <?php

                if ($profile['friendUserOnline']) {

                    ?>
                        <i class="online-label"></i>
                    <?php

                } else {

                    ?>
                        <span class="card-counter black noselect cardview-item-badge" original-title="<?php echo $LANG['label-last-seen']; ?>"><?php echo $profile['timeAgo']; ?></span>
                    <?php
                }
                ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/profile.php/?id=<?php echo $profile['friendUserId']; ?>">
                            <?php echo $profile['friendUserFullname']; ?>
                        </a>
                        <?php
                            if ($profile['friendUserVerify']) {

                                ?>
                                    <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                <?php
                            }
                        ?>
                    </h4>
                    <?php
                        if (strlen($profile['friendLocation']) > 0) {

                            ?>
                                <div class="gray-text"><?php echo $profile['friendLocation']; ?></div>
                            <?php
                        }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }

    static function guestItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['guestUserPhoto']) != 0) {

            $profilePhotoUrl = $profile['guestUserPhoto'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/profile.php/?id=<?php echo $profile['guestUserId']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')" src="<?php echo $profilePhotoUrl; ?>"></span>
                    </div>
                </a>


                <span class="card-counter black noselect cardview-item-badge" original-title="<?php echo $LANG['label-last-visit']; ?>"><?php echo $profile['timeAgo']; ?></span>

                <?php

                    if ($profile['guestUserOnline']) {

                        ?>
                            <i class="online-label"></i>
                        <?php
                    }
                ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/profile.php/?id=<?php echo $profile['guestUserId']; ?>">
                            <?php echo $profile['guestUserFullname']; ?>
                        </a>
                        <?php
                        if ($profile['guestUserVerify']) {

                            ?>
                            <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                            <?php
                        }
                        ?>
                    </h4>
                    <?php
                    if (strlen($profile['guestUserLocation']) > 0) {

                        ?>
                        <div class="gray-text"><?php echo $profile['guestUserLocation']; ?></div>
                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }

    static function peopleItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

        <li class="card-item classic-item">
            <a href="/profile.php?id=<?php echo $profile['id']; ?>" class="card-body">
                    <span class="card-header">
                        <img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/>
                        <?php if ($profile['online']) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                        <div class="card-content">
                            <span class="card-title"><?php echo $profile['fullname']; ?>

                                <?php

                                if ($profile['verify']) {

                                    ?>
                                    <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                    <?php
                                }
                                ?>
                            </span>
                            <span class="card-username">@<?php echo $profile['username']; ?></span>

                            <?php

                            if (strlen($profile['location']) > 0) {

                                ?>
                                <span class="card-location"><?php echo $profile['location']; ?></span>
                                <?php
                            }

                            if ($profile['online']) {

                                ?>
                                <span class="card-counter green">Online</span>
                                <?php

                            } else {

                                ?>
                                <span title="<?php echo $LANG['label-last-seen']; ?>" class="card-counter black"><?php echo $profile['lastAuthorizeTimeAgo']; ?></span>
                                <?php
                            }
                            ?>
                        </div>
                    </span>
            </a>
        </li>

        <?php
    }

    static function blackListItem($profile, $LANG, $helper = null)
    {
        ?>

        <li class="card-item classic-item" data-id="<?php echo $profile['id']; ?>">
            <a href="/profile.php/?id=<?php echo $profile['blockedUserId']; ?>" class="card-body">
                <span class="card-header">
                    <img class="card-icon" src="<?php echo $profile['blockedUserPhotoUrl']; ?>"/>
                    <div class="card-content">
                        <span class="card-title"><?php echo $profile['blockedUserFullname']; ?>

                            <?php

                            if ($profile['blockedUserVerify']) {

                                ?>
                                <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                <?php
                            }
                            ?>
                        </span>
                        <span class="card-username">@<?php echo $profile['blockedUserUsername']; ?></span>

                        <?php

                        if ($profile['blockedUserOnline']) {

                            ?>
                            <span class="card-date">Online</span>
                            <?php
                        }
                        ?>

                        <span class="card-action">
                            <span class="card-act negative" onclick="BlackList.remove('<?php echo $profile['id']; ?>', '<?php echo $profile['blockedUserId']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-unblock']; ?></span>
                        </span>

                        <span class="card-counter blue"><?php echo $profile['timeAgo']; ?></span>
                    </div>
                </span>
            </a>
        </li>

        <?php
    }

    static function messageItem($message, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($message['fromUserPhotoUrl']) != 0) {

            $profilePhotoUrl = $message['fromUserPhotoUrl'];
        }

        $time = new language(NULL, $LANG['lang-code']);

        $seen = false;

        if ($message['fromUserId'] == auth::getCurrentUserId() && $message['seenAt'] != 0 ) {

            $seen = true;
        }

        ?>

        <li class="card-item default-item message-item <?php if ($message['fromUserId'] == auth::getCurrentUserId()) echo "message-item-right"; ?>" data-id="<?php echo $message['id']; ?>">
            <div class="card-body">
                <span class="card-header">
                    <a href="/profile.php/?id=<?php echo $message['fromUserId']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/></a>
                    <?php if ($message['fromUserOnline'] && $message['fromUserId'] != auth::getCurrentUserId()) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                    <div class="card-content">

                        <?php

                        if ($message['stickerId'] != 0) {

                            ?>
                                <img class="sticker-img" style="" alt="sticker-img" src="<?php echo $message['stickerImgUrl']; ?>">
                            <?php

                        } else {

                            ?>
                            <span class="card-status-text">

                                    <?php

                                    if (strlen($message['message']) > 0) {

                                        ?>
                                            <span class="card-status-text-message">
                                                <?php echo $message['message']; ?>
                                            </span>
                                        <?php
                                    }

                                    if (strlen($message['imgUrl']) > 0) {

                                        ?>
                                            <img class="post-img" style="" alt="post-img" src="<?php echo $message['imgUrl']; ?>">
                                        <?php
                                    }

                                    ?>

                                    </span>
                            <?php
                        }
                        ?>

                        <span class="card-date">
                            <?php echo $time->timeAgo($message['createAt']); ?>
                            <span class="time green" style="<?php if (!$seen) echo 'display: none'; ?>" data-my-id="<?php echo $LANG['label-seen']; ?>"><?php echo $LANG['label-seen']; ?></span>
                        </span>

                    </div>
                </span>
            </div>
        </li>

        <?php
    }

    static function peopleCardviewItem($profile, $LANG, $counter = false, $counter_text = "", $counter_hint = "", $counter_color = "")
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/profile.php/?id=<?php echo $profile['id']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')" src="<?php echo $profilePhotoUrl; ?>"></span>
                    </div>
                </a>

                <?php

                    if ($counter) {

                        ?>
                            <span class="card-counter <?php echo $counter_color; ?> noselect cardview-item-badge" original-title="<?php echo $counter_hint; ?>"><?php echo $counter_text; ?></span>
                        <?php
                    }
                ?>

                <?php if ($profile['online']) echo "<i class=\"online-label\"></i>"; ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/profile.php/?id=<?php echo $profile['id']; ?>">
                            <?php echo $profile['fullname']; ?>
                        </a>
                        <?php
                            if ($profile['verify']) {

                                ?>
                                <b original-title="<?php echo $LANG['label-account-verified']; ?>" class="verified"></b>
                                <?php
                            }
                        ?>
                    </h4>
                    <?php
                        if (strlen($profile['location']) > 0) {

                            ?>
                                <div class="gray-text"><?php echo $profile['location']; ?></div>
                            <?php
                        }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }
}
