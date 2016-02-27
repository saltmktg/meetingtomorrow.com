<?php
/**************************************************************
*		FedEx Tracking Update Handler
*		Author: Ed Madrigal 2013.09.06
***************************************************************/
$type = $_GET['type'];
$restletURL = 'https://rest.netsuite.com/app/site/hosting/restlet.nl?script=253&deploy=1&type=' . $type;
$headersJSON = array('User-Agent-x: SuiteScript-Call','Authorization: NLAuth nlauth_account=3373305, nlauth_email=restletuser@meetingtomorrow.com, nlauth_signature=SpRMqAaPcK5tBcPG, nlauth_role=1020','Content-Type: application/json');
$headersText = array('User-Agent-x: SuiteScript-Call','Authorization: NLAuth nlauth_account=3373305, nlauth_email=restletuser@meetingtomorrow.com, nlauth_signature=SpRMqAaPcK5tBcPG, nlauth_role=1020','Content-Type: text/plain');

$ch_report = curl_init();
curl_setopt($ch_report,CURLOPT_HEADER,false); // don't return headers
curl_setopt($ch_report,CURLOPT_URL,$restletURL);
curl_setopt($ch_report,CURLOPT_FOLLOWLOCATION,true); // follow redirects
curl_setopt($ch_report,CURLOPT_HTTPGET,true);
curl_setopt($ch_report,CURLOPT_MAXREDIRS,10);
curl_setopt($ch_report,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch_report,CURLOPT_CONNECTTIMEOUT,0);
curl_setopt($ch_report,CURLOPT_TIMEOUT,0);
curl_setopt($ch_report,CURLOPT_HTTPHEADER,$headersJSON);
$ReportResultsBase64 = curl_exec($ch_report);
curl_close($ch_report);

$ReportResultsJSON = base64_decode($ReportResultsBase64);
$ReportResultsArray = json_decode($ReportResultsJSON);
$data = array();
$ResultSetIndex = 0;

//echo 'We got through';

foreach ( $ReportResultsArray as $id=>$d )
{
	$data[$id] = $d;
	
	if ( $ResultSetIndex == 50 )
	{
		multiRequest($data,$restletURL,$headersText);
		
		$data = array();
		$ResultSetIndex = 0;
	}
	else { $ResultSetIndex++; }
}

if ( $ResultSetIndex != 0 ) { multiRequest($data,$restletURL,$headersText); }
?>

<?
function multiRequest($data,$restletURL,$headersText)
{
	$curly = array(); // array of curl handles
	$result = array();
	$mh = curl_multi_init(); // multi handle
	$running = null;
	
	foreach ( $data as $id=>$d ) // loop through $data and create curl handles and then add them to the multi-handle
	{
		$url = $d->url;
		$xml = $d->xml;
		
		$curly[$id] = curl_init();
		curl_setopt($curly[$id],CURLOPT_URL,$url);
		curl_setopt($curly[$id],CURLOPT_HEADER,false);
		curl_setopt($curly[$id],CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curly[$id],CURLOPT_CONNECTTIMEOUT,120);
		curl_setopt($curly[$id],CURLOPT_TIMEOUT,120);
		curl_setopt($curly[$id],CURLOPT_POST,1);
		curl_setopt($curly[$id],CURLOPT_POSTFIELDS,$xml);
		
		curl_multi_add_handle($mh,$curly[$id]);
	}
	
	do
	{
		curl_multi_exec($mh,$running); // execute the handles
	}
	while ( $running > 0 );
	
	foreach ( $curly as $id=>$c ) // get content and remove handles
	{
		$Reply['id'] = $data[$id]->id;
		$Reply['status'] = $data[$id]->status;
		$Reply['number'] = $data[$id]->number;
		$Reply['uniqueid'] = $data[$id]->uniqueid;
		$Reply['xml'] = curl_multi_getcontent($c);
		$result[$id] = $Reply;
		curl_multi_remove_handle($mh,$c);
	}
	
	curl_multi_close($mh);
	
	$resultJSON = json_encode(array_merge($result));
	
	//echo '<br/><br/>Finished talking to FedEx: Final JSON<br/><br/>';
	//var_dump($resultJSON);
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$restletURL);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,base64_encode($resultJSON));
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);
	curl_setopt($ch,CURLOPT_TIMEOUT,0);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headersText);
	curl_exec($ch);
	curl_close($ch);
}
?>