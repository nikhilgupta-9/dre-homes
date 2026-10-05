<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===============================
   DATABASE TABLE STRUCTURE
   Run this SQL to create the table:
=================================
CREATE TABLE `tbl_instagram_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` enum('image','video') NOT NULL DEFAULT 'image',
  `media_url` varchar(500) NOT NULL,
  `thumbnail_url` varchar(500) DEFAULT NULL,
  `instagram_url` varchar(500) DEFAULT NULL,
  `caption` text,
  `likes_count` int(11) DEFAULT 0,
  `comments_count` int(11) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

/* ===============================
   SAVE / UPDATE CONTENT
================================= */
if (isset($_POST['save_content'])) {
    $content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
    $content_type = mysqli_real_escape_string($conn, $_POST['content_type']);
    $instagram_url = mysqli_real_escape_string($conn, $_POST['instagram_url']);
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $likes_count = intval($_POST['likes_count']);
    $comments_count = intval($_POST['comments_count']);
    $display_order = intval($_POST['display_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $media_url = '';
    $thumbnail_url = '';
    
    // Handle file upload
    if (!empty($_FILES['media_file']['name'])) {
        $upload_dir = __DIR__ . '/assets/media/instagram/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['media_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (move_uploaded_file($_FILES['media_file']['tmp_name'], $upload_dir . $file_name)) {
            $media_url = 'instagram/' . $file_name;
            
            // Create thumbnail for videos
            if ($content_type == 'video' && in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
                // You can add video thumbnail generation here if needed
                $thumbnail_url = 'instagram/' . $file_name; // Use same file for now
            }
        }
    }
    
    if ($content_id > 0) {
        // Update existing
        if (!empty($media_url)) {
            // Delete old file
            $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT media_url FROM tbl_instagram_content WHERE id='$content_id'"));
            if (!empty($old['media_url']) && file_exists(__DIR__ . '/../' . $old['media_url'])) {
                unlink(__DIR__ . '/../' . $old['media_url']);
            }
            $query = "UPDATE tbl_instagram_content SET 
                        content_type='$content_type', media_url='$media_url', thumbnail_url='$thumbnail_url',
                        instagram_url='$instagram_url', caption='$caption', likes_count='$likes_count',
                        comments_count='$comments_count', display_order='$display_order', is_active='$is_active'
                      WHERE id='$content_id'";
        } else {
            $query = "UPDATE tbl_instagram_content SET 
                        content_type='$content_type', instagram_url='$instagram_url', caption='$caption',
                        likes_count='$likes_count', comments_count='$comments_count', 
                        display_order='$display_order', is_active='$is_active'
                      WHERE id='$content_id'";
        }
        $msg = "Content updated successfully!";
    } else {
        // Insert new
        if (empty($media_url)) {
            $_SESSION['error_msg'] = "Please upload a file!";
            header("Location: manage-instagram-content.php");
            exit();
        }
        $query = "INSERT INTO tbl_instagram_content (content_type, media_url, thumbnail_url, instagram_url, caption, likes_count, comments_count, display_order, is_active) 
                  VALUES ('$content_type', '$media_url', '$thumbnail_url', '$instagram_url', '$caption', '$likes_count', '$comments_count', '$display_order', '$is_active')";
        $msg = "Content added successfully!";
    }
    
    mysqli_query($conn, $query) ? $_SESSION['success_msg'] = $msg : $_SESSION['error_msg'] = "Error saving content!";
    header("Location: manage-instagram-content.php");
    exit();
}

// DELETE CONTENT
if (isset($_GET['del_content'])) {
    $id = intval($_GET['del_content']);
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT media_url FROM tbl_instagram_content WHERE id='$id'"));
    if (!empty($row['media_url']) && file_exists(__DIR__ . '/../' . $row['media_url'])) {
        unlink(__DIR__ . '/../' . $row['media_url']);
    }
    mysqli_query($conn, "DELETE FROM tbl_instagram_content WHERE id='$id'");
    $_SESSION['success_msg'] = "Content deleted successfully!";
    header("Location: manage-instagram-content.php");
    exit();
}

// TOGGLE STATUS
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    mysqli_query($conn, "UPDATE tbl_instagram_content SET is_active = NOT is_active WHERE id='$id'");
    $_SESSION['success_msg'] = "Status updated!";
    header("Location: manage-instagram-content.php");
    exit();
}

// GET CONTENT
$content_result = mysqli_query($conn, "SELECT * FROM tbl_instagram_content ORDER BY display_order ASC");
?>

<style>
    .media-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .media-preview-video {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        background: #000;
    }
    .badge-type {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-image { background: #0d6efd; color: white; }
    .badge-video { background: #dc3545; color: white; }
    .stats-badge {
        background: #f0f0f0;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-right: 5px;
    }
    .icon-option { cursor: pointer; transition: all 0.2s; }
    .icon-option:hover { background-color: #e1f0ff; transform: scale(1.05); }
    .order-input { width: 80px; text-align: center; display: inline-block; }
    .instagram-feed-preview {
        background: #fafafa;
        border-radius: 10px;
        overflow: hidden;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    
    <!-- Header Mobile -->
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <a href="index.html"><img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" /></a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle"><span></span></button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle"><span></span></button>
        </div>
    </div>

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <?php include('includes/sidebar.php'); ?>

            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <div id="kt_header" class="header header-fixed">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="topbar">
                            <div class="topbar-item">
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">

                            <!-- Page Title -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label"><i class="bi bi-instagram"></i> Instagram Content Management</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#addContentModal">
                                            <i class="bi bi-plus-circle"></i> Add Content
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <?php if (isset($_SESSION['success_msg'])): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="bi bi-check-circle"></i> <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error_msg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>

                            <!-- Content List -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title"><h3 class="card-label"><i class="bi bi-table"></i> Instagram Feed</h3></div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="15%">Media</th>
                                                    <th width="20%">Type</th>
                                                    <!-- <th width="20%">Caption</th> -->
                                                    <th width="15%">Stats</th>
                                                    <th width="10%">Order</th>
                                                    <th width="10%">Status</th>
                                                    <th width="35%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($content_result) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($content_result)): ?>
                                                        <tr>
                                                            <td><?= $row['id'] ?></td>
                                                            <td class="text-center">
                                                                <?php if ($row['content_type'] == 'image'): ?>
                                                                    <img src="<?= BASE_URL ."assets/media/". $row['media_url'] ?>" class="media-preview" alt="Instagram post">
                                                                <?php else: ?>
                                                                    <video class="media-preview-video" muted>
                                                                        <source src="<?= BASE_URL ."assets/media/". $row['media_url'] ?>">
                                                                    </video>
                                                                <?php endif; ?>
                                                             </td>
                                                            <td>
                                                                <span class="badge-type badge-<?= $row['content_type'] ?>">
                                                                    <i class="bi bi-<?= $row['content_type'] == 'image' ? 'image' : 'camera-reels' ?>"></i>
                                                                    <?= ucfirst($row['content_type']) ?>
                                                                </span>
                                                                <?php if (!empty($row['instagram_url'])): ?>
                                                                    <br><a href="<?= htmlspecialchars($row['instagram_url']) ?>" target="_blank" class="small">View on Instagram <i class="bi bi-box-arrow-up-right"></i></a>
                                                                <?php endif; ?>
                                                             </td>
                                                            
                                                            <td>
                                                                <div><i class="bi bi-heart-fill text-danger"></i> <strong><?= number_format($row['likes_count']) ?></strong></div>
                                                                <div><i class="bi bi-chat-fill text-primary"></i> <strong><?= number_format($row['comments_count']) ?></strong></div>
                                                             </td>
                                                            <td>
                                                                <form method="post" style="display: inline-block;">
                                                                    <input type="hidden" name="update_order_id" value="<?= $row['id'] ?>">
                                                                    <input type="number" name="display_order" value="<?= $row['display_order'] ?>" class="form-control order-input">
                                                                    <button type="submit" name="update_order" class="btn btn-sm btn-link"><i class="bi bi-check"></i></button>
                                                                </form>
                                                             </td>
                                                            <td>
                                                                <a href="?toggle_status=<?= $row['id'] ?>" class="btn btn-sm btn-<?= $row['is_active'] ? 'success' : 'secondary' ?>" onclick="return confirm('Change status?')">
                                                                    <i class="bi bi-<?= $row['is_active'] ? 'check-circle' : 'circle' ?>"></i> <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                                                </a>
                                                             </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" onclick="editContent(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </button>
                                                                <a href="?del_content=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this content?')">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </a>
                                                             </td>
                                                         </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="8" class="text-center py-5">
                                                        <i class="bi bi-instagram display-4 text-muted"></i>
                                                        <p class="mt-3">No Instagram content found. Click "Add Content" to get started.</p>
                                                    </td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Instagram Feed Preview -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title"><h3 class="card-label"><i class="bi bi-eye"></i> Live Preview (Frontend View)</h3></div>
                                </div>
                                <div class="card-body">
                                    <div class="instagram-feed-preview p-3">
                                        <div class="row">
                                            <?php
                                            $preview_query = mysqli_query($conn, "SELECT * FROM tbl_instagram_content WHERE is_active = 1 ORDER BY display_order ASC LIMIT 4");
                                            if (mysqli_num_rows($preview_query) > 0):
                                                while ($preview = mysqli_fetch_assoc($preview_query)):
                                            ?>
                                                <div class="col-md-3 col-sm-6 mb-3">
                                                    <div class="card h-100">
                                                        <?php if ($preview['content_type'] == 'image'): ?>
                                                            <img src="<?= BASE_URL ."assets/media/". $preview['media_url'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Instagram">
                                                        <?php else: ?>
                                                            <video class="card-img-top" style="height: 200px; object-fit: cover;" muted>
                                                                <source src="<?= BASE_URL ."assets/media/". $preview['media_url'] ?>">
                                                            </video>
                                                        <?php endif; ?>
                                                        <div class="card-body p-2">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span><i class="bi bi-heart-fill text-danger"></i> <?= number_format($preview['likes_count']) ?></span>
                                                                <span><i class="bi bi-chat-fill text-primary"></i> <?= number_format($preview['comments_count']) ?></span>
                                                                <?php if (!empty($preview['instagram_url'])): ?>
                                                                    <a href="<?= htmlspecialchars($preview['instagram_url']) ?>" target="_blank"><i class="bi bi-instagram text-danger"></i></a>
                                                                <?php endif; ?>
                                                            </div>
                                                            <small class="text-muted"><?= htmlspecialchars(substr($preview['caption'], 0, 50)) ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; else: ?>
                                                <div class="col-12 text-center text-muted py-5">No active content to display</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add/Edit Modal -->
                            <div class="modal fade" id="addContentModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add Instagram Content</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="content_id" id="content_id">
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Content Type *</label>
                                                            <select name="content_type" id="content_type" class="form-control" required>
                                                                <option value="image">Image Post</option>
                                                                <option value="video">Video Post</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Display Order</label>
                                                            <input type="number" name="display_order" id="display_order" class="form-control" value="0">
                                                            <small class="text-muted">Lower number displays first</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Media File *</label>
                                                    <input type="file" name="media_file" id="media_file" class="form-control-file" accept="image/*,video/*" required>
                                                    <small class="text-muted">Supported: JPG, PNG, GIF (images) or MP4, WebM (videos)</small>
                                                    <div id="current_media_preview" class="mt-2" style="display: none;"></div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Instagram Post URL (Optional)</label>
                                                    <input type="url" name="instagram_url" id="instagram_url" class="form-control" placeholder="https://www.instagram.com/p/...">
                                                    <small class="text-muted">Link to original Instagram post</small>
                                                </div>

                                                <div class="form-group d-none">
                                                    <label>Caption</label>
                                                    <textarea name="caption" id="caption" class="form-control" rows="3" placeholder="Instagram caption..."></textarea>
                                                </div>

                                                <div class="row d-none">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="bi bi-heart-fill text-danger"></i> Likes Count</label>
                                                            <input type="number" name="likes_count" id="likes_count" class="form-control" value="0" placeholder="e.g., 1234">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><i class="bi bi-chat-fill text-primary"></i> Comments Count</label>
                                                            <input type="number" name="comments_count" id="comments_count" class="form-control" value="0" placeholder="e.g., 56">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="is_active" id="is_active" checked>
                                                        <span></span> Active (Show on frontend)
                                                    </label>
                                                </div>

                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i>
                                                    <strong>Tip:</strong> This content will be displayed in your Instagram feed section. Add images and videos to showcase your social media presence.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" name="save_content" class="btn btn-primary">Save Content</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <?php include('includes/footer-inner.php'); ?>
            </div>
        </div>
    </div>

    <script>
        function editContent(data) {
            document.getElementById('content_id').value = data.id;
            document.getElementById('content_type').value = data.content_type;
            document.getElementById('instagram_url').value = data.instagram_url || '';
            document.getElementById('caption').value = data.caption || '';
            document.getElementById('likes_count').value = data.likes_count || 0;
            document.getElementById('comments_count').value = data.comments_count || 0;
            document.getElementById('display_order').value = data.display_order;
            document.getElementById('is_active').checked = data.is_active == 1;
            
            // Show current media preview
            const previewDiv = document.getElementById('current_media_preview');
            if (data.media_url) {
                let previewHtml = '<strong>Current Media:</strong><br>';
                if (data.content_type === 'image') {
                    previewHtml += `<img src="<?= BASE_URL ?>${data.media_url}" class="media-preview mt-2">`;
                } else {
                    previewHtml += `<video width="200" controls class="mt-2"><source src="<?= BASE_URL ?>${data.media_url}"></video>`;
                }
                previewDiv.innerHTML = previewHtml;
                previewDiv.style.display = 'block';
            } else {
                previewDiv.style.display = 'none';
            }
            
            // Make file upload optional for edit
            document.getElementById('media_file').required = false;
            
            document.querySelector('#addContentModal .modal-title').innerHTML = '<i class="bi bi-pencil"></i> Edit Instagram Content';
            $('#addContentModal').modal('show');
        }
        
        $('#addContentModal').on('hidden.bs.modal', function() {
            document.querySelector('form').reset();
            document.getElementById('content_id').value = '';
            document.getElementById('current_media_preview').style.display = 'none';
            document.getElementById('media_file').required = true;
            document.querySelector('#addContentModal .modal-title').innerHTML = '<i class="bi bi-plus-circle"></i> Add Instagram Content';
        });
        
        // Content type change handler
        document.getElementById('content_type')?.addEventListener('change', function() {
            const fileInput = document.getElementById('media_file');
            if (this.value === 'image') {
                fileInput.accept = 'image/*';
            } else {
                fileInput.accept = 'video/*';
            }
        });
        
        // Preview media before upload
        document.getElementById('media_file')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewDiv = document.getElementById('current_media_preview');
                    const contentType = document.getElementById('content_type').value;
                    
                    let previewHtml = '<strong>Preview:</strong><br>';
                    if (contentType === 'image') {
                        previewHtml += `<img src="${event.target.result}" class="media-preview mt-2">`;
                    } else {
                        previewHtml += `<video width="200" controls class="mt-2"><source src="${event.target.result}"></video>`;
                    }
                    previewDiv.innerHTML = previewHtml;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Update order functionality
        document.querySelectorAll('form[style*="inline-block"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const orderInput = this.querySelector('input[name="display_order"]');
                const id = this.querySelector('input[name="update_order_id"]').value;
                const order = orderInput.value;
                
                fetch('update-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&order=${order}`
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          location.reload();
                      }
                  });
            });
        });
    </script>
</body>
</html>