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

    $report = new report($dbo);

    if (isset($_GET['act'])) {

        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $token = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        if (admin::getAccessToken() === $token && !APP_DEMO) {

            switch ($act) {

                case "clear" : {

                    $report->removeAllPhotoReports();

                    header("Location: /admin/photo_reports.php");
                    break;
                }

                default: {

                    header("Location: /admin/photo_reports.php");
                    exit;
                }
            }
        }

        header("Location: /admin/photo_reports.php");
        exit;
    }

    $page_id = "photo_reports";

    $css_files = array("mytheme.css");
    $page_title = "Profile Reports | Admin Panel";

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
                            <li class="breadcrumb-item active">Photo Reports</li>
                        </ol>
                    </div>
                </div>

                <?php

                    include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_banner.inc.php");
                ?>

                <?php

                    $reports = new report($dbo);

                    $result = $reports->getPhotoReports();

                    $inbox_loaded = count($result['reports']);

                    if ($inbox_loaded != 0) {

                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <a href="/admin/photo_reports.php/?act=clear&access_token=<?php echo admin::getAccessToken(); ?>" style="float: right">
                                            <button type="button" class="btn waves-effect waves-light btn-info">Delete all reports</button>
                                        </a>

                                        <div class="d-flex no-block">
                                            <h4 class="card-title">Profile Reports (Latest reports)</h4>
                                        </div>

                                        <div class="table-responsive m-t-20">

                                            <table class="table stylish-table">

                                                <thead>
                                                <tr>
                                                    <th colspan="2">Report From User</th>
                                                    <th colspan="2">Photo Author</th>
                                                    <th>Photo</th>
                                                    <th>Comment</th>
                                                    <th>Reason</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                    <?php

                                                        foreach ($result['reports'] as $key => $value) {

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

                    <script type="text/javascript">

            window.Photo || (window.Photo = {});

            Photo.remove = function (offset, fromUserId, accessToken) {

                $.ajax({
                    type: 'GET',
                    url: '/admin/photo_remove.php/?id=' + offset + '&fromUserId=' + fromUserId + '&access_token=' + accessToken,
                    data: 'itemId=' + offset + '&fromUserId=' + fromUserId + "&access_token=" + accessToken,
                    timeout: 30000,
                    success: function(response) {

                        $('tr[data-id=' + offset + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>

<?php

    function draw($item)
    {
        ?>

            <tr data-id="<?php echo $item['abuseToPhotoId']; ?>">
                <td style="width:50px;">

                    <?php

                        if ($item['abuseFromUserId'] != 0 && strlen($item['abuseFromUserPhotoUrl']) != 0) {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(<?php echo $item['abuseFromUserPhotoUrl']; ?>)"></span>
                            <?php

                        } else {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(/img/profile_default_photo.png)"></span>
                            <?php
                        }
                    ?>
                </td>
                <td>

                    <?php

                        if ($item['abuseFromUserId'] != 0) {

                            ?>
                                <h6><a href="/admin/profile.php?id=<?php echo $item['abuseFromUserId']; ?>"><?php echo $item['abuseFromUserFullname']; ?></a></h6>
                                <small class="text-muted">@<?php echo $item['abuseFromUserUsername']; ?></small>
                            <?php

                        } else {

                            ?>
                                <h6>Unknown user</h6>
                            <?php
                        }
                    ?>
                </td>

                <td style="width:50px;">

                    <?php

                        if ($item['abuseToPhotoFromUserId'] != 0 && strlen($item['abuseToPhotoFromUserPhotoUrl']) != 0) {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(<?php echo $item['abuseToPhotoFromUserPhotoUrl']; ?>)"></span>
                            <?php

                        } else {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(/img/profile_default_photo.png)"></span>
                            <?php
                        }
                    ?>
                </td>
                <td>

                    <?php

                        if ($item['abuseToPhotoFromUserId'] != 0) {

                            ?>
                                <h6><a href="/admin/profile.php?id=<?php echo $item['abuseToPhotoFromUserId']; ?>"><?php echo $item['abuseToPhotoFromUserFullname']; ?></a></h6>
                                <small class="text-muted">@<?php echo $item['abuseToPhotoFromUserUsername']; ?></small>
                            <?php

                        } else {

                            ?>
                                <h6>Unknown user</h6>
                            <?php
                        }
                    ?>
                </td>

                <td>

                    <?php

                        if (strlen($item['abuseToPhotoUrl']) != 0) {

                            ?>
                                <img src="<?php echo $item['abuseToPhotoUrl']; ?>" alt="iMac" width="80">
                            <?php

                        } else {

                            ?>
                            <h6>-</h6>
                            <?php
                        }
                    ?>
                </td>
                <td>
                    <?php

                        if (strlen($item['abuseToPhotoComment']) != 0) {

                            ?>
                                <h6><?php echo $item['abuseToPhotoComment']; ?></h6>
                            <?php

                        } else {

                            ?>
                                <h6>-</h6>
                            <?php
                        }
                    ?>
                </td>

                <td>
                    <?php

                        switch ($item['abuseId']) {

                            case 0: {

                                echo "<span class=\"label label-success\">This is spam.</span>";

                                break;
                            }

                            case 1: {

                                echo "<span class=\"label label-info\">Hate Speech or violence.</span>";

                                break;
                            }

                            case 2: {

                                echo "<span class=\"label label-danger\">Nudity or Pornography.</span>";

                                break;
                            }

                            default: {

                                echo "<span class=\"label label-warning\">Fake profile.</span>";

                                break;
                            }
                        }
                    ?>
                </td>
                <td><?php echo $item['date']; ?></td>
                <td>
                    <a href="javascript:void(0)" onclick="Photo.remove('<?php echo $item['abuseToPhotoId']; ?>', '<?php echo $item['abuseToPhotoFromUserId']; ?>', '<?php echo admin::getAccessToken(); ?>'); return false;" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Delete Photo and all reports to photo"><i class="ti-trash"></i></a>
                </td>
            </tr>

        <?php
    }