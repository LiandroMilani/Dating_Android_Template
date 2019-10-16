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

    $stats = new stats($dbo);
    $admin = new admin($dbo);

    $page_id = "purchases";

    $css_files = array("mytheme.css");
    $page_title = "Last Purchases";

    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper"> <!-- Page wrapper  -->

            <div class="container-fluid"> <!-- Container fluid  -->

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Last Purchases</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/main.php">Home</a></li>
                            <li class="breadcrumb-item active">Last Purchases</li>
                        </ol>
                    </div>
                </div>

                <?php

                    $reports = new report($dbo);

                    $result = $stats->getPurchases(0);

                    $inbox_loaded = count($result['items']);

                    if ($inbox_loaded != 0) {

                        ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title m-b-0">Full Statistics</h4>
                                        </div>
                                        <div class="card-body collapse show">
                                            <div class="table-responsive">
                                                <table class="table product-overview">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">Id</th>
                                                            <th>Account</th>
                                                            <th>Amount (Credits)</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php

                                                            foreach ($result['items'] as $key => $value) {

                                                                draw($value);
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

    </div> <!-- End Main Wrapper -->

</body>

</html>

<?php

    function draw($item)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $item['id']; ?></td>
            <td><?php echo "<a href=\"/admin/profile.php?id={$item['toUserId']}\">Account Id ({$item['toUserId']})</a>"; ?></td>
            <td><?php echo $item['amount']; ?></td>
            <td><?php echo date("Y-m-d H:i:s", $item['createAt']); ?></td>
        </tr>

        <?php
    }