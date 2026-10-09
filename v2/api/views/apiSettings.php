<?php
if ($settings = selectDB2("`about`, `terms`, `policy`, `whatsapp`, `instagram`, `twitter`, `tiktok`","settings","`status` = '0' AND `hidden` = '0'")) {
    echo dataOutput($settings);
} else {
    echo dataError(array(['error' => 'No settings found']));
}
?>