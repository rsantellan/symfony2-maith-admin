wyswygManager = function(options){
	this._initialize();
}

wyswygManager.instance = null;

wyswygManager.getInstance = function (){
	if(wyswygManager.instance == null)
		wyswygManager.instance = new wyswygManager();
	return wyswygManager.instance;
}

wyswygManager.prototype = {
    _initialize: function(){
        
    },
    initializeAlbumUploaderBox: function()
    {
      $(".album_uploader_link").colorbox({iframe: true, width: "80%", height: "80%"});
    },
    addNewFolder: function(form){
  	  $.ajax({
  	      url: $(form).attr('action'),
  	      data: $(form).serialize(),
  	      type: 'post',
  	      dataType: 'json',
  	      success: function(json){
  	          if(!json.isvalid || json.isvalid == 'false')
  	          {
  	            $('#wyswygModalBody').html(json.html);
  	          }
  	          if(json.reload || json.reload == 'true'){
  	          	 window.location.reload(true);
  	          }
  	      }
  	      , 
  	      complete: function()
  	      {
  	      }
  	  });
  	  return false;   
    },

    hoverImages: function(){
      $('div.img_edit').hide();
      $('div.img_delete').hide();
      $('.img_thumb_container').each(function(index, value) {
        $(this).hover(function(){
          $(this).find('div.img_edit').show();
          $(this).find('div.img_delete').show();
        },
        function(){
          $(this).find('div.img_edit').hide();
          $(this).find('div.img_delete').hide();
        });
      });
    },

    refreshFolder: function(folder)
    {
      $.ajax({
          url: $("#folder_refresh_url").val(),
          data: {'folder': folder},
          type: 'post',
          dataType: 'json',
          success: function(json){
              if(json.status == "OK")
              {
                $("#folder_" + folder + ' .images').html(json.options.html);
                wyswygManager.getInstance().hoverImages();
              }
          }
      });

      return false; 
    },

    removeImage: function(mUrl, confirmationText, element)
    {
      if(confirm(confirmationText))
      {
        $.ajax({
          url: mUrl,
          type: 'post',
          dataType: 'json',
          success: function(json)
          {
            if(json.result == "true" || json.result == true)
            {
              $(element).parent().parent().parent().fadeOut(500, function(){
                $(this).remove();
              });
            }
          }        
        });
      }
    },
    showModal: function(element){
      $.ajax({
        url: $(element).attr('href'),
        success: function(json){
            if(json.isvalid || json.isvalid == 'true'){
              $('#wyswygModalBody').html(json.html);
              $('#wyswygModal').modal('show');
            }
        }, 
        complete: function()
        {

        }
      });
      return false;
    },

    generateImageUrl: function(form){
      $.ajax({
          url: $(form).attr('action'),
          data: $(form).serialize(),
          type: 'post',
          dataType: 'json',
          success: function(json){
              if(!json.isvalid || json.isvalid == 'false')
              {
                $('#wyswygModalBody').html(json.html);
              }
              if(json.close || json.close == 'true'){
                window.opener.CKEDITOR.tools.callFunction( $('#used_ckeditor_number').val(), json.url );
                window.close();
              }
          }
          , 
          complete: function()
          {
          }
      });
      return false;   
    },
};

$( document ).ready(function() {
	wyswygManager.getInstance().hoverImages();
	wyswygManager.getInstance().initializeAlbumUploaderBox();
});
