$(document).ready(function(){
   $('#table_data').dataTable({
        "oLanguage": {
			"sUrl": $("#jquery-datatable-langs").val()
		}
    });
    
});