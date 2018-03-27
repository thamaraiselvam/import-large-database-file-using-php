<!doctype html>
<html lang="en">
<head>
  <title>Import Large database</title>
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</head>

<div class="container" style="margin-top: 100px">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Import database</h3>
				</div>
				<div class="panel-body">
					<form accept-charset="UTF-8" role="form">
							<fieldset>
						<input type="submit" name="start" id="start_import" class="btn btn-lg btn-success btn-block" type="submit" value="Start Importing">
					</fieldset>
					</form>
					<br>
					<h4 id="result"></h4>
				</div>
			</div>
		</div>
	</div>
</div>

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
		data = get_message_in_between(data);
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

function get_message_in_between(response_str){
	var start_str = '<LOTUS_START>';
	var start_str_len = start_str.length;
	var end_str = '<LOTUS_END>';
	var end_str_len = end_str.length;

	if(response_str.indexOf(start_str) === false){
		return false;
	}

	var start_str_full_pos = response_str.indexOf(start_str) + start_str_len;
	var in_between = response_str.substr(start_str_full_pos);

	var end_str_full_pos = in_between.indexOf(end_str);
	in_between = in_between.substr(0, end_str_full_pos);

	return in_between;
}
</script>

</body>
</html>