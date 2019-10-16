<?php

    /*!
     * ifsoft.co.uk admin engine v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

?>

<aside class="left-sidebar">

    <div class="scroll-sidebar"> <!-- Sidebar scroll-->

        <nav class="sidebar-nav"> <!-- Sidebar navigation-->

            <ul id="sidebarnav">

                <li class="nav-small-cap">General</li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "main") { echo "active";} ?>" href="/admin/main.php" aria-expanded="false">
                            <i class="ti-dashboard"></i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "gifts") { echo "active";} ?>" href="/admin/gifts.php" aria-expanded="false">
                            <i class="ti-gift"></i>
                            <span class="hide-menu">Gifts</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "admob") { echo "active";} ?>" href="/admin/admob.php" aria-expanded="false">
                            <i class="ti-layout-list-post"></i>
                            <span class="hide-menu">Admob</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "gcm") { echo "active";} ?>" href="/admin/gcm.php" aria-expanded="false">
                            <i class="ti-bell"></i>
                            <span class="hide-menu">Push Notifications</span>
                        </a>
                    </li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "users") { echo "active";} ?>" href="/admin/users.php" aria-expanded="false">
                            <i class=" ti-search"></i>
                            <span class="hide-menu">Search Users</span>
                        </a>
                    </li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "support") { echo "active";} ?>" href="/admin/support.php" aria-expanded="false">
                            <i class="ti-help-alt"></i>
                            <span class="hide-menu">Support</span>
                        </a>
                    </li>

                <li class="nav-devider"></li>

                <li class="nav-small-cap">Moderation</li>

                <li>
                    <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "accounts_moderation") { echo "active";} ?>" href="/admin/moderation_accounts.php" aria-expanded="false">
                        <i class="ti-user"></i>
                        <span class="hide-menu">Accounts Moderation</span>
                    </a>
                </li>

                <li>
                    <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "photos_moderation") { echo "active";} ?>" href="/admin/moderation_photos.php" aria-expanded="false">
                        <i class="ti-image"></i>
                        <span class="hide-menu">Photos Moderation</span>
                    </a>
                </li>

                <li class="nav-devider"></li>

                <li class="nav-small-cap">Stream</li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "stream_photos") { echo "active";} ?>" href="/admin/stream_photos.php" aria-expanded="false">
                            <i class="ti-image"></i>
                            <span class="hide-menu">Photos Stream</span>
                        </a>
                    </li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "stream_messages") { echo "active";} ?>" href="/admin/stream_messages.php" aria-expanded="false">
                            <i class="ti-comment-alt"></i>
                            <span class="hide-menu">Messages Stream</span>
                        </a>
                    </li>

                <li class="nav-devider"></li>

                <li class="nav-small-cap">Reports</li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "reports") { echo "active";} ?>" href="/admin/profile_reports.php" aria-expanded="false">
                            <i class="ti-face-sad"></i>
                            <span class="hide-menu">Profile Reports</span>
                        </a>
                    </li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "photo_reports") { echo "active";} ?>" href="/admin/photo_reports.php" aria-expanded="false">
                            <i class="ti-gallery"></i>
                            <span class="hide-menu">Photo Reports</span>
                        </a>
                    </li>

                <li class="nav-devider"></li>

                <li class="nav-small-cap">Mobile</li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "purchases") { echo "active";} ?>" href="/admin/purchases.php" aria-expanded="false">
                            <i class="ti-money"></i>
                            <span class="hide-menu">In-app Purchases</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "app") { echo "active";} ?>" href="/admin/app.php" aria-expanded="false">
                            <i class="ti-mobile"></i>
                            <span class="hide-menu">App Settings</span>
                        </a>
                    </li>

                <li class="nav-devider"></li>

                <li class="nav-small-cap">Settings</li>

                    <li>
                        <a class="waves-effect waves-dark <?php if (isset($page_id) && $page_id === "settings") { echo "active";} ?>" href="/admin/settings.php" aria-expanded="false">
                            <i class="ti-settings"></i>
                            <span class="hide-menu">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-dark" href="/admin/logout.php/?access_token=<?php echo admin::getAccessToken(); ?>&continue=/" aria-expanded="false">
                            <i class="ti-power-off"></i>
                            <span class="hide-menu">Logout</span>
                        </a>
                    </li>

            </ul>
        </nav> <!-- End Sidebar navigation -->
    </div> <!-- End Sidebar scroll-->
</aside>