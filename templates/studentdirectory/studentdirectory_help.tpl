<div>
<strong>Search Tips</strong>
</div>

<dl>

<dt>Case</dt>
<dd>
<p>Matches are CaSe InSeNsItIvE. <tt>daNIel</tt> matches "Daniel" 
	just as well as <tt>Daniel</tt> does.</p>
</dd>

<dt>Quotations and Spaces</dt>
<dd>
<p>Quotations and spaces are not supported. In order to search for multi-word terms that contain one or more spaces, such as everyone who lives in Great Falls, you can replace all of the spaces with *'s. For example: 
	<tt>city:great*falls</tt> matches "Great Falls".</p>
</dd>

<dt>Wildcards</dt>
<dd>
<p>Use a wildcard (<tt>*</tt>) in searches if you're not sure of a name. These work 
	the same way as DOS or Linux shell wildcards---the star matches any number of characters.
	For example:</p>
<ul>
<li><tt>jo*e*</tt> matches "jones", "joe", "joseph", 
	and "jorge". In general, it matches anything beginning with "jo", followed by any 
	number of characters (including no characters), followed by an "e", followed by any 
	number of characters.</li>
</ul>
</dd>

<dt>Field Matching</dt>
<dd>
<p>Specify fields by prefixing a field name and a colon to a search string. All search terms
	you specify are 'and'ed together---they all must be true for a student to match. For example,
	<tt>lastname:white grade:12</tt> searches for a senior with the last name "white".</p>
<dl>

<dt>No prefix</dt>
<dd>
<p>A search term without a field name prefixed will, if it contains 
	alphabetic characters (letters), be matched against all names 
	(first, middle, last, and nickname). </p>
</dd>

<dt><tt>name</tt></dt>
<dd>
<p>Searches in all name fields (first, middle, last, nickname). <br />
	<tt>name:r*a</tt> gets you "Rebecca Hope Dezube" and "Philip Sanghoon 
	Rha", but not "Rebekah Hannah Cutler".</p>
</dd>

<dt><tt>firstname</tt></dt>
<dd>
<p>Searches in the first name field.<br />
	<tt>firstname:m*l*</tt> picks up "Menelik Yilma" and 
	"Adam McLendon Eames", but not "Lynn Meghan Maxwell".</p>
</dd>

<dt><tt>middlename</tt></dt>
<dd>
<p>Searches in the middle name field.<br />
	<tt>middlename:b*t*</tt> picks up "Linus Benedict Torvalds", but
	not "Ludwig van Beethoven".</p>
</dd>

<dt><tt>lastname</tt></dt>
<dd>
<p>Searches in only the last name field. No name truncation 
	is supported, as for unprefixed words---see above.<br />
	<tt>lastname:b*n*</tt> gets you "Tamara Lamb Bjelland", but not 
	"Brittany Thorne Reid".</p>
</dd>

<dt><tt>nickname</tt></dt>
<dd>
<p>Searches in the nickname field.<br />
	<tt>nickname:d*y*</tt> picks up "George (Dubya) Bush", but
	not "Robert (Bob) Dylan".</p>
</dd>

<dt><tt>grade</tt></dt>
<dd>
<p>Searches by grade.</p>
</dd>

<dt><tt>address</tt></dt>
<dd>
<p>Searches by address. Remember, spaces are not supported. For example, if you
	wanted to find out who lives at 555 N Oak St, you would search
	<tt>address:555*N*Oak*St</tt>.</p>
</dd>

<dt><tt>city</tt></dt>
<dd>
<p>Searches by what city people live in.<br />
	<tt>city:alexandria</tt> finds "Michael Brandon Craig" and 
	"Christopher Paul Said" (people who live in Alexandria!).</p>
</dd>

<dt><tt>zip</tt></dt>
<dd>
<p>Searches by zip code.<br />
	<tt>zip:2204*</tt> matches anyone 
	whose zip code begins with 2204 (22044, 22041, etc.).</p>
</dd>

<dt><tt>phone</tt></dt>
<dd>
<p>Searches by both home phone number and cellphone number. It can be
	formatted either as 123-456-7890 or 1234567890. <!--(NOT (123)-456-7890).--></p>

<dt><tt>homephone</tt></dt>
<dd>
<p>Searches by home phone number only. The formatting is the same
	as <tt>phone</tt></p>
</dd>

<dt><tt>cell</tt></dt>
<dd>
<p>Searches by cellphone number only. The formatting is the same
	as <tt>phone</tt></p>
</dd>

<dt><tt>sex</tt></dt>
<dd>
<p>Searches by sex.<br />
	<tt>sex:f</tt> gets you all the women.</p>
</dd>

<dt><tt>namesound</tt></dt>
<dd>
<p>Does a soundex search on all the name fields. This tries to 
	find names that sound like what you typed in. The first letter 
	has to be correct.<br />
	<tt>namesound:chawrells</tt> finds "Carlos Hipolito Villa", 
	"Raymond Charles Hohenstein", and "Alexis Danielle Charles".</p>
</dd>

<dt><tt>firstsound</tt></dt>
<dd>
<p>Does a soundex search on first name. This tries to
	find names that sound like what you typed in. The first letter
	has to be correct.<br />
	<tt>firstsound:leeleen</tt> brings up "Lillian Shedd Whitesell".</p>
</dd>

<dt><tt>lastsound</tt></dt>
<dd>
<p>Does a soundex search on last names. This tries to
	find names that sound like what you typed in. The first letter
	has to be correct.<br />
	<tt>lastsound:hurts</tt> matches "Joia Maragioglio Hertz".</p>
</dd>

<dt><tt>AIM</tt></dt>
<dd>
<p>Searches by AOL Instant Messenger screen name. Case insensitivity and
	wildcards are supported, spaces are not. If searching for a user
	with a space in their AIM address, use a * in place of all spaces.</p>
</dd>

<dt><tt>email</tt></dt>
<dd>
<p>Searches by email address.</p>
</dd>

</dd>

</dl>
</dd>
</dl>
