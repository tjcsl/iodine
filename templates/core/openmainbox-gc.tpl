</td>
<td class="mod_td" valign=top>
<div class="[<$mainbox_class>]" id="mainbox">
  <div class="boxheader">
  [<if isset($error) && $error>]
   Error
  [<elseif isset($no_module)>]
   No such module: [<$no_module|escape>]
  [<else>]
   [<$title>]
  [</if>]
 </div>
 <div class="boxcontent" id="boxcontent">
 [<if isset($error) && $error>]
  There was an error initializing the module you requested. Look at the error messages below for more information. If you don't see any error messages, contact the intranetmaster about this error.
 [<elseif isset($no_module)>]
  The module `[<$no_module|escape>]` does not exist. Either you mistyped a URL, or you followed a broken link, or Intranet is just broken. Please inform the intranetmaster if the latter is the case.
 [</if>]
