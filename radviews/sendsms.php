<?php
/*****************************************************************************
**        Name: Outreach
**    Function: This function is used to send SMS message to a mobile phone.
**		This implementation uses outreach.pk HTTP->SMS gateway.
**		You can implement your own SMS gateway in this function easily.
**      Inputs: $recp - Mobile number
**		$body - Message body
**		$errmsg - Pointer to error message returned by the gateway
**      Result: TRUE if API succeeded or FALSE
*****************************************************************************/

function sendsms($recp, $body, &$errmsg)
{

  $sender="PaceTelecom"; 	// enter your Mask here
  $id="Khyber@007";		// enter your username here
  $pass="lahore123";		// enter your password here



$body = html_entity_decode($body, ENT_COMPAT, "UTF-8");  
$body = rawurlencode($body);
$sender = rawurlencode($sender);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.outreach.pk/api/sendsms.php/sendsms/url?id=$id&pass=$pass&mask=$sender&to=$recp&lang=English&msg=$body");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$res = curl_exec($ch);
curl_close($ch);
PRINT $res;

}
?>