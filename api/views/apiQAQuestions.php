<?php
if(isset($_GET['categoryId'])){
    $categoryId = intval($_GET['categoryId']);
    $questions10 = selectDB2("`id`, `type`, `question`, `correctAnswer`, `answer1`, `answer2`, `answer3`, `answerTrue`, `answerFalse`, `image`, `video`, `audio`, `points`","qas", "`category` = '$categoryId' AND `points` = '10' AND `status` = '0' AND `hidden` = '0' ORDER BY RAND() LIMIT 2");
    $questions20 = selectDB2("`id`, `type`, `question`, `correctAnswer`, `answer1`, `answer2`, `answer3`, `answerTrue`, `answerFalse`, `image`, `video`, `audio`, `points`","qas", "`category` = '$categoryId' AND `points` = '20' AND `status` = '0' AND `hidden` = '0' ORDER BY RAND() LIMIT 2");
    $questions40 = selectDB2("`id`, `type`, `question`, `correctAnswer`, `answer1`, `answer2`, `answer3`, `answerTrue`, `answerFalse`, `image`, `video`, `audio`, `points`","qas", "`category` = '$categoryId' AND `points` = '40' AND `status` = '0' AND `hidden` = '0' ORDER BY RAND() LIMIT 1");
    $questions = array_merge((array)$questions10, (array)$questions20, (array)$questions40);
    if (!empty($questions)) {
        echo dataOutput($questions);
    } else {
        echo dataError(array(['error' => 'No questions found for this category']));
    }
} else {
    echo dataError(array(['error' => 'Category ID is required']));
}

?>
