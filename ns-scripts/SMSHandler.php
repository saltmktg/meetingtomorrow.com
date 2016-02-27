<?php
/**************************************************************
*		SMS Handler
*		Author: Ed Madrigal 2013.03.29
***************************************************************/
$type = $_GET['type'];

if ( $type == 'callback' )
{
	$fields_string = '{"sid":"' . $_POST['MessageSid'] . '","status":"' . $_POST['MessageStatus'] . '"}';
	
	sleep(3);
}
else
{
	$from_number = substr($_POST['From'],2,10);
	$body =  str_replace('"','\\"',$_POST['Body']);
	$fields_string = '{"sid":"' . $_POST['MessageSid'] . '","to":"' . $_POST['To'] . '","from":"' . $from_number . '","body":"' . $body . '"}';
}

$url = 'https://forms.netsuite.com/app/site/hosting/scriptlet.nl?script=209&deploy=1&compid=3373305&h=2bdb70813d89a751aecc&type=' . $type;

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
curl_exec($ch);
curl_close($ch);
?>