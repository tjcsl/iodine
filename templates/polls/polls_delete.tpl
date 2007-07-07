<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />
[<if isset($deleted)>]
 The poll has been deleted.
[<else>]
 You are about to delete a poll. Are you really, entirely sure about this?
 <form method="post" action="" class="boxform">
 <input type="hidden" name="polls_delete_form" value="delete_poll" />
 <input type="submit" value="Delete" name="submit" />
 </form>
[</if>]
