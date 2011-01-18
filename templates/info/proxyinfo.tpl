[< assign var='proxyscript' value='https://iodine.tjhsst.edu/www/proxy.pac' >]
[< assign var='proxyhost' value='local.border.tjhsst.edu' >]
[< assign var='proxyport' value='8080' >]
[< assign var='dbsite' value='http://www.tjhsst.edu/curriculum/library/databases.php' >]
<h1 style="text-align: center; font-size: xx-large; font-weight: bold;">
  Internet Set-Up for Using TJHSST Library
  <br />Online Databases From Home
</h1>

<p>
Our library subscribes to online databases with high quality, authoritative resources that are
essential for your high school research. On campus, they open automatically because the
database providers can tell the search is coming from a school that has paid for the service.
The databases can also be accessed from home. But students need to set up their browser
(for example Mozilla Firefox or Internet Explorer) to connect to our school's server so that the
databases "think" you are actually on campus when you go to those database sites. This is
called "configuring your proxy settings" and it's easy to reconfigure your internet browser to link to
the databases via our TJ server (which is also called Zeus on which there runs software for the
intranet known as Iodine.)
<br /><br />
For the following directions, use the Automatic Configuration options. Only use the Manual Configuration
options if the Automatic Configuration fails completely. If that happens, please report it to the TJ 
Sysadmins at <a href="mailto:sysadmins@lists.tjhsst.edu">sysadmins@lists.tjhsst.edu</a>.
<br /><br />
The Manual Configuration options have to be set every time you want to access a database and unset every
time you want to access something else; the Automatic Configuration only needs to be set once.
<br /><br />
You can access the databases at the Library's
   <a href="[<$dbsite>]">Online Databases</a>
   web page. You will need to go to 
   <a href="[<$dbsite>]">[<$dbsite>]</a>.
<br /><br />
When you connect to a database through the proxy, you will need to log in. Use the same password that you
use for the TJHSST Intranet.
<br /><br />
If you are asked to log in by a particular database, please <strong>report it to the TJ Librarians</strong> so that they can fix problems with subscriptions.
</p>
<br />

<h3>Internet Explorer 8</h3>
<ol>
  <li> On the right side of the menu bar, click on "Tools", then on "Internet Options". </li>
  <li> Select the "Connections" tab then click on "LAN settings". </li>
  <li> Click on "LAN Settings" and select the box that says "Use a proxy server". </li>
  <li> <font color="red">Dial-up Users</font>: Click on the "Settings" box instead of "LAN Settings". </li>
  <li> Automatic Configuration 
  <ul>
  	<li> Check the box next to "Use automatic configuration script". </li>
	<li> In the Address box, put in <b>[<$proxyscript>]</b>. </li>
  </ul>
  <em>OR</em> Manual Configuration
  <ul>
  	<li> Check the box next to "Use a proxy server for your LAN". Then click on the "Advanced" button. </li>
	<li> In the HTTP box, put in <b>[<$proxyhost>]</b>. </li>
	<li> In the Port box, put in <b>[<$proxyport>]</b>. </li>
	<li> Click on OK to close the advanced settings window. </li>
  </ul>
  </li>
  <li> Click on OK to close the proxy settings window, then click Ok to close the Internet Options window. </li>
  <li> Go to the <a href="[<$dbsite>]">TJ Library Online Database</a> page. </li>
  <li> When prompted for a username and password, put in your Iodine username and password. </li>
  <li> You should now have access to the TJHSST Databases. </li>
  <li> <font color="red"><b>Manual Configuration ONLY</b></font>: when you are done using the TJHSST Databases, you will need to go back and uncheck the box next to "Use a proxy server" in order to access websites besides the databases.</li>
</ol>
<br />

