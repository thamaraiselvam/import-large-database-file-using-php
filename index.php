<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Import Large database</title>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>

<input type="submit" name="start" id="start_import" >
<!-- the result of the search will be rendered inside this div -->
<div id="result"></div>

<script>
$(document).ready(function($) {
	jQuery("#start_import").on("click", function() {
		import_sql(0);
	});
});

function import_sql(offset){
  $('#start_import').val('Importing...').attr('disabled', 'disabled');
	 // Send the data using post
  var posting = $.post( 'import.php', { offset: offset } );

  // Put the results in a div
  posting.done(function( data ) {
  	try{
  		data = jQuery.parseJSON(data);
		if (data.status === 'error') {
			$('#result').html(data.msg);
		} else if(data.status === 'continue'){
			$('#result').html('Offset :' + data.offset);
			import_sql(data.offset)
		} else if (data.status === 'completed') {
			$('#result').html(data.msg);
		}
  	} catch(err){
  		$('#result').html(data);
  	}
  });

  posting.fail(function(data) {
    $('#result').html('Something went wrong.');
  });
}
</script>

</body>
</html>