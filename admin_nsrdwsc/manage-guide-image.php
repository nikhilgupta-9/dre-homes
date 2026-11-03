<?php
include('db/config.php');

// The steps of a guide are now listed on the guide's own edit page. This address is kept
// so old links and bookmarks still arrive there.
$guide_id = (int) ($_GET['guide_id'] ?? 0);
header('Location: ' . BASE_URL . ($guide_id > 0 ? 'edit-guide.php?edit=' . base64_encode($guide_id) . '#sec-steps' : 'manage-guide.php'));
exit;
