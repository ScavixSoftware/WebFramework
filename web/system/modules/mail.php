<?
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>
 *
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
 
/**
 * Initializes the mail module.
 * 
 * @return void
 */
function mail_init()
{
	global $CONFIG;

	if( !isset($CONFIG['mail']['logging']) )
		$CONFIG['mail']['logging'] = false;
	
	$CONFIG['class_path']['system'][] = dirname(__FILE__).'/mail/';

// whatch out: setting this will only send the emails matching this array
//	if( !isset($CONFIG['mail']['dev_whitelist']) )
//		$CONFIG['mail']['dev_whitelist'] = array("");
}

/**
 * Prepares an email.
 * 
 * @param string $recipient The mail recipient
 * @param string $subject The subject
 * @param string $message The message (may be HTML formatted)
 * @param string $plainmessage Optional plain message (may differ from $message)
 * @param array $attachments Array of filenames to attach
 * @return PHPMailer A PHPMailer object ready to be sent.
 */
function mail_prepare($recipient,$subject,$message,$plainmessage="",$attachments=array())
{
	global $CONFIG;

	require_once(dirname(__FILE__)."/mail/class.smtp.php");
	require_once(dirname(__FILE__)."/mail/class.phpmailer.php");

	if( isDev() && isset($CONFIG['mail']['dev_whitelist']))
	{
		$isvalidrecipient = false;
		// on dev server, only domains/recipients in the whitelist are allowed
		foreach($CONFIG['mail']['dev_whitelist'] as $needle)
		{
			if(stripos($recipient, $needle) !== false)
			{
				$isvalidrecipient = true;
				break;
			}
		}
		if(!$isvalidrecipient)
		{
			// if not found in whitelist, send to predefined recipient
			$recipient = $CONFIG['mail']['dev_recipient'];
			log_debug("email recipient changed to: ".var_export($recipient, true));
		}
	}

	$mail = new PHPMailer(true);
	$mail->SetLanguage("en", dirname(__FILE__)."/mail/language/");
	$mail->CharSet = "utf-8";

	$mail->IsSMTP();
	$mail->Host     = $CONFIG['mail']['smtp_server'];
	if ($CONFIG['mail']['smtp_auth'])
	{
		$mail->SMTPAuth = true;
		$mail->Username = $CONFIG['mail']['smtp_user'];
		$mail->Password = $CONFIG['mail']['smtp_pass'];
	}

	if( isset($CONFIG['mail']['smtp_tls']) && $CONFIG['mail']['smtp_tls'] == true )
	{
		$mail->SMTPSecure = 'tls';
	}

	$mail->From     = $CONFIG['mail']['from'];
	$mail->FromName = $CONFIG['mail']['from_name'];
	try
	{
		if(is_array($recipient))
		{
			foreach($recipient as $r)
				$mail->AddAddress($r);
		}
		else
			$mail->AddAddress($recipient);
	}
	catch(Exception $ex){ WdfException::Log($ex); $res = false;}
	
	$mail->AddReplyTo($CONFIG['mail']['from']);

	$mail->WordWrap = 80;

	$env = getEnvironment();
    if( isNotLive() && !starts_with($subject, "[$env]"))
        $subject = "[$env] $subject";
	
	$mail->Subject = $subject;
	$mail->ContentType = "text/html";

    $message = str_ireplace("<br>","<br/>",$message);
    $message = str_ireplace("<br >","<br/>",$message);
    $message = str_ireplace("<br />","<br/>",$message);
    $message = str_ireplace("{crlf}","<br/>",$message);
    $message = str_ireplace("\\r","\r",$message);
    $message = str_ireplace("\\'","'",$message);
    $message = str_ireplace("\\\"","\"",$message);
    $message = str_ireplace("\r\n","<br/>",$message);
    $message = str_ireplace("\n","<br/>",$message);

    $message = str_ireplace("<br/>","<br/>\n",$message);
	$message = "<p style='font-family: verdana, arial, helvetica, sans-serif; font-size: 14px'>".$message."<p>";
	$mail->Body     =  $message;
	$mail->AltBody  =  $plainmessage==""?$message:$plainmessage;

	if( !is_array($attachments) )
		$attachments = array($attachments);

	foreach( $attachments as $a )
		if( file_exists($a) )
			$mail->AddAttachment($a);
		else
			log_debug("email attachment not found: $a");
	
	return $mail;
}

/**
 * Sends an email.
 * 
 * @param mixed $recipient Email recipient as string. If $recipient is <PHPMailer> will ignore all other arguments and use this.
 * @param string $subject The subject
 * @param string $message The message (may be HTML formatted)
 * @param string $plainmessage Optional plain message (may differ from $message)
 * @param array $attachments Array of filenames to attach
 * @return boolean true on success or string on error
 */
function mail_send($recipient,$subject="",$message="",$plainmessage="",$attachments=array())
{
	if( is_object($recipient) && $recipient instanceof PHPMailer )
		$mail = $recipient;
	else
		$mail = mail_prepare($recipient,$subject,$message,$plainmessage,$attachments);
	$res = false;
	try
	{
		$res = $mail->Send();
	}
	catch(Exception $ex){ WdfException::Log($ex); $res = false;}

	if( !$res )
	{
		log_trace("mail_send($subject,$message): " . $mail->ErrorInfo, $recipient);
		return $mail->ErrorInfo;
	}
	return true;
}

/**
 * Checks if a string is a syntactically correct email address.
 * 
 * @param string $string String to check
 * @return bool true or false
 */
function is_mail($string)
{
	return preg_match('%^(.+)@(.{2,})\.(.{2,4})$%i', $string);
}

/**
 * Checks for valid email address.
 * 
 * @param string $email Value to check
 * @param bool $check_dns_too If true will check the domain part for valid DNS records too
 * @return boolean true or false
 */
function mail_validate($email,$check_dns_too=true)
{
//	log_debug("mail_validate($email)");
	if( !filter_var($email,FILTER_VALIDATE_EMAIL) )
		return false;

	if( preg_match("/^[a-zA-Z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+(\.[a-zA-Z0-9,!#\$%&'\*\+\/=\?\^_`\{\|}~-]+)*@[a-zA-Z0-9-]+(\.[a-z0-9-]+)*\.([a-zA-Z]{2,})$/", $email, $check) )
	{
		if( $check_dns_too )
		{
			$types = array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT');
			$host = substr(strstr($check[0], '@'), 1);
			foreach( $types as $t )
				if( checkdnsrr($host,$t) )
					return true;
		}
		else
			return true;
	}
	return false;
}
