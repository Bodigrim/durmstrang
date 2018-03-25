var byId = function (id) { return document.getElementById(id); };

var validate = function(el){
  var capacity = $(el).attr("data-capacity");
  var inhabitants = $(el).find(".inhabitant").length;
  if(inhabitants > capacity){
    $(el).addClass("tile__list__red");
  }
  else {
    $(el).removeClass("tile__list__red");
  }
};

[].forEach.call(byId('multi').getElementsByClassName('tile__list'), function (el){
  Sortable.create(el, {
    group: 'photo',
    animation: 150,
    onSort: function(evt){
      validate(evt.from);
      validate(evt.to);
    }
  });
});

$('#save-button').click(function(){
  var payload = [];
  $('.house').each(function(ix1, house){
    var houseid = $(house).attr("data-houseid");
    $(house).find(".inhabitant").each(function(ix2, inhabitant){
      var inhabitantid = $(inhabitant).attr("data-inhabitantid");
      payload[inhabitantid] = houseid;
    });
  });
  $.post("/actions/houses-save.php", {payload : payload});

  return false;
});
