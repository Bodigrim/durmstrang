$(function(){

  $('.status-selector').change(function(){
    var hash = {
      email: $(this).attr("data-email"),
      status: $(this).val()
    };
    $(this).parents("td").attr("data-value", $(this).val());
    $.post("/actions/update-status.php", hash);
  });

  $('.payment-checkbox').change(function(){
    var hash = {
      email: $(this).attr("data-email"),
      payment: +$(this).prop("checked")
    };
    $(this).parents("td").attr("data-value", +$(this).prop("checked"));
    $.post("/actions/update-payment.php", hash);
  });

});
