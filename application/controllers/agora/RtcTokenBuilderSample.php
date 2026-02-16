<?php
include("RtcTokenBuilder.php");

/*$appID = "970CA35de60c44645bbae8a215061b33";
$appCertificate = "5CFd2fd1755d40ecb72977518be15d3b";
$channelName = "7d72365eb983485397e3e3f9d460bdda";
$uid = 2882341273;
$uidStr = "2882341273";*/


$appID = "e4e6037834024af9b4a903194a9d1c07";
$appCertificate = "0e5c3d0ebad34778aae5aa141c3952b5";
$channelName = $_POST['appiontment_id'];  //"Doctto-video-audio-call";
$uid = 0;
$uidStr = "2882341273";


$role = RtcTokenBuilder::RoleAttendee;
$expireTimeInSeconds = 3600;
$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

$token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
$user_generated_token = $token . PHP_EOL;

$user_generated_token = str_replace("\r\n", "", $user_generated_token);

$user_generated_token = str_replace("\r", "", $user_generated_token);
$user_generated_token = str_replace("\n", "", $user_generated_token);



$arr = array("status"=>TRUE,"token"=>$user_generated_token);
 echo json_encode($arr);
die;
/*$token = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uidStr, $role, $privilegeExpiredTs);
echo 'Token with user account: ' . $token . PHP_EOL;*/
?>
