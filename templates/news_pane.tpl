This is the news Smarty pane.<BR />
[<foreach from=$news_stories item=story>]
[<foreach from=$story key=column item=val>]
[<$val>]&nbsp;
[</foreach>]
<BR />
[</foreach>]
