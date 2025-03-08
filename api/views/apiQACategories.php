<?php

$categories = selectDB("qas_categories","`status` = '0' AND `hidden` = '0'");

if ($categories) {
    $mainCategories = [];
    $subCategories = [];

    foreach ($categories as $category) {
        if ($category['parentId'] == 0) {
            $mainCategories[] = [
                'id' => $category['id'],
                'date' => $category['date'],
                'title' => $category['title'],
                'subTitle' => $category['subTitle'],
                'image' => $category['image'],
                'subcategories' => [],
            ];
        } else {
            $subCategories[$category['parentId']][] = [
                'id' => $category['id'],
                'date' => $category['date'],
                'title' => $category['title'],
                'subTitle' => $category['subTitle'],
                'image' => $category['image'],
            ];
        }
    }

    // Add subcategories to their parent categories
    foreach ($mainCategories as &$mainCategory) {
        if (isset($subCategories[$mainCategory['id']])) {
            $mainCategory['subcategories'] = $subCategories[$mainCategory['id']];
        }
    }

    echo dataOutput($mainCategories);
} else {
    echo dataError(array(['error' => 'No categories found']));
}
?>