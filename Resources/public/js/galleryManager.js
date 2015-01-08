galleryManager = function(options){
	this._initialize();

}

galleryManager.instance = null;

galleryManager.getInstance = function (){
	if(galleryManager.instance == null)
		galleryManager.instance = new galleryManager();
	return galleryManager.instance;
}

galleryManager.prototype = {
    _initialize: function(){
        
    },
    
    hoverImages: function(){
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
    
    initializeAlbumUploaderBox: function()
    {
      $(".album_uploader_link").colorbox({iframe: true, width: "80%", height: "80%"});
    },
    
    refreshGallery: function(gallery)
    {
      $.ajax({
          url: $("#gallery_refresh_url").val(),
          data: {'gallery': gallery},
          type: 'post',
          dataType: 'json',
          success: function(json){
              if(json.status == "OK")
              {
                $("#gallery_" + gallery + ' .images').html(json.options.html);
                galleryManager.getInstance().hoverImages();
              }
          }
      });

      return false; 
    },
    
    removeImage: function(mUrl, confirmationText, element)
    {
      /*
      console.log(element);
      console.log($(element));
      console.log($(element).parent());
      console.log($(element).parent().parent());
      */
      
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
              //$('#file_container_' + itemId).fadeOut(500, function() {$(this).remove();});
            }
          }        
        });
      }
      
    }
}

$( document ).ready(function() {
    galleryManager.getInstance().hoverImages();
    galleryManager.getInstance().initializeAlbumUploaderBox();
});