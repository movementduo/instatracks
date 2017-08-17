<div id="select-pics">
	<div class="back" id="video-two.html"><span>< BACK</span></div>
	<div id="manual-header">
		<figure id="profile_picture"><img src="<?php echo $user['profile_picture']; ?>" width="100%" height="100%" /></figure>
		<h2><?php echo $user['username'];?></h2>
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
			<button id="cta-submit" class="cta-green cta-small"><input type="button" onclick="check_selected()" id="submit" value="GO"></button>
			<button id="cta-reset" class="cta-orange cta-small"><a href="#reset-pics" id="reset-pics">Reset</a></button>
		</div>
	</form>

</div>
<script>

function check_selected() {
	  var check = document.getElementsByName("url");

        var checkboxesChecked = [];
        // loop over them all
        for (var i=0; i<check.length; i++) {
         // And stick the checked ones onto an array...
         if (check[i].checked) {
            checkboxesChecked.push(check[i].getElementById());
         }
        }
        console.log(checkboxesChecked);
	}

	$('#reset-pics').on('click', uncheckAll)

	function uncheckAll() {
	  $("input[type='checkbox'][id^='img-']:checked").prop("checked", false)
	}
</script>
