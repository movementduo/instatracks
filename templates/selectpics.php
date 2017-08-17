<div id="select-pics">
	<div class="back" id="video-two.html"><span>< BACK</span></div>
	<div id="manual-header">
		<figure id="profile_picture"><img src="<?php echo $user['profile_picture']; ?>" width="100%" height="100%" /></figure>
		<h2><?php echo $user['username'];?></h2>
		<div class="row">
			<button class="cta-orange cta-small"><a href="#reset-pics" id="reset-pics">Reset</a></button>
			<!-- <button class="cta-green cta-small"><a href="go-pics" id="go-pics">Go</a></button> -->
		</div>
	</div>
	<form id="manual-form" method="post" action="/somewhere">
		<div id="popular-posts">
			<h3>Top posts</h3>
			<div class="square-grid">
				<?php foreach($popular as $image) { ?>
					<div class="col-xs-4" style="padding: 0;">
						<input id="<?php echo 'img-'.$image['id']; ?>" class="checkbox" type="checkbox" name="url" value=$image>
						<label for="<?php echo 'img-'.$image['id']; ?>" style="height: 33.33vw">
							<img src="<?php echo $image['cdnURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" />
						</label>
					</div>
				<? } ?>
			</div>
		</div>
		<div id="recent-posts">
			<h3>Most recent</h3>
			<div class="square-grid">
				<?php foreach($recent as $image) { ?>
					<div class="col-xs-4" style="padding: 0;">
						<input id="<?php echo 'img-'.$image['id']; ?>" class="checkbox" type="checkbox" name="url" value=$image>
						<label for="<?php echo 'img-'.$image['id']; ?>" style="height: 33.33vw">
							<img src="<?php echo $image['cdnURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" />
						</label>
					</div>
				<? } ?>
			</div>
			<button class="cta-green cta-small"><input type="submit" id="submit" value="GO"></button>
		</div>
	</form>

</div>
