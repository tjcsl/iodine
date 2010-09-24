<a href="[<$I2_ROOT>]podcasts">Podcasts Home</a><br /><br />
[<if isset($deleted)>]
 "[<$podcastname>]" has been deleted. Returning to Podcasts Home.
 <meta HTTP-EQUIV="REFRESH" content="3;url=[<$I2_ROOT>]podcasts">
[<else>]
 You are about to delete "[<$podcastname>]". Are you really, entirely sure about this?
 <form method="post" action="" class="boxform">
 <input type="hidden" name="podcasts_delete_form" value="delete_podcast" />
 <input type="submit" value="Delete" name="submit" />
 </form>
[</if>]
