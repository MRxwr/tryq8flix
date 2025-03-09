<?php
// room types [ 1 for public game, 2 for private game, 3 for group game ]
if( isset($_GET["roomType"]) && !empty($_GET["roomType"]) ){
    if( $token == "" ){
        echo dataOutput(array("msg" => "401 Unauthorized"));die();
    }elseif( $user = selectDB2("`id`, `username`, `avatar`","users","`keepalive` = '{$token}'") ){
        $user = $user[0];
    }
    if( $_GET["roomType"] == 1 ){
        if( $room = selectDB("qas_rooms","`type` = '1' AND `status` = '0' AND `hidden` = '0'") ){
            $members = json_decode($room[0]["members"],true);
            if( !in_array($user["id"],$members) ){
                $members[] = $user;
                $members = json_encode($members);
                $data = array("members" => $members);
                if( updateDB("qas_rooms",$data,"`id` = '{$room[0]["id"]}'") ){
                    echo dataOutput($room[0]);die();
                }else{
                    echo dataError(array("msg" => "could not add user to public room"));die();
                }
            }else{
                echo dataOutput($room[0]);die();
            }
        }else{
            $members = json_encode(array($user));
            // generate new random room code 6 characters and digits long
            $roomCode = generateRandomString(6);
            while( selectDB("qas_rooms","`code` = '{$roomCode}'") ){
                $roomCode = generateRandomString(6);
            }
            $data = array("members" => $members, "type" => 1, "code" => $roomCode);
            if( insertDB("qas_rooms",$data) ){
                $room = selectDB("qas_rooms","`type` = '1' AND `status` = '0' AND `hidden` = '0'");
                echo dataOutput($room[0]);die();
            }else{
                echo dataError(array("msg" => "could not create new public room"));die();
            }
        }
            echo dataOutput($room);die();
    }
}
// Function to generate a random string of specified length
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>