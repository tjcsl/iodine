<h1>Welcome to the TJHSST Intranet</h1>
If you are not [<$I2_USER->fullname>], please <a href="[<$I2_ROOT>]logout">logout</a>.
<script src="[<$I2_ROOT>]www/js/prefs.js" type="text/javascript" language="javascript"></script>
<form method="post" action="[<$I2_ROOT>]welcome">
<input type="hidden" name="posted" value="" />
The Intranet provides many services to the students and staff at TJ, including an online directory and eighth period signups, to name just a few.<br />
Before you begin using the Intranet, we ask that you read this page and enter information as necessary.  Some of the features mentioned here will not be available until you have completed this Intranet brief.<br /><br />
[<if $I2_USER->grade == "staff">]Unfortunately, we cannot accurately determine what your e-mail might be, and we aren't currently provided with a list of staff e-mails.  It is helpful for students if you enter your e-mail address(es) so that Intranet can be a centralized place to search for anyone's e-mail:[<else>]Since people like to be able to reach you via e-mail if they need to, please [<if $user->mail>]verify[<else>]enter[</if>] your <strong>e-mail address(es)</strong>:[</if>]<br />
<table><tr><td>
[<foreach from=$I2_USER->mail item=email name=mail_loop>]
<input class="pref_preference_input" type="text" name="pref_mail[]" value="[<$email|escape:'html'>]" />[<if $smarty.foreach.mail_loop.first>]<a href="#" onClick="add_field('mail', this);">Add Another</a>[<else>]<a href="#" onClick="remove_field('mail', this);">Remove</a>[</if>][<if !$smarty.foreach.mail_loop.last>]<br />[</if>]
[<foreachelse>]
<input class="pref_preference_input" type="text" name="pref_mail[]" /><a href="#" onClick="add_field('mail', this);">Add Another</a>
[</foreach>]
</td></tr></table>
[<if $I2_USER->grade != "staff">]Please don't enter an e-mail that you don't use.  It is frustrating when people list an e-mail that they cannot be reached at.  [</if>]You may add/verify other personal information (cell phone, AIM, etc.), and change other settings on the "Preferences" page.  Keep in mind that any information you enter will be available to the entire TJ community.<br /><br />
[<if $I2_USER->grade != "staff">]<strong>Student e-mail accounts</strong><br />
All students at TJHSST are assigned their own e-mail address.  Your TJ e-mail address is [<$user->tjmail>].  You can access your TJ e-mail by going to <a href="https://franklin.tjhsst.edu/">https://franklin.tjhsst.edu/</a>, clicking on the "Mail" link on the Intranet login page, or by clicking on the link in the E-mail intrabox.  You can also use a mail client such as Thunderbird or Outlook to access your TJ e-mail (using either the IMAP or POP3 settings).<br />
A feature that some students find useful is the e-mail forwarding feature.  The mail system that we use allows you to setup your TJ e-mail to forward to another e-mail address that you may have.  This gives you the benefit of combining your TJ and non-TJ mail together while giving some people only your TJ address.  This is not configured through Intranet; see the Options page after logging in to TJ webmail.<br /><br />[</if>]
<strong>Reading announcements</strong><br />
[<if $I2_USER->grade != "staff">]Please read the announcements that are posted regularly on Intranet!  They are posted because they contain some sort of information that is important to some extent.  Also, we advise that when you have finished reading a news item,[<else>]We recommend that after reading a news item on Intranet,[</if>] you "Mark as Read" to prevent your news page from growing horrendously long.<br /><br />
[<if $I2_USER->grade == "staff">]
<strong>Polls</strong><br />
From time to time, Intranet posts survey polls.  Most of these polls are also available for staff to vote in.  Please feel free to participate if you see a poll that interests you.<br /><br />
<strong>Directory access and student privacy</strong><br />
As a member of the TJ faculty, you have been given access to all student information regardless of their privacy settings.  Please be aware of this fact and do not allow students to use your Intranet account to access the directory.<br /><br />
<strong>Identifying lost calculators</strong><br />
If you find a lost calculator and are unable to identify who the owner is, Intranet has a database of calculators that students voluntarily registered their calculator(s) in.  To search this database, click on the "Identify lost calculator" link in the "Useful Links" intrabox.<br /><br />
[</if>]
[<if $I2_USER->grade != "staff">]
<strong>Calculator registration</strong><br />
If you own a TI calculator that you bring to TJ, you may want to register it in Intranet.  By registering, you enable teachers and administrators to identify you as the owner of your calculator in the event you lose it.  You may register calculators on the "Preferences" page.<br /><br />
[</if>]
The TJ Intranet is student-written and student-run.  It is continually being improved, and there are always bugs that are being ironed out.  If you run into a problem or want to give comments or suggestions, feel free to use our suggestion box.<br /><br />
You are encouraged to explore the Intranet to become familiar with the various features that it has.<br /><br />
Thanks for reading and have a great year!<br />
Where do you want to go first?<br />
[<* Actually, I would style these as input[type="submit"], but IE6 does not recognize that style... *>]
<input type="submit" name="news" value="Home (News)" class="submit" /><input type="submit" name="prefs" value="Preferences" class="submit" />
</form>
