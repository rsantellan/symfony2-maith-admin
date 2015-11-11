function retrievePreview(element)
{
  $('#folders-list').find('i.text-success').removeClass('text-success').addClass('text-default');
  $.blockUI();
  $.ajax({
      url: $(element).attr('href'),
      cache: false,
      success: function(data){
        $(element).find('i').removeClass('text-default').addClass('text-success');
        $('#email_messages_preview_box').html(data.htmldata);
        $('#email_body_box').html('');
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false;
}

function doSearchOnFolder(form)
{
  $.blockUI();
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(data){
        //$(element).find('i').removeClass('text-default').addClass('text-success');
        $('#email_messages_preview_box').html(data.htmldata);
        $('#email_body_box').html('');
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false;
}

function doChangePageInFolder(url)
{
  $.blockUI();
  $.ajax({
      url: url,
      cache: false,
      success: function(data){
        $('#email_messages_preview_box').html(data.htmldata);
        $('#email_body_box').html('');
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false;
}

function retrieveEmailData(element)
{
  $.blockUI();
  $.ajax({
      url: $(element).attr('href'),
      cache: false,
      success: function(data){
        $('#email_body_box').html(data.htmldata);
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false; 
}

function moveToFolder(element){
  $.blockUI();
  $.ajax({
      url: $(element).attr('href'),
      cache: false,
      success: function(json){
        if(json.isvalid || json.isvalid == 'true')
        {
            toastr.info(json.message);
            $('#message_header_' + json.uid).fadeOut('slow', function(){
              $(this).remove();
              $('#email_body_box').html('');
            });
        }
        else
        {
            toastr.error(json.message);
        }
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false;
}


function removeEmail(url, confirmationText)
{
  if(confirm(confirmationText))
  {
    $.blockUI();
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.isvalid || json.isvalid == 'true')
            {
                toastr.info(json.message);
                $('#message_header_' + json.uid).fadeOut('slow', function(){
                  $(this).remove();
                  $('#email_body_box').html('');
                });
            }
            else
            {
                toastr.error(json.message);
            }
        }
        , 
        complete: function()
        {
          $.unblockUI();
        }
    });  
  }
}

function replyEmail(myUrl)
{
  $.blockUI();
  $.ajax({
      url: myUrl,
      cache: false,
	  type: 'post',
      success: function(data){
        $('#email_body_box').html(data.htmldata);
        setTimeout(function(){
          startComposeJs();
        }, 200);
        
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false; 
  
}

function composeEmail(element)
{
  $.blockUI();
  $.ajax({
      url: $(element).attr('href'),
      cache: false,
      success: function(data){
        $('#email_body_box').html(data.htmldata);
        setTimeout(function(){
          startComposeJs();
        }, 200);
        
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false; 
}

function sendEmail(form)
{
  //Checking the emails
  var myarr = $('#form_to').val().split(",");
  var wrongEmailMessage = '';
  var isEmailWrong = false;
  for (var i = 0; i < myarr.length; i++) {
    //alert(myarr[i]);
    if(!validateEmail(myarr[i]))
    {
      isEmailWrong = true;
      wrongEmailMessage = wrongEmailMessage + ' El email: '+ myarr[i] + ' es incorrecto.<br/>';
    }
  }
  
  myarr = $('#form_cc').val().split(",");
  //console.log(myarr);
  for (var i = 0; i < myarr.length; i++) {
    //alert(myarr[i]);
    if(!validateEmail(myarr[i]))
    {
      isEmailWrong = true;
      wrongEmailMessage = wrongEmailMessage + ' El email: '+ myarr[i] + ' en CC es incorrecto.<br/>';
    }
  }
  
  myarr = $('#form_bcc').val().split(",");
  //console.log(myarr);
  for (var i = 0; i < myarr.length; i++) {
    //alert(myarr[i]);
    if(!validateEmail(myarr[i]))
    {
      isEmailWrong = true;
      wrongEmailMessage = wrongEmailMessage + ' El email: '+ myarr[i] + ' en BCC es incorrecto.<br/>';
    }
  }
  
  if(isEmailWrong)
  {
    toastr.error(wrongEmailMessage);
    return false;
  }
  tinyMCE.triggerSave();
  $.blockUI();
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(data){
        //$(element).find('i').removeClass('text-default').addClass('text-success');
        //$('#email_messages_preview_box').html(data.htmldata);
        //$('#email_body_box').html('');
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false;
}

function validateEmail(email) {
    if(email.length == 0){
      return true;
    }
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function startComposeJs()
{
  $('.tagInput').tagsInput({
    'width':'100%',
    'defaultText':'Direcciones:',
    'autocomplete_url': $('#autocomplete-email-path').val(),
    'minChars' : 2
  });
  startComposerTinyMCE();
}

function startComposerTinyMCE()
{
  console.log('startComposerTinyMCE');
  try{
    tinymce.remove();
  }catch(e)
  {
    console.info('no tiny');
  }
  
  tinyMCE.init({

	  // General options
	  mode : "textareas",
      editor_selector: 'mceEditor',
	  theme: "modern",
	  plugins: [
		  "advlist autolink lists link image charmap print preview hr anchor pagebreak",
		  "searchreplace wordcount visualblocks visualchars code fullscreen",
		  "insertdatetime media nonbreaking save table contextmenu directionality",
		  "emoticons template paste textcolor"
	  ],
	  toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	  toolbar2: "print preview media | forecolor backcolor emoticons",
			image_advtab: true,
	  forced_root_block : "",
	  force_br_newlines : true,
	  force_p_newlines : false

  });  
}

function removeAttachment(element, myUrl)
{
  $.blockUI();
  $.ajax({
      url: myUrl,
      cache: false,
	  type: 'post',
      success: function(data){
        console.log($(element).parent());
        $(element).parent().remove();
        
      }, 
      complete: function()
      {
        $.unblockUI();
      }
  });
  return false; 
}

function showAttachmentModal()
{
  $('#attachmentsModal').modal('show');
  console.info($('#attachment-upload-url').val());
  console.info($('#form_formId').val());
  var uploadUrl = $('#attachment-upload-url').val().replace('zzzz', $('#form_formId').val());
  $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5, html4',
        url : uploadUrl,
        dragdrop: true,
        filters : {
            // Maximum file size
            max_file_size : '5mb'
        },
        
        // PreInit events, bound before the internal events
        preinit : {
            Init: function(up, info) {
                log('[Init]', 'Info:', info, 'Features:', up.features);
            },
 
            UploadFile: function(up, file) {
                log('[UploadFile]', file);
 
                // You can override settings before the file is uploaded
                // up.setOption('url', 'upload.php?id=' + file.id);
                // up.setOption('multipart_params', {param1 : 'value1', param2 : 'value2'});
            }
        },
 
        // Post init events, bound after the internal events
        init : {
			PostInit: function() {
				// Called after initialization is finished and internal event handlers bound
				log('[PostInit]');
				/*
				document.getElementById('uploadfiles').onclick = function() {
					uploader.start();
					return false;
				};
                */
			},

			Browse: function(up) {
                // Called when file picker is clicked
                log('[Browse]');
            },

            Refresh: function(up) {
                // Called when the position or dimensions of the picker change
                log('[Refresh]');
            },
 
            StateChanged: function(up) {
                // Called when the state of the queue is changed
                log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
            },
 
            QueueChanged: function(up) {
                // Called when queue is changed by adding or removing files
                log('[QueueChanged]');
            },

			OptionChanged: function(up, name, value, oldValue) {
				// Called when one of the configuration options is changed
				log('[OptionChanged]', 'Option Name: ', name, 'Value: ', value, 'Old Value: ', oldValue);
			},

			BeforeUpload: function(up, file) {
				// Called right before the upload for a given file starts, can be used to cancel it if required
				log('[BeforeUpload]', 'File: ', file);
			},
 
            UploadProgress: function(up, file) {
                // Called while file is being uploaded
                log('[UploadProgress]', 'File:', file, "Total:", up.total);
            },

			FileFiltered: function(up, file) {
				// Called when file successfully files all the filters
                log('[FileFiltered]', 'File:', file);
			},
 
            FilesAdded: function(up, files) {
                // Called when files are added to queue
                log('[FilesAdded]');
 
                plupload.each(files, function(file) {
                    log('  File:', file);
                });
            },
 
            FilesRemoved: function(up, files) {
                // Called when files are removed from queue
                log('[FilesRemoved]');
 
                plupload.each(files, function(file) {
                    log('  File:', file);
                });
            },
 
            FileUploaded: function(up, file, info) {
                // Called when file has finished uploading
                log('[FileUploaded] File:', file, "Info:", info);
                var myData = jQuery.parseJSON(info.response);
                if(myData.result == true || myData.result == "true")
                {
                  $('#uploaded-file-list-ul').append(myData.html);
                }
            },
 
            ChunkUploaded: function(up, file, info) {
                // Called when file chunk has finished uploading
                log('[ChunkUploaded] File:', file, "Info:", info);
            },

			UploadComplete: function(up, files) {
				// Called when all files are either uploaded or failed
                log('[UploadComplete] File:', files);
			},

			Destroy: function(up) {
				// Called when uploader is destroyed
                log('[Destroy] ');
			},
 
            Error: function(up, args) {
                // Called when error occurs
                log('[Error] ', args);
            }
        }
        
    });
  
}

function log() {
  return false;
  var str = "";
 
        plupload.each(arguments, function(arg) {
            var row = "";
 
            if (typeof(arg) != "string") {
                plupload.each(arg, function(value, key) {
                    // Convert items in File objects to human readable form
                    if (arg instanceof plupload.File) {
                        // Convert status to human readable
                        switch (value) {
                            case plupload.QUEUED:
                                value = 'QUEUED';
                                break;
 
                            case plupload.UPLOADING:
                                value = 'UPLOADING';
                                break;
 
                            case plupload.FAILED:
                                value = 'FAILED';
                                break;
 
                            case plupload.DONE:
                                value = 'DONE';
                                break;
                        }
                    }
 
                    if (typeof(value) != "function") {
                        row += (row ? ', ' : '') + key + '=' + value;
                    }
                });
 
                str += row + " ";
            } else {
                str += arg + " ";
            }
        });
 
    var log = document.getElementById('console');
    log.innerHTML += str + "\n";
}