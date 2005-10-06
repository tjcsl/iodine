
<B>Search Tips</B><BR>
<DL>
<DT>Case
<DD>

<P>Matches are CaSe InSeNsItIvE. <TT>daNIel</TT> matches "Daniel" 
just as well as <TT>Daniel</TT> does.</P>
<DT>Quotes
<DD>
<P>Quotes are supported, so to find all the people who live in Great Falls, you can do
<TT>city:"great falls"</TT>.</P>

<DT>Wildcards
<DD>
<P>Use wildcards (<TT>*</TT>,
		<TT>?</TT>) in searches if you're not sure of a name. These work 
the same way as DOS or Linux shell wildcards---the star matches any number of characters, the question 
mark matches one character. For example:</P>
<P><UL>
<LI><TT>jo*e*</TT> matches "jones", "joe", "joseph", 
	and "jorge". In general, it matches anything beginning with "jo", followed by any 
	number of characters (including no characters), followed by an "e", followed by any 
	number of characters.
	<LI><TT>jo?e?</TT> matches "jones", and in general, anything
	beginning with "jo", followed by any one character, followed by "e", followed by
	any one character.
	</UL></P>

	<DT>Field Matching
	<DD>
	<P>Specify fields by prefixing a field name and a colon to a search string. All search terms
	you specify are 'and'ed together---they all must be true for a student to match. For example,
	<TT>lastname:white 12</TT> searches for a senior with the
	last name "white".</P>
	<DL>
	<DT>No prefix
	<DD>	<P>A search term without a field name prefixed will, if it contains
	alphabetic characters (letters), be matched against all names
	(first, middle, and last) as a prefix. So searching for 
	<TT>rob</TT> will return
	"Robert D Holeman", "Keith Robert Thornburg", and "Kilani Kei 
	Robinson". </P>

	<P>If it contains no alphabetic	characters, but includes numbers,
	it will be matched against grades. Searching for
	<TT><code>12</TT> will give you 
	all seniors. Searching for 
	<TT>1?</TT> gives you all
	10, 11, and 12th graders.</P>
	<DT><TT>name</TT>
	<DD>	<P>Searches in all name fields (first, middle, last). No name truncation 
	is supported, as for unprefixed words---see above.<BR>
	<TT>name:r*a</TT> gets you
	"Rebecca Hope Dezube" and "Philip Sanghoon Rha", but not "Rebekah Hannah 
	Cutler".</P>

	<DT><TT>firstname</TT>
	<DD>	<P>Searches in first and middle name fields. No name truncation 
	is supported, as for unprefixed words---see above.<BR>
	<TT>firstname:m*l*</TT> picks up
	"Menelik Yilma" and "Adam McLendon Eames", but not "Lynn Meghan 
	Maxwell".</P>
	<DT><TT>lastname</TT>
	<DD>	<P>Searches in only the last name field. No name truncation 
	is supported, as for unprefixed words---see above.<BR>

	<TT>lastname:b*n?</TT> gets you
	"Tamara Lamb Bjelland" but not "Brittany Thorne Reid".</P>
	<DT><TT>grade</TT>
	<DD>	<P>Searches by grade. Like <TT>name</TT>,
	does not support truncation, i.e., <TT>grade:1</TT>
	gets you noone (no first graders at our school. We hope.).<BR>

	<TT>grade:12</TT> gets you seniors
	only.</P>
	<DT><TT>city</TT>
	<DD>	<P>Searches by what city people live in.<BR>
	<TT>city:alexandria</TT> finds
	"Michael Brandon Craig" and "Christopher Paul Said" (people who live
			in Alexandria!).</P>

	<DT><TT>zip</TT>
	<DD>	<P>Searches by zip code.<BR>
	<TT>zip:2204?</TT> matches anyone
	whose zip code begins with 2204 (22044, 22041, etc.).</P>
	<DT><TT>sex</TT>
	<DD>	<P>Searches by sex.<BR>

	<TT>sex:f</TT> gets you all the
	women.</P>
	<DT><TT>namesound</TT>
	<DD>	<P>Does a soundex search on all the name fields. This tries to 
	find names that sound like what you typed in. The first letter
	has to be correct.<BR>
	<TT>namesound:chawrells</TT> finds
	"Carlos Hipolito Villa", "Raymond Charles Hohenstein", and "Alexis 
	Danielle Charles".</P>

	<DT><TT>firstsound</TT>
	<DD>	<P>Does a soundex search on first and middle names. This tries to
	find names that sound like what you typed in. The first letter
	has to be correct.<BR>
	<TT>firstsound:leeleen</TT> brings up
	"Lillian Shedd Whitesell".</P>
	<DT><TT>lastsound</TT>
	<DD>	<P>Does a soundex search on last names. This tries to
	find names that sound like what you typed in. The first letter
	has to be correct.<BR>

	<TT>lastsound:hurts</TT> matches
	"Joia Maragioglio Hertz".
	<DT><TT>AIM</TT>
	<DD>	<P>Searches by AOL Instant Messenger screen name. Spacing does not matter for this query. Case insensitivity and wildcards are supported.<BR>
	<TT>AIM:AOLuser</TT>, <TT>AIM:"AOL User"</TT>, and <TT>AIM:"a o lUsEr"</TT> will all match "AOL User".
	</DL>

	</DL>
