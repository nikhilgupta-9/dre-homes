<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=15 and is_active=1');
include('common/header.php');
$team_details = select_row('tbl_team_det', 'is_active=1');
?>

<div class="wrapper">
    <div class="content">

        <!-- HERO SECTION -->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1><?= $team_details['team_heading'] ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg"
                            data-bg="<?= BASE_URL_IMG ?>team/<?= $team_details['team_img'] ?>"
                            data-scrollax="properties: { translateY: '30%' }">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BREADCRUMB -->
        <div class="container">
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Our Team Town Square</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="container"><div class="main-content ms_vir_height">
            <div class="boxed-container">
                <div class="boxed-content">

                    <div class="boxed-content-title">
                        <?= $team_details['team_descp'] ?>
                    </div>

                    <!-- FILTER BUTTONS - Only show designations that have Town Square team members -->
                    <div class="ag-filter-tags">
                        <button class="filter-btn active" data-id="all">All</button>

                        <?php
                        // Fetch only those designations that have at least one team member with is_time_square_team = 1
                        $desg_query = mysqli_query($conn, "
                            SELECT DISTINCT d.* 
                            FROM tbl_team_desg d
                            INNER JOIN tbl_team t ON d.team_desg_id = t.team_desg_id
                            WHERE d.is_active = '1' 
                            AND t.is_active = 1 
                            AND t.is_time_square_team = 1
                            ORDER BY d.order_no ASC
                        ");
                        
                        if(mysqli_num_rows($desg_query) > 0) {
                            while ($desg = mysqli_fetch_array($desg_query)) {
                        ?>
                                <button class="filter-btn" data-id="<?= $desg['team_desg_id'] ?>">
                                    <?= $desg['team_desg_name'] ?>
                                </button>
                        <?php 
                            }
                        } else {
                            // Optional: Show a message if no designations found
                            echo '<div style="display:none;"></div>';
                        }
                        ?>
                    </div>

                    <div id="noResultsMessage" style="display:none; text-align:center; padding:30px; color:#ab9889;">
                        No team members found.
                    </div>

                    <!-- TEAM LIST -->
                    <div class="boxed-content-item ot-pg">
                        <div class="agent-page-container">

                            <?php
                            $team_query = mysqli_query($conn, "
                                SELECT t.*, d.team_desg_name 
                                FROM tbl_team t
                                LEFT JOIN tbl_team_desg d 
                                    ON t.team_desg_id = d.team_desg_id
                                WHERE t.is_active = 1 AND t.is_time_square_team = 1
                                ORDER BY 
                                    d.order_no IS NULL,
                                    d.order_no ASC,
                                    t.order_no ASC
                            ");

                            if(mysqli_num_rows($team_query) > 0) {
                                while ($team_row = mysqli_fetch_array($team_query)) {
                                    if($team_row['team_desg_id'] == 10){
                                        $redirect_url = BASE_URL ."management/". $team_row['team_slug'];
                                    }else{
                                        $redirect_url = BASE_URL ."our-team/". $team_row['team_slug'];
                                    }
                            ?>

                                <!-- TEAM CARD -->
                                <div class="agent-detail-box"
                                    data-desg="<?= $team_row['team_desg_id'] ?>"
                                    data-name="<?= strtolower($team_row['team_name']) ?>"
                                    data-role="<?= strtolower($team_row['team_desg_name']) ?>">

                                    <div class="listing-item agent-equal-box">
                                        <div class="geodir-category-listing">

                                            <div class="geodir-category-img">
                                                <a href="<?=$redirect_url ?>"
                                                    class="geodir-category-img_item">
                                                    <div>
                                                                    <img
                                                                        src="<?= BASE_URL_IMG ?>team/<?= $team_row['team_img'] ?>">
                                                                </div>
                                                    <div class="overlay"></div>
                                                </a>
                                            </div>

                                            <div class="geodir-category-content">
                                                <h3>
                                                    <a href="<?= $redirect_url ?>">
                                                        <?= $team_row['team_name'] ?>
                                                    </a>
                                                </h3>

                                                <div class="geodir-category-content_price">
                                                    <?= $team_row['team_desg'] ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            <?php 
                                }
                            } else {
                                echo '<div style="text-align:center; padding:50px;">No team members found for Town Square.</div>';
                            }
                            ?>

                        </div>
                    </div>

                </div>
            </div>
        </div></div>

    </div>

    <?php include 'common/footer.php'; ?>
</div>