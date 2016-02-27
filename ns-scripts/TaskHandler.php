<?php
/**************************************************************
*		FedEx Tracking Update Handler
*		Author: Ed Madrigal 2013.09.06
***************************************************************/
$restletURL = 'https://rest.netsuite.com/app/site/hosting/restlet.nl?script=266&deploy=1';
$headersJSON = array('User-Agent-x: SuiteScript-Call','Authorization: NLAuth nlauth_account=3373305, nlauth_email=restletuser@meetingtomorrow.com, nlauth_signature=SpRMqAaPcK5tBcPG, nlauth_role=1020','Content-Type: application/json');

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
curl_exec($ch_report);
curl_close($ch_report);
?>