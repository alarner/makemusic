<?php 
class MailerComponent extends Object
{

var $to              = array();
var $from            = "admin@makemusicny.org";
var $fromname        = "Make Music New York";
var $Subject         = null;
var $Message         = null;
var $errorMsg  		 = null;

function socketmail() {
  

// $this->Email->smtpOptions = array(
 //       'port'=>'465', 
  //      'timeout'=>'30',
   //     'host' => 'ssl://smtp.gmail.com',
     //   'username'=>'admin@makemusicny.org',
       // 'password'=>'672Bede735!'
   //);  

//$this->Email->from    = 'Somebody <somebody@example.com>';
//$this->Email->to      = 'Somebody Else <mike_infranco@hotmail.com>';
//$this->Email->subject = 'Test';
//$this->Email->send();



	ini_set("smtp_port", "465");  // Optional
	ini_set("SMTP", "ssl://smtp.googlemail.com"); // Optional smtp-tony.timeoutny.com  

	//try the powweb SMTP server
	//ini_set("smtp_port", "25");
	//ini_set("SMTP", "smtp.powweb.com");
	//ini_set("SMTP", "tls://smtp.gmail.com");
	//ini_set("SMTP", "10.0.0.220"); // Optional


	ini_set("sendmail_from", $this->from);

        $connect = fsockopen(ini_get("SMTP"), ini_get("smtp_port"), $errno, $errstr, 60) or die("Could not talk to the sendmail server!");

        $rcv = fgets($connect, 1024);

      fputs($connect, "HELO {$_SERVER['SERVER_NAME']}\r\n");
      
        $rcv = fgets($connect, 1024);


  // perform authentication
  fputs($connect, "auth login\r\n");
  $rcv .=fgets($connect,256);
  if(substr($rcv,0,3) != "334") $this->errorMsg = "Failed to Initiate Authentication";
 
  	fputs($connect, base64_encode("admin@makemusicny.org")."\r\n");
  	$rcv =fgets($connect,256);
  	if(substr($rcv,0,3) != "334") $this->errorMsg =  "Failed to Provide Username for Authentication";
 
  	fputs($connect, base64_encode("672Bede735!")."\r\n");
  	$rcv =fgets($connect,256);
  	if(substr($rcv,0,3) != "220") {
		$this->errorMsg =  "Failed to Authenticate";
	
  	}
//print "rcv: " . $rcv . "<br />";
  while (list($toKey, $toValue) = each($this->to)) {
      fputs($connect, "MAIL FROM: <" . $this->from . ">\r\n");

        $rcv = fgets($connect, 1024);
//print "rcv: " . $rcv . "<br />";
      fputs($connect, "RCPT TO: <" . $toValue . ">\r\n");
        $rcv = fgets($connect, 1024);
//print "rcv: " . $rcv . "<br />";
      fputs($connect, "DATA\r\n");
        $rcv = fgets($connect, 1024);
//print "rcv: " . $rcv . "<br />";
          
  fputs($connect, "Subject: $this->Subject\r\n");
  fputs($connect, "From: $this->fromname <$this->from>\r\n");
  fputs($connect, "To: $toKey  <".$toValue.">\r\n");
  fputs($connect, "X-Sender: <$this->from>\r\n");
  fputs($connect, "Return-Path: <$this->from>\r\n");
  fputs($connect, "Errors-To: <$this->from>\r\n");
  fputs($connect, "X-Mailer: PHP\r\n");
  fputs($connect, "X-Priority: 3\r\n");
  fputs($connect, "Content-Type: text/plain; charset=iso-8859-1\r\n");
  fputs($connect, "\r\n");
  fputs($connect, stripslashes($this->Message)." \r\n");
  fputs($connect, ".\r\n");

   $rcv = fgets($connect, 1024);
//print "rcv: " . $rcv . "<br />";
   fputs($connect, "RSET\r\n");
     $rcv = fgets($connect, 1024);
//print "rcv: " . $rcv . "<br />";

  }

   fputs ($connect, "QUIT\r\n");
    $rcv = fgets ($connect, 1024);
//print "rcv: " . $rcv . "<br />";
  fclose($connect);
  ini_restore("sendmail_from");

    }

function AddAddress($name = "",$address ) {

        $cur = count($this->to);
        $this->to["$name"] = trim($address);

    }

}
?>