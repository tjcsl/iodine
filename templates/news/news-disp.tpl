
<div class="newspost" id="newspost[<$story->id>]">

	<div class="newstitle" ondblclick="doNewsShade([<$story->nid>])">[<if !$story->visible>]*HIDDEN* [</if>][<$story->title>]
		<div class="newsoptions">
			<a href="[<$I2_ROOT>]news/shade/[<$story->id>]" onclick="return doNewsShade([<$story->nid>])" id="shadelink_[<$story->nid>]">[<if $story->shaded()>]Expand[<else>]Collapse[</if>]</a> - 
			[<if !$story->has_been_read()>]
				<a href="[<$I2_ROOT>]news/read/[<$story->id>]">Mark read</a>
			[<else>]
				<a href="[<$I2_ROOT>]news/unread/[<$story->id>]">Mark unread</a>
			[</if>]
		</div>
	</div>
	 <div class="newsitem"[<if $story->shaded()>] style="display: none;"[</if>]>
	 <div class="newsgroups">
	 [<if $story->editable()>]
	  <a href="[<$I2_ROOT>]news/edit/[<$story->id>]">Edit</a>
	  - <a href="[<$I2_ROOT>]news/delete/[<$story->id>]">Delete</a>
	  --
	 [</if>]
	 Posted by [<$story->author->name>] on [<$story->posted|date_format:'%a %B %e, %Y at %l:%M %p'>] to [<$story->groupsstring>].</div>
	 <div id="newsitem_[<$story->nid>]">[<$story->text>]</div>
	 <div class="newsresponsebar">
		 <span class="newslikebtn [<if $story->liked==1>]newslikebtnliked[<else>]newslikebtnunliked[</if>]" onclick="newsLike([<$story->nid>]);" id="likebtn[<$story->id>]">[<if $story->liked==1>]Unlike[<else>]Like[</if>]</span>
		<span class="newslikecount" id="likecount[<$story->id>]">[<$story->likecount>] [<if $story->likecount == 1>]person[<else>]people[</if>] liked this</span>
	 </div>
	</div>
</div>
