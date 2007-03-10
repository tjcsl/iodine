<a href="[<$I2_ROOT>]polls/admin">Polls Admin</a><br /><br />

<b>[<$pollname>]</b><br /><br />

[<$question>]<br /><br />

[<foreach from=$votes item=vote>]
 [<$vote.uid>]:<br />[<$vote.vote>]<br /><br />
[</foreach>]
