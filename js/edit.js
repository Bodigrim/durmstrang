$(function(){
	$('#delete-form').submit(function(){
		return confirm("Вы уверены, что хотите удалить заявку безвозвратно?");
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
			$('#messages').append("<b>Новое сообщение:</b><br />" + message + "<hr />");
		});

		return false;
	});
});
