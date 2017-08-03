<div id="video-one" style="height: 100%;">
	<div id="video-player">
		<video width="100%" height="100%" controls>
			<source src="<?php echo CLOUDFRONT_ENDPOINT.$video['videoFile']; ?>" stype="video/mp4">
		</video>
	</div>
	<!-- IOS -->
	<div class="row" style="height: 20%; padding-top:7%">
		<button class="cta-orange"><a href="#email-video" class="open-overlay" id="email-video-">Email video</a></button>
	</div>
	<div class="row" style="height: 20%; padding-bottom:7%">
		<button class="cta-orange"><a href="#new-video" class="open-overlay" id="new-video-">Make a new video</a></button>
	</div>
	<!-- Andriod -->
	<!-- <div class="row" style="height: calc((100% - 100vw) / 2);">
		<button class="cta-orange"><a href="#download-video" class="open-overlay" id="download-video-">Download video</a></button>
	</div>
	<div class="row" style="height: calc((100% - 100vw) / 2);">
		<button class="cta-orange"><a href="#new-video" class="open-overlay" id="new-video-">Make a new video</a></button>
	</div> -->
</div>
<div id="email-video-overlay">
	<div class="back" id="email-video"><span>< BACK</span></div>
	<div class="row first" style="height: 60%;">
		<p>Receive your video by email</p>
	</div>
	<div class="row" style="height: 40%;">
		<p>Enter your email:</p>
		<input id="email-address" />
		<button class="go-email"><a href="<?php echo $link; ?>" id="send-email">Go</a></button>
	</div>
</div>
<div id="new-video-overlay">
	<div class="back" id="new-video"><span>< BACK</span></div>
</div>aaa