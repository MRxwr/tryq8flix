<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL ^ E_NOTICE);  

function checkCode($link) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://uptobox.com/api/link/info?fileCodes=' . $link,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
	if ( isset($data['data']['list']) ){
		foreach ($data['data']['list'] as $item) {
			if (isset($item['error']) && $item['error']['code'] === 28) {
				return true;
			}
		}
	}
    return false;
}

function extractFileCode($input) {
    $pattern = '/https:\/\/uptobox.com\/(.+)/';
    if (preg_match($pattern, $input, $matches)) {
        // If the input matches the URL pattern, extract the file code from it
        return $matches[1];
    } else {
        // Otherwise, assume the input is already a file code and return it as is
        return $input;
    }
}

// Set the initial offset value to 0
$offset = 0;
$limit = 100;

// Loop through the video links in batches of 100
while (true) {
    // Build the query with the current offset and limit values
    $query = "SELECT `id`, `videolink`, `category` FROM `posts` ORDER BY `id` LIMIT {$limit} OFFSET {$offset}";
    $result = mysqli_query($dbconnect, $query);
    // Check if there are any rows left to process
    if (mysqli_num_rows($result) == 0) {
        break;
    }
    // Loop through the rows and delete any links with code 28
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $link = $row['videolink'];
        $category = $row['category'];
        // Extract the file code from the video link
        $file_code = extractFileCode($link);
        // Check if the link has code 28
        if (checkCode($file_code)) {
            // Delete the link from the database
            $delete_query = "DELETE FROM `posts` WHERE id = {$id}";
            mysqli_query($dbconnect, $delete_query);
            // Print a message indicating that the link has been deleted
            echo "Link '{$link}' from category: {$category} has been deleted from the database.\n";
        }
    }
    // Increment the offset value
    $offset += $limit;
}

$deleteCategory = "DELETE FROM `category` WHERE `id` NOT IN (SELECT DISTINCT `catid` FROM `posts`)";
mysqli_query($dbconnect, $deleteCategory);
// Close the database connection
mysqli_close($dbconnect);

?>