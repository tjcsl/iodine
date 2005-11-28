[<include file="eighth/eighth_header.tpl">]
Eighth Test Data for [<$method>]:<br />
[<foreach from=$args item=value key=key>]
	[<$key>]: [<php>] var_dump($this->_tpl_vars['value']); [</php>]<br />
[</foreach>]
