<?php
include('db/config.php');

if (isset($_GET['id']) && $_GET['id'] == 'podcast') {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = intval($_GET['limit'] ?? 4);
    $start = ($page - 1) * $limit;
    $podcastData = [];

    // Query run karein
    $query2 = mysqli_query($conn, "SELECT * 
    FROM tbl_podcast
    WHERE is_active = 1
    ORDER BY 
        CASE WHEN is_main = 1 THEN 0 ELSE 1 END,
        order_no ASC
    LIMIT 1, 999;");

    if ($query2) {
        while ($row = mysqli_fetch_assoc($query2)) {
            $original_video = $row['video_file'];
            $podcastData[] = [
                'podcast_id' => $row['podcast_id'],
                'title' => $row['podcast_title'],
                'description' => $row['podcast_descp'],
                'thumbnail' => !empty($row['podcast_thumb'])
                    ? BASE_URL_IMG . "podcast/thumb/" . $row['podcast_thumb']
                    : "",
                'video_url' => !empty($original_video)
                    ? BASE_URL_IMG . "podcast/video/" . $original_video
                    : "",
                'youtube_url' => $row['youtube_link'] ?? "",
                'media_type' => !empty($original_video) ? "video" : "youtube",
                'order_no' => $row['order_no'],
                'published_at' => $row['tstp'],
            ];
        }
    }

    // JSON Response return karein
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    echo json_encode($podcastData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

include('common/header.php');

?>

<!--wrapper-->
<div class="wrapper">
    <!--content-->
    <div class="content">
        <!--section-->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1>Podcast</h1>
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg"
                            style="background-image: url('https://t3.ftcdn.net/jpg/05/10/75/30/360_F_510753092_f4AOmCJAczuGgRLCmHxmowga2tC9VYQP.jpg');">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--section-end-->

        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Podcast</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->

            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">

                    <section class="dre-podcast-section">
                        <div class="container">

                            <!-- HERO SECTION -->
                            <div class="dre-podcast-hero">

                                <div class="dre-podcast-content">
                                    <span class="dre-podcast-label">DRE PODCAST</span>

                                    <?php
                                    $hero_podcast = select_row('tbl_podcast', 'is_active = 1 AND is_main = 1 LIMIT 1');
                                    $youtubeUrl = $hero_podcast[0]['youtube_link'] ?? '';
                                    $videoId = '';

                                    if (!empty($youtubeUrl)) {
                                        parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $params);
                                        $videoId = $params['v'] ?? '';
                                    }
                                    ?>
                                    <h2 class="dre-podcast-heading">
                                        Conversations shaping Dubai's real estate future.
                                    </h2>

                                    <p class="dre-podcast-description">
                                        <?php
                                        if ($hero_podcast[0]['podcast_descp']) {
                                            echo $hero_podcast[0]['podcast_descp'];
                                        } else {
                                            echo "The DRE Podcast is where real conversations happen.
                                        We bring together visionaries and experts to talk
                                        about the trends, ideas and opportunities shaping
                                        Dubai's real estate future.";
                                        }
                                        ?>

                                    </p>
                                    <a href="https://www.youtube.com/@DrehomesRE" class="dre-podcast-btn">
                                        <i class="fab fa-youtube"></i>
                                        Subscribe on YouTube
                                    </a>
                                </div>

                                <div class="dre-podcast-featured-video">
                                    <div class=" is-loading" id="videoContainer">
                                        <!-- <div class="dre-skeleton-img"></div> -->
                                        <div class="dre-podcast-video-card real-content">
                                            <img src="<?= !empty($hero_podcast['podcast_thumb'])
                                                ? BASE_URL_IMG . 'podcast/thumb/' . htmlspecialchars($hero_podcast['podcast_thumb'])
                                                : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGa70BgePn1Rsf41oiG6ac0_TAzpKXj4d9qg&s'; ?>"
                                                alt="Podcast Episode">

                                            <div class="dre-podcast-overlay"></div>

                                            <div class="dre-podcast-play-btn" onclick="playVideo()">
                                                <i class="fa fa-play"></i>
                                            </div>

                                            <div class="dre-podcast-video-info">
                                                <span>LATEST EPISODE</span>
                                                <h3><?= $hero_podcast['podcast_title'] ?></h3>

                                                <div class="dre-podcast-meta">
                                                    <span>EP #<?=$hero_podcast['podcast_id']?></span>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- EPISODES -->

                            <div class="dre-podcast-episodes">
                                <h4 class="dre-podcast-episodes-title">EPISODES</h4>

                                <div class="dre-podcast-grid">
                                    <!-- Cards yahan aayenge -->
                                </div>

                                <div id="load-more-sentinel" style="height: 20px; text-align: center; margin: 20px 0;">
                                    <span class="spinner" style="display: none;">Loading more episodes...</span>
                                </div>
                            </div>

                        </div>
                    </section>
                    <div class="limit-box"></div>
                </div>
                <!--boxed-container end-->
            </div>
            <!--main-content end-->

            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)"></div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <!--content end-->


    <script>
        function getEmbedUrl(url) {
            const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
            if (shortMatch) {
                return `https://www.youtube.com/embed/${shortMatch[1]}?autoplay=1`;
            }

            const watchMatch = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
            if (watchMatch) {
                return `https://www.youtube.com/embed/${watchMatch[1]}?autoplay=1`;
            }

            return url;
        }

        function playVideo() {
            const youtubeUrl = "<?= addslashes($hero_podcast['youtube_link'] ?? '') ?>";
            const embedUrl = getEmbedUrl(youtubeUrl);

            document.getElementById('videoContainer').innerHTML = `
        <iframe
            width="100%"
            height="400"
            src="${embedUrl}"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    `;
        }
        if (window.location.pathname.includes('.php')) {
        window.location.href = base_url + '404';
    }
    </script>
    <?php include 'common/footer.php'; ?>