<?php
if ($banners = selectDB2("`id`, `url`, `imageurl`","banners","`status` = '0' AND `hidden` = '0'")) {
    echo dataOutput($banners);
} else {
    echo dataError(array(['error' => 'No banners found']));
}
?>