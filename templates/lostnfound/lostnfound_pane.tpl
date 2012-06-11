<div>
	[<if !$blacklisted>]
		<a href="[<$I2_ROOT>]lostnfound/add">Add a new item</a>
	[</if>]
</div>
<br />
<div>
	[<foreach from=$items item=item>]
		<a href="[<$I2_ROOT>]lostnfound/view/[<$item->id>]" style="display:block;" class="[<cycle values="odd,even">]">[<$item->title|escape:'html'>]</a>
	[<foreachelse>]
		No items are missing :D
	[</foreach>]
</div>
