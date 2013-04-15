$(function(){
  var jcrop_api;
  
  $('<img id="imgToCrop" class="cropbox" src="#" />').insertAfter('#ContentBundle_tutorialbundle_tutorialtype_path');
  $('<div class="clear"></div>').insertAfter('#imgToCrop');

  $('#ContentBundle_tutorialbundle_tutorialtype_path').attr('onChange','readURL(this)');
  $('#tutorial-new').attr('onsubmit','return checkCoords()');
});

function initJcrop(){
  $('.cropbox').Jcrop({
    onSelect: updateCoords,
    aspectRatio: 4/3
  },function(){
    jcrop_api = this;

    jcrop_api.setOptions({
            minSize: [ 330, 210 ],
        maxSize: [ 3300, 2100 ]
        });

        jcrop_api.animateTo([100,100,400,300]);
  });
};

function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {

      $('#imgToCrop').attr('src', e.target.result);

      if (typeof jcrop_api != 'undefined'){
        var src = $("#imgToCrop").attr("src");
        
        jcrop_api.setImage(src,function(){
          this.setOptions({
            minSize: [ 330, 210 ],
            maxSize: [ 3300, 2100 ]
          });
        });

        jcrop_api.animateTo([100,100,400,300]);

      } else {
        initJcrop();
      }

    }

    reader.readAsDataURL(input.files[0]);
  }
}

function updateCoords(c)
{
  $('#x').val(c.x);
  $('#y').val(c.y);
  $('#w').val(c.w);
  $('#h').val(c.h);
};

function checkCoords()
{
  if (parseInt($('#w').val())) return true;
  alert('Please select a crop region then press Create.');
  return false;
};