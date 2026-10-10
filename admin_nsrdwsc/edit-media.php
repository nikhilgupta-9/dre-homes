<?php
include('db/config.php');

// Pictures are now added and described on the gallery page itself. This address is kept
// so old links and bookmarks still arrive somewhere useful.
header('Location: ' . BASE_URL . 'manage-media.php');
exit;
