$(function(){

	$('.status-selector').change(function(){
		var hash = {
			email: $(this).attr("data-email"),
			status: $(this).val()
		};
		$(this).parents("td").attr("data-value", $(this).val());
		$.post("/actions/update-status.php", hash);
	});

});
