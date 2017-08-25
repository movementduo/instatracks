<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index">
	<title><?php echo $title; ?></title>
	<link href="/assets/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="/assets/css/style.css" type="text/css" rel="stylesheet" />
	<script src="/assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/script.js" type="text/javascript"></script>
</head>
	<body>
		<div id="landscape-screen"><p>Please use this app in portrait mode for the best viewing experience.</p></div>
		<?php echo $body; ?>
	</body>
	<script>
		$(document).ready(function() {
			function getMobileOS() {
			  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

			      // Windows Phone must come first because its UA also contains "Android"
			    if (/windows phone/i.test(userAgent)) {
			        return "Windows Phone";
			    }

			    if (/android/i.test(userAgent)) {
			    		$('.ios-OS').css('display', 'none');
			    		$('.other-OS').css('display', 'block');
			        return "Android";
			    }

			    // iOS detection from: http://stackoverflow.com/a/9039885/177710
			    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
			    		$('.ios-OS').css('display', 'block');
			    		$('.other-OS').css('display', 'none');
			        return "iOS";
			    }

			    return "unknown";
			}
			console.log(getMobileOS());
		});
	</script>
</html>
