<a href="[<$I2_ROOT>]polls">Polls Home</a><br /><br />
[<if isset($deleted)>]
 "[<$pollname>]" has been deleted. Returning to Polls Home.
 <meta HTTP-EQUIV="REFRESH" content="3;url=[<$I2_ROOT>]polls">
[<else>]
 You are about to delete "[<$pollname>]". Are you really, entirely sure about this?
 <form method="post" action="" class="boxform">
 <input type="hidden" name="polls_delete_form" value="delete_poll" />
 <input type="submit" value="Delete" name="submit" />
 </form>
[</if>]
