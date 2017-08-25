<div id="manual-select">
	<div class="back" id=""><a href="videotwo"><span>< BACK</span></a></div>
	<div id="manual-header">
		<figure id="profile_picture"><img src="<?php echo $user['profile_picture']; ?>" width="100%" height="100%" /></figure>
		<h2><?php echo $user['username'];?></h2>
	</div>
	<button id="cta-reset" class="cta-orange cta-small"><a href="#reset-pics" id="reset-pics">Reset</a></button>
	<form id="manual-form" method="post" action="/select">
		<div id="popular-posts">
			<p style="margin-top: 15px; margin-bottom: 5px;">Top posts</p>
			<div class="square-grid">
				<?php foreach($popular as $key => $image) { ?>
					<div class="col-xs-4" style="padding: 0;">
						<input id="<?php echo 'img-'.$image['id'].'-popular'; ?>" class="checkbox" type="checkbox" name="url[<?php echo $image['id']; ?>]" value="1">
						<label for="<?php echo 'img-'.$image['id'].'-popular'; ?>">
							<img src="<?php echo $image['thumbnailURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" />
						</label>
					</div>
				<? } ?>
			</div>
		</div>
		<div id="recent-posts">
			<p style="margin-top: 10px; margin-bottom: 10px;">Most recent</p>
			<div class="square-grid">
				<?php foreach($recent as $key => $image) { ?>
					<div class="col-xs-4" style="padding: 0;">
						<input id="<?php echo 'img-'.$image['id'].'-recent'; ?>" class="checkbox" type="checkbox" name="url[<?php echo $image['id']; ?>]" value="1">
						<label for="<?php echo 'img-'.$image['id'].'-recent'; ?>" style="height: 33.33vw">
							<img src="<?php echo $image['thumbnailURL']; ?>" data-id="<?php echo $image['id']; ?>" width="100%" height="100%" />
						</label>
					</div>
				<? } ?>
			</div>
		</div>
		<button id="cta-submit" class="cta-green cta-small"><input type="button" type="submit" value="GO"></button>
		<div id="manual-error-overlay">
			<button id="cta-reset" class="cta-green"><a href="" id="">Ok got it</a></button>
		</div>
	</form>

</div>
<script>

	$( document ).ready(function() {

		$('#manual-form').submit(function(){

			if($('#manual-form').serializeArray().length < 4){
				$('#manual-error-overlay').css('display', 'block');
			} else {
				$.get('/ajax?action=select',$('#manual-form').serializeArray(),function(resp){
		  		if(resp == 'true') {
		  			window.location.href = '/loading';
		  		}
		  	});
			}
	  	
	  	return false;
	  });

		$('input.checkbox').on('change', function(evt) {

			var image_selected = $("input[value='1']:checked").length;

			if(image_selected > 6) {
				this.checked = false;
			} 

			if(image_selected < 4) {

			  $('#cta-submit.cta-green input').css('opacity', '0.3');
			  $('#cta-submit.cta-green ').css('opacity', '0.8');
			  $('#cta-submit.cta-green').css('background', 'darkgrey');

			} else {

			  $('#cta-submit.cta-green input').css('opacity', '1');
			  $('#cta-submit.cta-green ').css('opacity', '1');
			  $('#cta-submit.cta-green').css('background', '#99bc1f');

			}

	  });

	  function uncheckAll() {
		  $("input[type='checkbox'][id^='img-']:checked").prop("checked", false)
		}

		$('#reset-pics').on('click', uncheckAll);

	});

</script>
