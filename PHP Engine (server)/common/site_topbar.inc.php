<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */


    if (!auth::isSession()) {

        ?>

            <div id="mySidenav" class="sidenav">

                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

                <a href="/signup"><?php echo $LANG['topbar-signup']; ?></a>
                <a href="/login"><?php echo $LANG['topbar-signin']; ?></a>

                <div style="margin-bottom: 60px; padding-bottom: 60px;"></div>

            </div>

            <header class="nav-header">

                <div class="wrap">

                    <div class="l-sidebar">

                      <span class="navigation-toggle-outer">
                        <span class="navigation-toggle">
                          <span class="navigation-toggle-inner"></span>
                        </span>
                      </span>

                        <a class="nav-logo" href="/">
                            <h1><?php echo APP_TITLE; ?></h1>
                        </a>

                        <nav class="main-nav">
                            <ul>
                                <li class=""><a href="/signup"><?php echo $LANG['topbar-signup']; ?></a></li>
                                <li class=""><a href="/login"><?php echo $LANG['topbar-signin']; ?></a></li>
                            </ul>
                        </nav>

                    </div>

                </div>

            </header>

        <?php

    } else {

        ?>

        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="/profile.php/?id=<?php echo auth::getCurrentUserId(); ?>"><?php echo $LANG['topbar-profile']; ?></a>
            <a href="/account/messages"><b><?php echo $LANG['topbar-messages']; ?> <span class="badge" style="display: none" id="messages_counter_cont">(<span id="messages_counter">0</span>)</span></b></a>
            <a href="/account/notifications"><b><?php echo $LANG['topbar-notifications']; ?> <span class="badge" style="display: none" id="notifications_counter_cont">(<span id="notifications_counter">0</span>)</span></b></a>
            <a href="/account/friends"><b><?php echo $LANG['topbar-friends']; ?> <span class="badge" style="display: none" id="friends_counter_cont">(<span id="friends_counter">0</span>)</span></b></a>
            <a href="/account/guests"><b><?php echo $LANG['topbar-guests']; ?> <span class="badge" style="display: none" id="guests_counter_cont">(<span id="guests_counter">0</span>)</span></b></a>
            <a href="/account/likes"><b><?php echo $LANG['topbar-likes']; ?></b></a>
            <a href="/search.php"><b><?php echo $LANG['topbar-search']; ?></b></a>
            <a href="/account/settings"><b><?php echo $LANG['topbar-settings']; ?></b></a>
            <a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-logout']; ?></b></a>

            <div style="margin-bottom: 60px; padding-bottom: 60px;"></div>

        </div>

        <header class="nav-header">

            <div class="wrap">

                <div class="l-sidebar">

                  <span class="navigation-toggle-outer">
                    <span class="navigation-toggle">
                      <span class="navigation-toggle-inner"></span>
                    </span>
                  </span>

                    <a class="nav-logo" href="/">
                        <h1><?php echo APP_TITLE; ?></h1>
                    </a>

                    <?php

                    if (isset($page_id) && $page_id === 'welcome') {

                        ?>

                        <nav class="main-nav">
                            <ul>
                                <li class=""><a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/"><?php echo $LANG['nav-logout']; ?></a></li>
                            </ul>
                        </nav>
                        <?php

                    } else {

                        ?>
                        <nav class="main-nav">
                            <ul>
                                <li class=""><a href="/profile.php/?id=<?php echo auth::getCurrentUserId(); ?>"><?php echo $LANG['topbar-profile']; ?></a></li>
                                <li class=""><a href="/account/messages" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-messages']; ?> <span class="badge" style="display: none" id="messages_counter_cont">(<span id="messages_counter">0</span>)</span></b></a></li>
                                <li class=""><a href="/account/notifications" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-notifications']; ?> <span class="badge" style="display: none" id="notifications_counter_cont">(<span id="notifications_counter">0</span>)</span></b></a></li>
                                <li class=""><a href="/account/friends" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-friends']; ?> <span class="badge" style="display: none" id="friends_counter_cont">(<span id="friends_counter">0</span>)</span></b></a></li>
                                <li class=""><a href="/account/guests" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-guests']; ?> <span class="badge" style="display: none" id="guests_counter_cont">(<span id="guests_counter">0</span>)</span></b></a></li>
                                <li class=""><a href="/account/likes" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-likes']; ?></b></a></li>
                                <li class=""><a href="/search.php" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-search']; ?></b></a></li>
                                <li class=""><a href="/account/settings" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-settings']; ?></b></a></li>
                                <li class=""><a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/" class="waves-effect waves-ripple"><b><?php echo $LANG['topbar-logout']; ?></b></a></li>
                            </ul>
                        </nav>
                        <?php
                    }
                    ?>

                </div>

            </div>

        </header>

        <?php
    }

    if (!isset($_COOKIE['privacy'])) {

        if (isset($page_id) && $page_id != 'main') {

            ?>
            <div class="header-message">
                <div class="wrap">
                    <p class="message"><?php echo $LANG['label-cookie-message']; ?> <a
                            href="/terms"><?php echo $LANG['page-terms']; ?></a></p>
                </div>

                <button class="close-message-button close-privacy-message">×</button>
            </div>
            <?php
        }
    }
?>