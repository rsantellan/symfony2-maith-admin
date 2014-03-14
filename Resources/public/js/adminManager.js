adminManager = function(options){
	this._initialize();

}

adminManager.instance = null;

adminManager.getInstance = function (){
	if(adminManager.instance == null)
		adminManager.instance = new adminManager();
	return adminManager.instance;
}

adminManager.prototype = {
    _initialize: function(){
        
    },
    
    deleteTableRow: function(itemId, text, mUrl)
    {
      if(confirm(text))
      {
        $.ajax({
          url: mUrl,
          data: {id: itemId},
          type: 'post',
          dataType: 'json',
          complete: function(json)
          {
            var obj = jQuery.parseJSON(json.responseText);
            //console.info(obj);
            if(obj.response == "OK")
            {
              //console.info($('#table_row_' + itemId))
              $('#table_row_' + itemId).fadeOut(500, function() { $(this).remove(); });
            }
          }        
        });
      }
      return false;
    },
    
    startFancyInPage: function(fancy_class)
    {
      $("."+fancy_class).fancybox({});
    },
    
    startSortable: function(sortable_id, sortable_url)
    {
      $("#"+sortable_id).sortable(
      {
        axis: 'y', 
        update : function () { 
          var order = $('#'+sortable_id).sortable('serialize'); 
          parent.$.fancybox.showActivity();
          $.ajax({
            url: sortable_url,
            data: order,
            type: 'post',
            dataType: 'json',
            complete: function()
            {
                parent.$.fancybox.hideActivity();
                parent.$.fancybox.resize();
            }        
          });

        }
      });
    },
    
    startBasicTextAreasTinyMCE: function()
    {
      tinyMCE.init({

          // General options

          mode : "textareas",

          theme : "advanced",

          plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist, spellchecker",

          // Theme options
          theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|",
          theme_advanced_buttons2 : "bullist,numlist,|,link,unlink,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|fullscreen,|,cut,copy,paste,pastetext,pasteword,|",
          theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid",

          theme_advanced_toolbar_location : "top",

          theme_advanced_toolbar_align : "left",

          theme_advanced_statusbar_location : "bottom",

          theme_advanced_resizing : true,

          forced_root_block : "",

          force_br_newlines : true,
          force_p_newlines : false

      });
    }
}

/**
 * Esta funcion es la mas basica de los fancy. Hace que todo lo que se .fancy_link se inizialice
 */
function startFancyLinks()
{
  adminManager.getInstance().startFancyInPage('fancy_link');
}