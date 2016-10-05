String.prototype.nl2br = function(){
  return this.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
}

$(function(){
	$('#delete-btn').click(function(){
		if(confirm("Вы уверены, что хотите удалить заявку безвозвратно?")){
			$.post
				( "/actions/delete.php"
				, { email: $(this).attr("data-email") }
				, function(){ location = "/"; }
				)
		}
		return false;
	});

	$('#send-message').click(function(){
		var message = $('#message').val();
		var email   = $('#email').val();

		if(!message){
			alert("Нельзя отправить пустое сообщение!");
			return false;
		}

		var hash = {
			message: message,
			email: email
		};
		$.post("/actions/send-message.php", hash, function(){
			$('#message').val("");
			$('#messages').append("<b>Новое сообщение:</b><br />" + message.nl2br() + "<hr />");
		});

		return false;
	});

	$('#settlement-self').change(function(){
		$('#group-name').attr("disabled", true);
		$('#group-id').attr("disabled", true);
	})

	$('#settlement-owner').change(function(){
		$('#group-name').attr("disabled", false);
		$('#group-id').attr("disabled", true);
	})

	$('#settlement-member').change(function(){
		$('#group-name').attr("disabled", true);
		$('#group-id').attr("disabled", false);
	})

});
