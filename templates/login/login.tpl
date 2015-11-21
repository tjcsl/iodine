[<if isset($smarty.get.login)>]
    [<include file='login/REGULAR_LOGIN.tpl'>]
[<else>]
    <!--iframe style="position:absolute;top:0;left:0;width:100%;height:100%" src="https://www.youtube.com/embed/9jK-NcRmVcw?modestbranding=1&autoplay=1&controls=0&showinfo=0" frameborder="0" allowfullscreen id="ifr"></iframe>
    <style>.content{position:absolute;top:100%}</style-->
    [<include file='ION_TRANSFER.tpl'>]
[</if>]
