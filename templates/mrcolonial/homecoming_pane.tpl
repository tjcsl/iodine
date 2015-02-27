[<if isset($admin)>]
 You are a mrcolonial admin. <a href="[<$I2_ROOT>]mrcolonial/admin">View voting results</a><br /><br />
[</if>]
[<if isset($voted_male) && isset($voted_female)>]
 You have voted for <a href="[<$I2_ROOT>]studentdirectory/info/[<$voted_male->uid>]">[<$voted_male->name>]</a> [ <a href="[<$I2_ROOT>]mrcolonial/clearvote/male">clear vote</a> ] and <a href="[<$I2_ROOT>]studentdirectory/info/[<$voted_female->uid>]">[<$voted_female->name>]</a> [ <a href="[<$I2_ROOT>]mrcolonial/clearvote/female">clear vote</a> ].<br />
 <a href="[<$I2_ROOT>]mrcolonial/clearvote/both">Clear both votes</a>
[<elseif isset($voted_male)>]
 You have voted for <a href="[<$I2_ROOT>]studentdirectory/info/[<$voted_male->uid>]">[<$voted_male->name>]</a> [ <a href="[<$I2_ROOT>]mrcolonial/clearvote/male">clear vote</a> ].
[<elseif isset($voted_female)>]
 You have voted for <a href="[<$I2_ROOT>]studentdirectory/info/[<$voted_female->uid>]">[<$voted_female->name>]</a> [ <a href="[<$I2_ROOT>]mrcolonial/clearvote/female">clear vote</a> ].
[<else>]
 You have not voted for anyone.
[</if>]
