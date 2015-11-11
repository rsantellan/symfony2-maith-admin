function showCalendarExpirationsModal(element)
{
    $.ajax({
      url: $(element).attr('href'),
      dataType: 'json',
      success: function(json){
        if(json.isvalid || json.isvalid == 'true')
        {
          $('#calendarExpirationsModalBody').html(json.rowhtml);
          $('#calendarExpirationsModal').modal('show');
          $('.editable').each(function(index, element){
              $(element).editable($(element).attr('prophref'), {
                  type      : 'textarea',
                  cancel    : 'Cancel',
                  submit    : 'OK',
                  tooltip   : 'Click to edit...'
              });
          });
        }
        else
        {
          toastr.error(json.message);
        }
      }, 
      complete: function()
      {
      }
  });

  return false; 
}

function sendRemoveContactTypeModal(element, confirmationText){
	  if(confirm(confirmationText))
	  {
		  $.ajax({
			url: $(element).attr('href'),
			dataType: 'json',
			success: function(json){
			  if(json.isvalid || json.isvalid == 'true')
			  {
				$('#show_contact_type_id_' + json.contactId + '_' + json.typeId).remove();
				toastr.info(json.message);
			  }
			  else
			  {
				toastr.error(json.message);
			  }
			  
			}, 
			complete: function()
			{
			}
		  });
	  }
      return false;     
}

function sendAddEditContactTypeModal(element){
  $.ajax({
      url: $(element).attr('href'),
      success: function(data){
        $('#addContactModalBody').html(data);
        $('#addEditContactModal').modal('show');
      }, 
      complete: function()
      {
      }
  });

  return false;  
}

function saveAddContactTypeToClientForm(form)
{
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          console.log(json);
          if(json.isvalid || json.isvalid == 'true')
          {
            console.log($('#contactsTypestable > tbody:last'));
            console.log(json.rowhtml);
            $('#contactsTypestable > tbody:last').append(json.rowhtml);
          }
          $('#addEditContactModal').modal('hide');
          toastr.info(json.message);
      }
      , 
      complete: function()
      {
      }
  });
  return false;   
}


function sendAddEditContactModal(element){
      $.ajax({
          url: $(element).attr('href'),
          success: function(data){
            $('#addContactModalBody').html(data);
            $('#addEditContactModal').modal('show');
          }, 
          complete: function()
          {
          }
      });

      return false;     
}  

function saveNewExistingCalendarForm(form)
{
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          if(json.isvalid || json.isvalid == 'true')
          {
            //console.log(json.rowhtml);
            $('#contactstable > tbody:last').append(json.rowhtml);
          }
		  $('#addEditContactModal').modal('hide');
          toastr.info(json.message);
      }
      , 
      complete: function()
      {
      }
  });
  return false;   
}

function saveNewContactForm(form)
{
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          console.log(json);
          if(json.isvalid || json.isvalid == 'true')
          {
            console.log($('#contactstable > tbody:last'));
            console.log(json.rowhtml);
            $('#contactstable > tbody:last').append(json.rowhtml);
          }
          $('#addEditContactModal').modal('hide');
          toastr.info(json.message);
      }
      , 
      complete: function()
      {
      }
  });
  return false;   
}

function sendRemoveContactModal(element, confirmationText){
	  if(confirm(confirmationText))
	  {
		  $.ajax({
			url: $(element).attr('href'),
			dataType: 'json',
			success: function(json){
			  if(json.isvalid || json.isvalid == 'true')
			  {
				$('#show_contact_id_' + json.contactId + '_' + json.clientId).remove();
				toastr.info(json.message);
			  }
			  else
			  {
				toastr.error(json.message);
			  }
			  
			}, 
			complete: function()
			{
			}
		  });
	  }
      return false;     
}

function sendAddCalendarModal(element){
      $.ajax({
          url: $(element).attr('href'),
          success: function(data){
            $('#addCalendarModalBody').html(data);
            $('#addCalendarModal').modal('show');
          }, 
          complete: function()
          {
          }
      });

      return false;     
}

function sendRemoveCalendarModal(element, confirmationText){
	  if(confirm(confirmationText))
	  {
		  $.ajax({
			url: $(element).attr('href'),
			dataType: 'json',
			success: function(json){
			  if(json.isvalid || json.isvalid == 'true')
			  {
				$('#show_calendar_id_' + json.calendarid).remove();
				toastr.info(json.message);
			  }
			  else
			  {
				toastr.error(json.message);
			  }
			  
			}, 
			complete: function()
			{
			}
		  });
	  }
      return false;     
}

function saveNewCalendarForm(form)
{
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          if(json.isvalid || json.isvalid == 'true')
          {
            $('#show_calendar_id_' + json.calendarid).remove();
            $('#calendarstable > tbody:last').append(json.rowhtml);
            $('#addCalendarModal').modal('hide');
          }
          else
          {
            $('#addCalendarModalBody').html(json.formhtml);
          }
      }
      , 
      complete: function()
      {
      }
  });
  return false;   
}

function changeContactNote(element, description)
{
	$.ajax({
	  url: $(element).attr('href'),
	  dataType: 'json',
	  type: 'post',
	  success: function(json){
		$('#addEditContactNoteModalBody').html(json.rowhtml);
		$('#addEditContactNoteModal').modal('show');
	  }, 
	  complete: function()
	  {
	  }
  });

  return false;
}

function doSaveContactNote(form)
{
  $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          if(json.isvalid || json.isvalid == 'true')
          {
			$('#show_contact_id_'+json.contactId+'_'+json.clientId).replaceWith(json.rowhtml);
          }
          toastr.info(json.message);
      }
      , 
      complete: function()
      {
		$('#addEditContactNoteModal').modal('hide');
      }
  });
  return false;
}