[<foreach from=$open item=item>]
<a href="[<$I2_ROOT>]polls/vote/[<$item->pid>]">[<$item->name>]<a/><br />
[</foreach>]
<a href="[<$I2_ROOT>]polls/"><i>Polls Home</i></a>
