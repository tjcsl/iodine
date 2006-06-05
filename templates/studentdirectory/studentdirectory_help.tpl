<div>
<strong>Search Tips</strong>
</div>

<dl>

<dt>Case</dt>
<dd>
<p>Matches are CaSe InSeNsItIvE. <tt>daNIel</tt> matches "Daniel" 
	just as well as <tt>Daniel</tt> does.</p>
</dd>

<dt>Quotes</dt>
<dd>
<p>Quotes are supported, so to find all the people who live in Great Falls, you can do 
	<tt>city:"great falls"</tt>.</p>
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
<br />

<dt>Field Matching</dt>
<dd>
<p>Specify fields by prefixing a field name and a colon to a search string. All search terms
	you specify are 'and'ed together---they all must be true for a student to match. For example,
	<tt>lastname:white 12</tt> searches for a senior with the last name "white".</p>
<dl>

<dt>No prefix</dt>
<dd>
<p>A search term without a field name prefixed will, if it contains 
	alphabetic characters (letters), be matched against all names 
	(first, middle, last, and nickname). </p>
</dd>

<dt><tt>name</tt></dt>
<dd>
<p>Searches in all name fields (first, middle, last). <br />
	<tt>name:r*a</tt> gets you "Rebecca Hope Dezube" and "Philip Sanghoon 
	Rha", but not "Rebekah Hannah Cutler".</p>
</dd>

<dt><tt>firstname</tt></dt>
<dd>
<p>Searches in first and middle name fields.<br />
	<tt>firstname:m*l*</tt> picks up "Menelik Yilma" and 
	"Adam McLendon Eames", but not "Lynn Meghan Maxwell".</p>
</dd>

<dt><tt>lastname</tt></dt>
<dd>
<p>Searches in only the last name field. No name truncation 
	is supported, as for unprefixed words---see above.<br />
	<tt>lastname:b*n?</tt> gets you "Tamara Lamb Bjelland" but not 
	"Brittany Thorne Reid".</p>
</dd>

<dt><tt>grade</tt></dt>
<dd>
<p>Searches by grade.</p>
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
	<tt>zip:2204?</tt> matches anyone 
	whose zip code begins with 2204 (22044, 22041, etc.).</p>
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
<p>Searches by AOL Instant Messenger screen name. Spacing does 
	not matter for this query. Case insensitivity and wildcards
	are supported.<br />
	<tt>AIM:AOLuser</tt>, <tt>AIM:"AOL User"</tt>, and 
	<tt>AIM:"a o lUsEr"</tt> will all match "AOL User".</p>
</dd>

</dl>
</dd>
</dl>
