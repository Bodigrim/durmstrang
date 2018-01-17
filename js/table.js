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

  $('.payment-room-checkbox').change(function(){
    var hash = {
      email: $(this).attr("data-email"),
      payment: +$(this).prop("checked")
    };
    $(this).parents("td").attr("data-value", +$(this).prop("checked"));
    $.post("/actions/update-payment-room.php", hash);
  });

  $('.payment-food-checkbox').change(function(){
    var hash = {
      email: $(this).attr("data-email"),
      payment: +$(this).prop("checked")
    };
    $(this).parents("td").attr("data-value", +$(this).prop("checked"));
    $.post("/actions/update-payment-food.php", hash);
  });

  var toRussianLayout = function(str){
    var dict = {
      "q": "й",
      "w": "ц",
      "e": "у",
      "r": "к",
      "t": "е",
      "y": "н",
      "u": "г",
      "i": "ш",
      "o": "щ",
      "p": "з",
      "[": "х",
      "]": "ъ",
      "{": "х",
      "}": "ъ",
      "a": "ф",
      "s": "ы",
      "d": "в",
      "f": "а",
      "g": "п",
      "h": "р",
      "j": "о",
      "k": "л",
      "l": "д",
      ";": "ж",
      "'": "э",
      ":": "ж",
      "\"": "э",
      "z": "я",
      "x": "ч",
      "c": "с",
      "v": "м",
      "b": "и",
      "n": "т",
      "m": "ь",
      ",": "б",
      ".": "ю",
      "<": "б",
      ">": "ю",
    };
    var ret = "";
    for(var i = 0; i < str.length; i ++){
      var ch = str.charAt(i).toLowerCase();
      ret = ret + (dict[ch] || ch);
    }
    return ret;
  };

  var escapeRegExp = function(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  };

  var omnisearch = function(){
    var keyword = $("#omnibox").val().trim();
    var keywordRu = toRussianLayout(keyword);
    var regexp = new RegExp(escapeRegExp(keyword) + "|" + escapeRegExp(keywordRu), "i");
    $('#users-table tr').each(function(){
      if(regexp.test($(this).html())){
        $(this).show();
      } else {
        $(this).hide();
      }
    });
    $('#users-table thead tr').show();
  }

  $('#omnibox')
    .change(omnisearch)
    .keyup(function(){ return setTimeout(omnisearch, 50); });

});
