<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link href='http://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
		<link rel="image_src" href="http://creative.vantagelocal.com/images/wflogo.jpg" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Password Reset</title>
		<link rel="shortcut icon" href="https://s3.amazonaws.com/brandcdn-assets/images/favicon_blue.png" />
		<meta name="description" content="Password reset" />
	</head>
	<body>
		<style>
		 	body {font-family: 'Oxygen', sans-serif; font-weight: normal;}
			p {font-size: 18px;}
			a {text-decoration:none;}
			a:visited {color:#00a8ff;}
			a:link {color:#00a8ff;}
		</style>
		<table width="600">
			<tr>
				<td><br><br>
				<img src="http://creative.vantagelocal.com/email/pwreset.jpg" width="600" height="52" />
				</td>

			</tr>
			<tr>
				<td style="padding:50px">
			        <p>Hi <?php echo $firstname;?> <?php echo $lastname;?> (Login Email: <b style="font-family: courier; font-size:15px"><?php echo $email; ?></b>),</p> 
					<big style="font: bold 16px/18px Arial, Helvetica, sans-serif;">Your <?php echo $partner_url; ?> password has been reset.</big><br />
					<br />
					<br />
					If you believe you have received this notice in error, please contact your account rep.<br />
					<br />
					<br />
					Thank you,<br />
					The <?php echo $partner_name; ?> Team
				</td>
			</tr>
		</table>
	</body>
</html>