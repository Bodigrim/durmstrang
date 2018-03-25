String.prototype.nl2br = function(){
  return this.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
}

$(function(){
	$('#delete-btn').click(function(){
		if(confirm("Вы уверены, что хотите удалить текст безвозвратно?")){
			$.post
				( "/actions/text-delete.php"
				, { id: $(this).attr("data-id") }
				, function(){ location = "/texts.php"; }
				)
		}
		return false;
	});

});
