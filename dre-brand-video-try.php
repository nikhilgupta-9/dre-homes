<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=35 and is_active=1');
include('common/header.php');

// Fetch active brand video content dynamically from DB
$brand_video_content = select_row('tbl_brand_video_content', 'is_active=1');

// Function to extract YouTube video ID from URL
function getYoutubeVideoID($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? '';
}

$youtube_url = $brand_video_content['brand_video_youtube'] ?? '';
$youtube_id = getYoutubeVideoID($youtube_url);
$youtube_img = $brand_video_content['brand_video_youtube_img'] ?? '';  // Thumbnail filename or fallback

// Fallback image if none available
if (empty($youtube_img)) {
    $youtube_img = 'default-video-thumbnail.jpg'; // make sure this default image exists in your folder
}

// Construct embed URL for iframe
$youtube_embed_url = $youtube_id ? "https://www.youtube.com/embed/$youtube_id?autoplay=1" : '';

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
                                <h1><?= htmlspecialchars($brand_video_content['brand_video_heading']) ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                       <div class="bg" style="background-image: url('<?= BASE_URL_IMG ?>brand-video/<?= htmlspecialchars($brand_video_content['brand_video_cont_img']) ?>');"></div>

                    </div>

                </div>
            </div>
        </div>
        <!--section-end-->

        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Dre Brand Video</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->

            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <div class="scroll-content-wrap">
                        <div class="post-container">
                            <div class="boxed-content">
                                <div class="boxed-content-item">
                                    <div class="text-block-container">
                                        <div class="row guide-head">
                                            <div class="col-12 mt-5">

                                                <div class="y-video-thumb">

                                                    <div class="ratio ratio-16x9 mb-1 d-flex justify-content-center align-items-center position-relative">
                                                        <!-- Thumbnail -->
                                                        <img src="<?= BASE_URL_IMG ?>brand-video/<?= htmlspecialchars($youtube_img) ?>"
                                                             alt="Video Thumbnail"
                                                             class="object-fit-cover position-absolute top-0 start-0 w-100 h-100"
                                                             style="z-index: 1; object-fit: cover;">

                                                        <!-- Video iframe container -->
                                                        <div class="video-container position-absolute top-0 start-0 w-100 h-100" style="display: none; z-index: 4;">
                                                            <iframe src="<?= htmlspecialchars($youtube_embed_url) ?>" width="100%" height="100%" frameborder="0"
                                                                allowfullscreen
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
                                                            </iframe>
                                                        </div>

                                                        <!-- Play button -->
                                                        <div class="banner-widget_content position-absolute" style="z-index: 3;">
                                                            <div class="video-box-btn">
                                                                <?php if ($youtube_embed_url): ?>
                                                                    <button class="play-button"
                                                                        onclick="playVideoSection(this)">
                                                                        <i class="fas fa-play"></i>
                                                                    </button>
                                                                <?php else: ?>
                                                                    <p>Video unavailable.</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-12 mt-5">
                                                <?= $brand_video_content['brand_video_content'] ?? '<p>No content available.</p>' ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="limit-box"></div>
                </div>
                <!--boxed-container end-->
            </div>
            <!--main-content end-->

            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)">
                </div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)">
                </div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <!--content end-->

    <?php include 'common/footer.php'; ?>

<script>
function playVideoSection(button) {
    const container = button.closest('.ratio');
    const iframeContainer = container.querySelector('.video-container');
    const iframe = iframeContainer.querySelector('iframe');
    // Show iframe container and hide thumbnail & play button
    iframeContainer.style.display = 'block';
    button.style.display = 'none';
    container.querySelector('img').style.display = 'none';

    // Set iframe src to autoplay (already set in src)
    iframe.focus();
}
</script>
