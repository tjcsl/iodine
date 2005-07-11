This is the news Smarty pane.<BR />
[<foreach from=$news_stories item=story>]
<b>[<$story.title>]</b><BR />
[<$story.text>]<BR />
<em>Posted by [<$story.author>] at [<$story.posted|date_format:"%l:%M %p on %a %B %e, %Y">]</em>
<HR />
[</foreach>]
