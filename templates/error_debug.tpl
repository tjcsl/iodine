[<if $errors>]
 <div class="error" [<if $debug>]style="width:45%;"[<else>]style="width:90%;"[</if>]>
  Iodine has encountered the following errors: <br /><br />
  [<$errors>]
 </div>
[</if>]
[<if $debug>]
 <div class="debug" [<if $errors>]style="width:45%;"[<else>]style="width:90%;"[</if>]>
  Debug messages: <br /><br />
  [<$debug>]
 </div>
[</if>]
