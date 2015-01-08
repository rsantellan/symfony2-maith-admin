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
    /*
    initializeAlbumSortableBox: function()
    {
      $(".album_sortable_link").each(function(){
        var object = $(this);
        object.colorbox({
          iframe: true, 
          width: "80%", 
          height: "80%", 
          onClosed:function(){ 
            galleryManager.getInstance().refreshAlbums(object.attr("albumId"));
          }
        });
      });
    },
    */
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
    
    removeImage: function(mUrl, confirmationText, itemId)
    {
      if(confirm(confirmationText))
      {
        $.ajax({
          url: mUrl,
          type: 'post',
          dataType: 'json',
          success: function(json)
          {
            if(json.status == "OK")
            {
              $('#file_container_' + itemId).fadeOut(500, function() {$(this).remove();});
            }
          }        
        });
      }
      
    }
    /*
    ,
    createColorboxMiniatureFrames: function()
    {
      $('.img_edit a').colorbox();
    },
    
    saveFileEditForm: function(form)
    {
      $("#saving_image_admin_file").show();
      $(".admin_file_action_button").hide();
      $.ajax({
          url: $(form).attr('action'),
          data: $(form).serialize(),
          type: 'post',
          dataType: 'json',
          success: function(json){
              if(json.result == "false" || json.result == false)
              {
                $("#form_errors_admin_file").html(json.errors);
                console.log(json.errors);
              }
              else
              {
                $("#form_errors_admin_file").html(" ");
              }
          }
          , 
          complete: function()
          {
            $.colorbox.resize();
            $("#saving_image_admin_file").hide();
            $(".admin_file_action_button").show();
          }
      });
      return false; 
    }
    */
}

$( document ).ready(function() {
    galleryManager.getInstance().hoverImages();
    galleryManager.getInstance().initializeAlbumUploaderBox();
    //galleryManager.getInstance().initializeAlbumSortableBox();
    //galleryManager.getInstance().createColorboxMiniatureFrames();
});