<h3>Google Chrome</h3>
<ol>
  <li> Click on the wrench icon on the far right, then select the "Under the Hood" tab. </li>
  <li> In the "Network" section, click on "Change Proxy Settings", then click on "LAN Settings". </li>
  <li> Automatic Configuration
  <ul>
  	<li> Check the box next to "Use automatic configuration script". </li>
	<li> In the Address box, put in <b>[<$proxyscript>]</b>. </li>
  </ul>
  <em>OR</em> Manual Configuration 
  <ul>
  	<li> Check the box next to "Use a proxy server for your LAN". Then click on the "Advanced" button. </li>
	<li> In the HTTP box, put in <b>[<$proxyhost>]</b>. </li>
	<li> In the Port box, put in <b>[<$proxyport>]</b>. </li>
	<li> Click on OK to close the advanced settings window. </li>
  </ul>
  </li>
  <li> Click on OK to close the proxy settings window, then click Ok to close the Internet Properties window, then click Close to close the Chrome Options window. </li>
  <li> Go to the <a href="[<$dbsite>]">TJ Library Online Database</a> page. </li>
  <li> When prompted for a username and password, put in your Iodine username and password. </li>
  <li> You should now have access to the TJHSST Databases. </li>
  <li> <font color="red"><b>Manual Configuration ONLY</b></font>: when you are done using the TJHSST Databases, you will need to go back and uncheck the box next to "Use a proxy server" in order to access websites besides the databases.</li>
</ol>
<br />

<h3>Mozilla Firefox 3.x</h3>
<ol>
  <li> For Windows, click on "Tools", then on "Options".  For Mac OS X/Linux, click on "Edit", then on "Preferences". </li>
  <li> Select the "Advanced" tab, then the "Network" sub-tab. </li>
  <li> In the "Connection" section, click on settings </li>
  <li> Automatic Configuration 
  <ul>
  	<li> Select the radio button next to "Automatic proxy configuration URL". </li>
	<li> In the Address box, put in <b>[<$proxyscript>]</b>. </li>
  </ul>
  <em>OR</em> Manual Configuration
  <ul>
  	<li> Select the radio button next to "Manual proxy configuration". </li>
	<li> In the "HTTP Proxy" box, put in <b>[<$proxyhost>]</b>. </li>
	<li> In the "Port" box, put in <b>[<$proxyport>]</b>. </li>
	<li> Make sure the "Use this proxy server for all protocols" box is checked </li>
  </ul>
  </li>
  <li> Click on OK to close the Connection settings window, then on OK to close the Firefox Options window. </li>
  <li> Go to the <a href="[<$dbsite>]">TJ Library Online Database</a> page. </li>
  <li> When prompted for a username and password, put in your Iodine username and password. </li>
  <li> You should now have access to the TJHSST Databases. </li>
  <li> <font color="red"><b>Manual Configuration ONLY</b></font>: when you are done using the TJHSST Databases, you will need to go back and select the radio button next to "No Proxy" in order to access websites besides the databases. </li>
</ol>
<br />

<h3>PROXY USING A MACINTOSH USING MAC OS X - Method 1</h3>
<ol>
  <li> Click on the Apple Menu on the upper-left corner of the screen. </li>
  <li> Select the "System Preferences..." option. </li>
  <li> Click on the icon labelled "Network". </li>
  <li> Click on the device corresponding to your network connection (it is
    likely to be "Built-In Ethernet", "AirPort", or "Modem"; if you are not
	sure, it is probably the one with a green dot next to it). </li>
  <li> Click on the "Configure..." button. </li>
  <li> Click on the "Proxies" tab. </li>
  <li> Check the box that says "Web Proxy (HTTP)" in the list under "Select a
    proxy server to configure:". </li>
  <li> Under "Web Proxy Server", enter <b>[<$proxyhost>]</b>; after the
    colon, set the port to <b>[<$proxyport>]</b>. </li>
  <li> Click the "Apply Now" button at the lower-right of the window. </li>
  <li> Go to the <a href="[<$dbsite>]">TJ
    Library Online Database</a> page and refresh it (press Command-R). </li>
  <li> After using the databases, go back to the network settings and uncheck
    the box that says "Web Proxy (HTTP)" to close the portal to TJ. </li>
</ol>
<br />

<h3>PROXY USING A MACINTOSH USING MAC OS X - Method 2</h3>
<ol>
  <li> In Safari, click "Safari" on the toolbar. </li>
  <li> Click the "Preferences" option in the drop-down menu. </li>
  <li> Select the "Advanced" tab at the top of the window that comes up. </li>
  <li> Click the "Change Settings..." button next to "Proxies" at the bottom 
    of the window (it may take a few seconds to change to the "Network"
	window). </li>
  <li> Check the box that says "Web Proxy (HTTP)" in the list under "Select a
    proxy server to configure:". </li>
  <li> Under "Web Proxy Server", enter <b>[<$proxyhost>]</b>; after the
    colon, set the port to <b>[<$proxyport>]</b>. </li>
  <li> Click the "Apply Now" button at the lower-right of the window. </li>
  <li> Go back to Safari and close the "Advanced" settings window. </li>
  <li> Go to the <a href="[<$dbsite>]">TJ
    Library Online Database</a> page and refresh it (press Command-R). </li>
  <li> After using the databases, go back to the network settings and uncheck
    the box that says "Web Proxy (HTTP)" to close the portal to TJ. </li>
