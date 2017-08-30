<div id="video-one" style="height: 100%;">
	<div id="video-player">
		<video width="100%" height="100%" controls>
			<source src="<?php echo CLOUDFRONT_ENDPOINT.$video['videoFile']; ?>" stype="video/mp4">
		</video>
	</div>
	<div class="con">
		<!-- IOS -->
		<div class="get-video-CTA ios-OS"> 
			<button class="cta-orange"><a href="#email-video" class="open-overlay" id="email-video-">Email video</a></button>
		</div>
		<!-- Android/Other OS -->
		<div class="get-video-CTA other-OS">
			<button class="cta-orange"><a href="<?php echo CLOUDFRONT_ENDPOINT.$video['videoFile']; ?>" class="open-overlay" id="download-video-" download>Download video</a></button>
		</div>
		<div class="new-video-CTA">
			<button class="cta-orange"><a href="#new-video" class="open-overlay" id="new-video-">Make a new video</a></button>
		</div>
	</div>
</div>
<div id="email-video-overlay">
	<div class="back" id="email-video"><span>< BACK</span></div>
	<div class="row first" style="height: 60%;">
		<p>Receive your video by email</p>
	</div>
	<div class="row" style="height: 40%;">
		<p>Enter your email:</p>
		<input id="email-address" />
		<button class="go-email"><a href="#received-video" class="open-overlay" id="received-video-">Go</a></button>
	</div>
</div>
<div id="new-video-overlay">
	<p style="margin-top: 20px;" class="ios-OS">Your video will be lost if you have no emailed it to yourself already. Are you sure you want to make a new video
	<br>
	<p style="margin-top: 20px;" class="other-OS">Your video will be lost if you have no downloaded it already. Are you sure you want to make a new video?</p>
	
	<button style="margin-top: 20px;"class="cta-orange"><a href="" class="" id="">Go back</a></button>
	<button style="margin-top: 20px;"class="cta-orange"><a href="/videotwo" class="" id="">Yes I'm sure</a></button>
</div>
<div id="received-video-overlay">
	<div class="ios-OS first" style="height: 60%;">
		<p>Video sent! <br /> Check your inbox to view and share.</p>
	</div>
	<div class="other-OS first" style="height: 60%;">
		<p>Video downloaded! <br /> Find it in your downloads folder or inside your favourite photo/video gallery app!</p>
	</div>
	<div class="" style="height: 20%; padding-top: 7%;">
		<button class="cta-green"><a class="open-overlay" href="/videotwo">Make a new video</a></button>
	</div>
	<div class="" style="height: 20%; padding-bottom: 7%;">
		<button class="cta-orange"><a href="" id="what-again">Watch video again</a></button>
	</div>
</div>
<script>
	
</script>