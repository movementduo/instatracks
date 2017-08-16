<div id="select-pics">
	<div class="back" id="video-two.html"><span>< BACK</span></div>
	<div class="user" style="height: 25vh;">
		<figure class="profile-pic"><img src="<?php echo $instance['profile_picture']; ?>" width="100%" height="100%" /></figure>
		<h2><?php echo $instance['username']; ?></h2>
		<div class="row">
			<button class="cta-orange cta-small"><a href="#reset-pics" id="reset-pics">Reset</a></button>
			<button class="cta-green cta-small"><a href="go-pics" id="go-pics">Go</a></button>
		</div>
	</div>
	<div class="top-pics">
		<h3>Top posts</h3>
		<div class="square-grid">
<?php foreach($popular as $image) { ?>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33vw;"><img src="<?php echo $image['cdnURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" /></figure>
			</div>
<? } ?>
		</div>
	</div>
	<div class="most-recent">
		<h3>Most recent</h3>
		<div class="square-grid">
<?php foreach($recent as $image) { ?>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33vw;"><img src="<?php echo $image['cdnURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" /></figure>
			</div>
<? } ?>
		</div>
	</div>
</div>
