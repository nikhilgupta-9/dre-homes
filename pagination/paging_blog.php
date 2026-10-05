<?php
$ends_count = 1;
$middle_count = 1;
$dots = false;
$prev_next = true;
?>

<?php if ($prev_next && $page > 1) { ?>
    <a href="javascript:void(0)" data-id="<?= $page - 1 ?>" class="prevposts-link paging_blog">
        <i class="fa fa-caret-left"></i>
    </a>
<?php }

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) { ?>
        <a class="page-numbers current current-page"><?= $i ?></a>
        <?php $dots = false;
    } elseif (
        $i <= $ends_count ||
        ($i >= $page - $middle_count && $i <= $page + $middle_count) ||
        $i > $no_of_page - $ends_count
    ) { ?>
        <a class="page-numbers paging_blog" data-id="<?= $i ?>"><?= $i ?></a>
        <?php $dots = false;
    } elseif (!$dots) { ?>
        <a class="page-numbers">&hellip;</a>
        <?php $dots = true;
    }
}

if ($prev_next && $page < $no_of_page) { ?>
    <a href="javascript:void(0)" data-id="<?= $page + 1 ?>" class="nextposts-link paging_blog">
        <i class="fa fa-caret-right"></i>
    </a>
<?php } ?>