</ol>
<br />

<h3>Opera 10</h3>
<ol>
  <li> In the upper left, click on the Opera menu, then on settings, then on preferences. </li>
  <li> Select the advanced tap, then the Network submenu, then click on "Proxy Servers". </li>
  <li> Automatic Configuration
  <ul>
  	<li> Check the "Use automatic proxy configuration" checkbox </li>
	<li> In the box below it, put in <b>[<$proxyscript>]</b>. </li>
  </ul>
  <em>OR</em> Manual Configuration
  <ul>
  	<li> Check the "HTTP" box. </li>
	<li> In the "HTTP" box, put in <b>[<$proxyhost>]</b>. </li>
	<li> In the "Port" box, put in <b>[<$proxyport>]</b>. </li>
  </ul>
  </li>
  <li> Click Ok to close the Proxy servers window, then Ok to close the Opera Preferences window. </li>
  <li> Go to the <a href="[<$dbsite>]">
    TJ Library Online Database</a> page. </li>
  <li> When prompted for a username and password, put in your Iodine username and password. </li>
  <li> You should now have access to the TJHSST Databases. </li>
  <li> <font color="red"><b>Manual Configuration ONLY</b></font>: when you are done using the TJHSST Databases, you will need to go back and uncheck the checkbox next to "HTTP" in order to access websites besides the databases. </li>
</ol>
<br />

<h3>Konqueror</h3>
<ol>
  <li> Select "Settings", then "Configure Konqueror". </li>
  <li> On the left, select the web browsing menu, then the proxy submenu. </li>
  <li> Automatic Configuration
  <ul>
  	<li> Select the radio button next to "Use proxy configuration URL". </li>
	<li> In the Address box, put in <b>[<$proxyscript>]</b>. </li>
  </ul>
  <em>OR</em> Manual Configuration 
  <ul>
  	<li> Select the radio button next to "Manually specify the proxy settings," then click "Setup". </li>
	<li> In the "HTTP Proxy" box, put in <b>[<$proxyhost>]</b>. </li>
	<li> In the "Port" box, put in <b>[<$proxyport>]</b>. </li>
	<li> Make sure the "Use the same proxy server for all protocols" box is checked </li>
	<li> Click Ok. </li>
  </ul>
  </li>
  <li> Click Ok. </li>
  <li> Go to the <a href="[<$dbsite>]">
    TJ Library Online Database</a> page. </li>
  <li> When prompted for a username and password, put in your Iodine username and password. </li>
  <li> You should now have access to the TJHSST Databases. </li>
  <li> <font color="red"><b>Manual Configuration ONLY</b></font>: when you are done using the TJHSST Databases, you will need to go back and select the radio button next to "Connect to the Internet Directly" in order to access websites besides the databases. </li>
</ol>
<br />

<h3>AOL</h3>
<ol>
  <li> On the Sign On Screen, click "Sign On Options". </li>
  <li> In the "America Online Setup" window, click "Expert Setup". </li>
  <li> In the "AOL Expert Setup" window, click the plus sign for any network
    connection (e.g. My LAN) to expand the "Edit Connection Details". </li>
  <li> In the "Edit Connection Details area, select the connection device that
    you want to configure and click "Edit". </li>
  <li> If the "Edit Modem" window appears, click the check box labeled 
    "Automatically reconnect me and ignore interruptions when using this
	location," then click "Next". </li>
  <li> In the Edit TCP/IP: LAN or ISP window, select "Manual Proxy 
    Configuration," then click "Next". </li>
  <li> In the "Edit Manual Proxy Configuration" window, enter 
    <b>[<$proxyhost>]</b> and set the port to <b>[<$proxyport>]</b>. Then click "Next". </li>
  <li> In the "Summary" window, click "Sign On to AOL Now" to sign on
    immediately or click "Make More Changes" to make additional changes. </li>
</ol>
<br />

<div style="text-align: center;">

<p>
  Please <a href="mailto:dbproxy@tjhsst.edu">contact the Proxy Admins</a> with any questions, suggestions or to update information.
</p>
         
<p>
  [Last updated November 12, 2010] <br />
</p>

</div>
