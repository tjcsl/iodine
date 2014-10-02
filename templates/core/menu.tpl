<div id="menu" onmouseover="menu_onmouseover()" onmouseout="menu_onmouseout()">
  <a id="menu_news" href="[<$I2_ROOT>]news" [<if $I2_MODNAME == "news">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_news','menu_eighth','menu_polls','menu_prefs','menu_suggest','menu_cred','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_news')">News</a>
  <span class="bold">&middot;</span>
  <a id="menu_eighth" 
    [<if $I2_USER->grade != 'staff'>]
      href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$I2_USER->uid>]"
    [<else>]
      href="[<$I2_ROOT>]eighth/vcp_attendance"
    [</if>]
    [<if $I2_MODNAME == "eighth">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_eighth','menu_news','menu_polls','menu_prefs','menu_suggest','menu_cred','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_eighth')">Eighth</a>
  <span class="bold">&middot;</span>
  <a id="menu_polls" href="[<$I2_ROOT>]polls" [<if $I2_MODNAME == "polls">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_polls','menu_news','menu_eighth','menu_prefs','menu_suggest','menu_cred','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_polls')">Polls</a>
  <span class="bold">&middot;</span>
  <a id="menu_prefs" href="[<$I2_ROOT>]prefs" [<if $I2_MODNAME == "prefs">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_prefs','menu_news','menu_eighth','menu_polls','menu_suggest','menu_cred','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_prefs')">Preferences</a>
  <span class="bold">&middot;</span>
  <a id="menu_suggest" href="[<$I2_ROOT>]suggestion" [<if $I2_MODNAME == "suggestion">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_suggest','menu_news','menu_eighth','menu_polls','menu_prefs','menu_cred','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_suggest')">Suggestions</a>
  <span class="bold">&middot;</span>
  <a id="menu_cred" href="[<$I2_ROOT>]info/credits" [<if $I2_ARGSTRING|substr:0:12  == "info/credits">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_cred','menu_news','menu_eighth','menu_polls','menu_prefs','menu_suggest','menu_help','menu_logout')" onmouseout="menuitem_onmouseout('menu_cred')">Credits</a>
  <span class="bold">&middot;</span>
  <a id="menu_help" href="[<$I2_ROOT>]info/[<$I2_ARGSTRING|replace:'info/':''|escape>]" [<if $I2_MODNAME == "info" && $I2_ARGSTRING|substr:0:12 != "info/credits">]class="currentmodule" [</if>]style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_help','menu_news','menu_eighth','menu_polls','menu_prefs','menu_suggest','menu_cred','menu_logout')" onmouseout="menuitem_onmouseout('menu_help')">Help</a>
  <span class="bold">&middot;</span>
  <a id="menu_logout" href="[<$I2_ROOT>]logout" style="opacity: 1;" onmouseover="menuitem_onmouseover('menu_logout','menu_news','menu_eighth','menu_polls','menu_prefs','menu_suggest','menu_cred','menu_help')" onmouseout="menuitem_onmouseout('menu_logout')">Logout</a>
</div>
