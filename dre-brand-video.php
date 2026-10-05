<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=35 and is_active=1');
include('common/header.php');

// Fetch active brand video content dynamically from DB
$brand_video_content = select_row('tbl_brand_video_content', 'is_active=1');

// Function to extract YouTube video ID from URL
function getYoutubeVideoID($url)
{
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? '';
}

$youtube_url = $brand_video_content['brand_video_youtube'] ?? '';
$youtube_id = getYoutubeVideoID($youtube_url);
$youtube_img = $brand_video_content['brand_video_youtube_img'] ?? 'default-video-thumbnail.jpg'; // fallback

// Construct embed URL base (without autoplay param, will be added in JS)
$youtube_embed_base_url = $youtube_id ? "https://www.youtube.com/embed/$youtube_id" : '';

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
                        <div class="bg"
                            style="background-image: url('<?= BASE_URL_IMG ?>brand-video/<?= htmlspecialchars($brand_video_content['brand_video_cont_img']) ?>');">
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
                <a href="<?= BASE_URL ?>">Home</a><span>DRE Brand Video</span>
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

                                                    <div
                                                        class="ratio ratio-16x9 mb-1 d-flex justify-content-center align-items-center position-relative">

                                                        <?php
                                                        $isYoutube = ($brand_video_content['video_source'] == "youtube");
                                                        $videoSrc = $isYoutube
                                                            ? $youtube_embed_base_url
                                                            : BASE_URL_IMG . "brand-video/videos/" . htmlspecialchars($brand_video_content['brand_video_2']);
                                                        ?>

                                                        <!-- Thumbnail (COMMON for both) -->
                                                        <img src="<?= BASE_URL_IMG ?>brand-video/<?= htmlspecialchars($youtube_img) ?>"
                                                            class="object-fit-cover position-absolute top-0 start-0 w-100 h-100"
                                                            style="z-index: 1; object-fit: cover;">

                                                        <!-- Video container (COMMON) -->
                                                        <div class="video-container position-absolute top-0 start-0 w-100 h-100"
                                                            style="display: none; z-index: 4;">

                                                            <?php if ($isYoutube): ?>
                                                                <iframe width="100%" height="100%" frameborder="0"
                                                                    allowfullscreen allow="autoplay; encrypted-media">
                                                                </iframe>
                                                            <?php else: ?>
                                                                <video class="w-100 h-100" controls
                                                                    style="object-fit: cover;">
                                                                    <source src="<?= $videoSrc ?>" type="video/mp4">
                                                                </video>
                                                            <?php endif; ?>

                                                        </div>

                                                        <!-- Play Button (COMMON) -->
                                                        <div class="banner-widget_content position-absolute"
                                                            style="z-index: 3;">
                                                            <div class="video-box-btn">
                                                                <?php if (!empty($videoSrc)): ?>
                                                                    <button class="play-button"
                                                                        onclick="playVideoSection('<?= $videoSrc ?>', this, <?= $isYoutube ? 'true' : 'false' ?>)">
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
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)"></div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <!--content end-->

    <script>
function playVideoSection(src, btn, isYoutube) {
    const ratio = btn.closest('.ratio');
    const container = ratio.querySelector('.video-container');
    const img = ratio.querySelector('img');
    
    if (isYoutube) {
        // Handle YouTube
        container.style.display = 'block';
        const iframe = container.querySelector('iframe');
        iframe.src = src + "?autoplay=1&mute=1";
        
        // Hide thumbnail and button
        if (img) img.style.display = 'none';
        btn.style.display = 'none';
    } else {
        // Handle Local Video
        const video = container.querySelector('video');
        
        // CRITICAL: Make sure video element is visible and has source
        console.log("Video source:", src);
        console.log("Video element:", video);
        console.log("Source element:", video.querySelector('source'));
        
        // Check if source exists and has src
        const source = video.querySelector('source');
        if (source && source.src) {
            console.log("Source URL:", source.src);
        } else {
            console.error("No source found for video");
            return;
        }
        
        // Show container first
        container.style.display = 'block';
        
        // Force video to load and play
        video.load();
        
        // Small delay to ensure video is ready
        setTimeout(() => {
            video.play().then(() => {
                console.log("Video playing successfully");
                // Hide thumbnail and button only after play starts
                if (img) img.style.display = 'none';
                btn.style.display = 'none';
            }).catch(error => {
                console.error("Play failed:", error);
                // Show error message or keep controls visible
                btn.innerHTML = '<i class="fas fa-play"></i> Click to play';
                btn.style.display = 'flex';
                
                // Some browsers need user interaction - add click handler to video
                video.addEventListener('click', function playOnClick() {
                    video.play();
                    video.removeEventListener('click', playOnClick);
                });
            });
        }, 100);
    }
}
</script>
    <?php include 'common/footer.php'; ?>