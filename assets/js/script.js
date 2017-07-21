$( document ).ready(function() {
	
	$('.open-overlay').click(function(){
		let id0 = $(this)[0].id;
		let id = id0.substring(0, id0.length - 1);
		console.log(id0, id);
		$('#'+id+'-overlay').css('display', 'block');
	})
	
	$('.back').click(function(){
		let id = $(this)[0].id;
		$('#'+id+'-overlay').css('display', 'none');
	})
	
});