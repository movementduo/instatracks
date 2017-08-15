<div id="text" style="color: black;"></div>
<div id="loading" style="height: 100%;">
	<div class="container-fluid" style="height: 100%;">
		<div class="row" style="height: 100vw;">
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
			<div class="col-xs-4" style="padding: 0;">
				<figure style="height: 33.33%;"><img src="" /></figure>
			</div>
		</div>
		<div class="row" style="height: calc((100% - 100vw) / 4);">
			<a href="<?php echo $link; ?>"><h1>Gathering pics...</h1></a>
		</div>
		<div class="row" style="height: calc(3 * (100% - 100vw) / 4);"></div>
	</div>
</div>

<script type="text/javascript">

	var state;
	
	$(document).ready(function() {
		state = setInterval(getState,500);
	});
	
	function getState() {
		$.get('/ajax?action=status',function(ret) {
			d = new Date();
			$('#text').html(d+': '+ret.state);
			if(ret.state == 'complete') {
				clearInterval(state);
				window.location.href = ret.share;
			}
		},'json');
	}


</script>