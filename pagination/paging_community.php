<?php
$ends_count = 1; 
$middle_count = 1;
$dots = true;
$prev_next=true;
?>
	<?php
if ($prev_next && $page && 1 < $page) {  //print previous button?
?>
<a href="javascript:void(0)" data-id="<?=$page-1?>"  class="prevposts-link paging_community"><i class="fa fa-caret-left"></i></a>
<?php }

for ($i = 1; $i <= $no_of_page; $i++) {
if ($i == $page) {
?>
<a class="page-numbers current current-page"><?php echo $i; ?></a>
<?php
$dots = true;
} else {
if ($i <= $ends_count || ($page && $i >= $page - $middle_count && $i <= $page + $middle_count) || $i > $no_of_page - $ends_count) { 
?>
<a class="page-numbers paging_community" data-id="<?=$i?>"><?php echo $i; ?></a>
<?php
$dots = true;
} elseif ($dots) {
?><a class="page-numbers">&hellip;</a>
<?php
$dots = false;
}
}
}

if ($prev_next && $page && ($page < $no_of_page || -1 == $no_of_page)) { //print next button?
?>
<a href="javascript:void(0)" data-id="<?=$page+1?>" class="nextposts-link paging_community"><i class="fa fa-caret-right"></i></a>
<?php }?>
