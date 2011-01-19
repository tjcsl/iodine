<table>
<tr><th>Date</th><th>Type</th><th>Cost</th></tr>
[<foreach from=$tests item=test>]
<tr><td>[<$test.time|date_format:"%b %e, %Y">]</td><td>[<$test.type>]</td><td>[<$test.cost>]</td></tr>
[</foreach>]
</table>
<hr />
Sign-Up: <a href="https://services.actstudent.org/OA_HTML/actibeCAcdLogin.jsp">ACT</a> | <a href="http://sat.collegeboard.com/register">SAT</a><br />
Test Prep: <a href="http://www.actstudent.org/sampletest/index.html">ACT</a> | <a href="http://sat.collegeboard.com/practice/sat-practice-test">SAT</a>
