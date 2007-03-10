<a href="[<$I2_ROOT>]polls/edit/[<$pid>]">Edit Poll</a><br /><br />

<form method="post" action="[<$I2_ROOT>]polls/add/[<$pid>]/" class="boxform">
<input type="hidden" name="poll_add_form" value="question" />
Question: <input type="text" name="question" value="" /><br />
Type: <select name=answertype> 
	<option> Checkbox 
	<option> Radio
	<option value='freeresponse'> Free Response 
	</select><br />
<!-- Max Votes (0 for unlimited): <input type="text" name="maxvotes" value="" /><br /> 
<textarea rows="5" cols="50" name="answers"></textarea><br /> -->
<input type="submit" value="Create" name="submit">
</form>
