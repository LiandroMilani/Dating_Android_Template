<?php

    /*!
     * ifsoft.co.uk admin engine v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: /admin/login.php");
        exit;
    }

    $error = false;
    $error_message = '';
    $query = '';
    $result = array();
    $result['id'] = 0;
    $result['tickets'] = array();

    $support = new support($dbo);

    if (isset($_GET['act'])) {

        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $ticketId = isset($_GET['ticketId']) ? $_GET['ticketId'] : 0;
        $token = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        $ticketId = helper::clearText($ticketId);

        if (admin::getAccessToken() === $token && !APP_DEMO) {

            switch ($act) {

                case "delete" : {

                    $support->removeTicket($ticketId);

                    header("Location: /admin/support.php");
                    break;
                }

                default: {

                    header("Location: /admin/support.php");
                }
            }
        }

        header("Location: /admin/support.php");
    }

    $result = $support->getTickets();

    $page_id = "support";

    $css_files = array("mytheme.css");
    $page_title = "Support | Admin Panel";

    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">

    <div id="main-wrapper">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper">

            <div class="container-fluid">

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/main.php">Home</a></li>
                            <li class="breadcrumb-item active">Support</li>
                        </ol>
                    </div>
                </div>

                <?php

                    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_banner.inc.php");
                ?>

                <?php

                    if (count($result['tickets']) > 0) {

                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="d-flex no-block">
                                            <h4 class="card-title">Tickets</h4>
                                        </div>

                                        <div class="table-responsive m-t-20">

                                            <table class="table stylish-table">

                                                <thead>
                                                <tr>
                                                    <th class="text-left">Id</th>
                                                    <th class="text-left"From account</th>
                                                    <th class="text-left">Email</th>
                                                    <th class="text-left">Subject</th>
                                                    <th class="text-left">Text</th>
                                                    <th class="text-left">Date</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                    <?php

                                                        foreach ($result['tickets'] as $key => $value) {

                                                            draw($dbo, $value);
                                                        }

                                                    ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php

                    } else {

                        ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="card-title">List is empty.</h4>
                                            <p class="card-text">This means that there is no data to display :)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                ?>

            </div> <!-- End Container fluid  -->

            <?php

                include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_footer.inc.php");
            ?>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>

<?php

    function draw($dbo, $value)
    {

        $profile = new profile($dbo, $value['accountId']);
        $profileInfo = $profile->getVeryShort();

        ?>

        <tr>
            <td class="text-left"><?php echo $value['id']; ?></td>
            <td class="text-left"><?php if ($value['accountId'] != 0 ) echo "<a href=\"/admin/profile.php/?id={$value['accountId']}\">{$profileInfo['fullname']}</a>"; else echo "-"; ?></td>
            <td class="text-left"><?php echo $value['email']; ?></a></td>
            <td class="text-left" style="word-break: break-all;"><?php echo $value['subject']; ?></td>
            <td class="text-left" style="word-break: break-all;"><?php echo $value['text']; ?></td>
            <td class="text-left" style="white-space: nowrap;"><?php echo date("Y-m-d H:i:s", $value['createAt']); ?></td>
            <td><a href="/admin/support.php/?ticketId=<?php echo $value['id']; ?>&act=delete&access_token=<?php echo admin::getAccessToken(); ?>">Delete</a></td>
        </tr>

        <?php
    }