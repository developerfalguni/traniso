<?php  echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

<input type="text" name="asdf" class="form-control form-control-sm" />

<table class="table table-bordered table-condensed DataEntry">
<thead>
	<tr>
		<th>Text</th>
		<th>Select</th>
		<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><input type="text" class="form-control form-control-sm tags" /></td>
		<td>
			<select name="drop">
				<option value="chetan">chetan</option>
				<option value="ravi">ravi</option>
				<option value="vishal">vishal</option>
			</select>
		</td>
		<td class="aligncenter"><input type="checkbox"></td>
	</tr>

	<tr class="TemplateRow">
		<td><input type="text" class="form-control form-control-sm tags Validate" /></td>
		<td>
			<select name="drop">
				<option value="chetan">chetan</option>
				<option value="ravi">ravi</option>
				<option value="vishal">vishal</option>
			</select>
		</td>
		<td class="aligncenter"><button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
	</tr>
</tbody>
</table>

<table class="table table-bordered table-condensed DataEntry">
<thead>
	<tr>
		<th>Text</th>
		<th>Select</th>
		<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><input type="text" class="form-control form-control-sm tags" /></td>
		<td>
			<select name="drop">
				<option value="chetan">chetan</option>
				<option value="ravi">ravi</option>
				<option value="vishal">vishal</option>
			</select>
		</td>
		<td class="aligncenter"><input type="checkbox"></td>
	</tr>
	<tr class="TemplateRow">
		<td><input type="text" class="form-control form-control-sm tags" /></td>
		<td>
			<select name="drop" multiple class="SelectizeKaabar">
				<option value="chetan">chetan</option>
				<option value="ravi">ravi</option>
				<option value="vishal">vishal</option>
			</select>
		</td>
		<td class="aligncenter"><button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
	</tr>
</tbody>
</table>

<button type="submit" id="Update">Update</button>
</form>
<script>
$(document).ready(function() {
	var availableTags = [
	"ActionScript",
	"AppleScript",
	"Asp",
	"BASIC",
	"C",
	"C++",
	"Clojure",
	"COBOL",
	"ColdFusion",
	"Erlang",
	"Fortran",
	"Groovy",
	"Haskell",
	"Java",
	"JavaScript",
	"Lisp",
	"Perl",
	"PHP",
	"Python",
	"Ruby",
	"Scala",
	"Scheme"
	];

	$('.tags').on('keydown.autocomplete', function() {
		$(this).autocomplete({
		source: availableTags
		});
	});
});
</script>