<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

?>

    <div id="main-footer">
        <div class="wrap">

            <ul id="footer-nav">
                <li><a href="/about"><?php echo $LANG['footer-about']; ?></a></li>
                <li><a href="/terms"><?php echo $LANG['footer-terms']; ?></a></li>
                <li><a href="/gdpr"><?php echo $LANG['footer-gdpr']; ?></a></li>
                <li><a href="/support"><?php echo $LANG['footer-support']; ?></a></li>
                <li><a class="lang_link" href="javascript:void(0)" onclick="App.getLanguageBox('<?php echo $LANG['page-language']; ?>'); return false;"><?php echo $LANG['lang-name']; ?></a></li>

                <li id="footer-copyright">
                    © <?php echo APP_YEAR; ?> <?php echo APP_TITLE; ?>
                </li>
            </ul>

        </div>
    </div>


    <script type="text/javascript" src="/js/jquery-2.1.1.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.js"></script>
    <script src="/js/jquery.colorbox.js"></script>
    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>
    <script src="/js/drawer.js"></script>
    <script type="text/javascript" src="/js/my.js?x=2"></script>

    <script src="/js/common.js?x=2"></script>
    <script type="text/javascript" src="/js/chat.js?x=101"></script>

    <script type="text/javascript">

        <?php

            if (auth::isSession()) {

                ?>
                    App.init();
                <?php
            }

        ?>

        window.App || ( window.App = {} );

    </script>

    <script type="text/javascript">

        var options = {

            pageId: "<?php echo $page_id; ?>"
        }

        var account = {

            id: "<?php echo auth::getCurrentUserId(); ?>",
            username: "<?php echo auth::getCurrentUserLogin(); ?>",
            accessToken: "<?php echo auth::getAccessToken(); ?>"
        }

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
            $(".cardview-item-badge").tipsy({gravity: 'w'});
        });

    </script>