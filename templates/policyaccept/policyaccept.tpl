<!-- [<$I2_USER->eighthagreement>] -->
<div style="position: absolute; top: 15px; right: 15px">
    <a href="[<$I2_ROOT>]logout">Logout</a>
</div>
<div style="max-width: 700px; margin-left: auto; margin-right: auto; padding: 15px">

    <h1>Eighth Period Policies 2014-2015</h1>
    <h2>Eighth Period Signups:</h2>
    <h3>I understand that..</h2>
    <ul>
        <li>
            <b>I must sign up for 8th period by the end of lunch</b>
        </li>
        <li>
            <b>If I have not signed up by lunch, I will be assigned to an Administrative Study Hall</b> (check Intranet for location after lunch). This activity cannot be changed.
        </li>
        <li>
            <b>If my activity is cancelled,</b> I must sign up for another activity before the end of lunch or come to the 8th Period Office for a pass.
        </li>
        <li>
            <b>If I wish to change my activity after lunch,</b> I must get a pass from the 8th Period Office.
        </li>
    </ul>
    <h2>Eighth Period Attendance:</h2>
    <h3>I understand that..</h3>
    <ul>
        <li>
            Because 8th period is a required part of the school day, the school must account for my whereabouts during this time. <b>Attendance is recorded for every activity.</b>
        </li>
        <li>
            <b>I must keep track of my attendance and clear absences within two weeks.</b>
        </li>
        <li>
            Eighth Period attendance records are used for references, assigning leadership, mentorship, parking, and other privileges.
        </li>
    </ul>

    <h3>I will be assigned to Administrative Detention if..</h3>
    <ul>
        <li>I get <b>two or more uncleared absences</b> in any quarter</li>
        <li>I get any additional uncleared absences in the same quarter after attending detention</li>
    </ul>
    <h3>I may be referred to an Administrator for possible additional consequences, including Saturday detention, if..</h3>
    <ul>
        <li>I am late or fail to show up for both blocks of the detention period</li>
        <li>I get <b>five or more absences</b> in any quarter</li>
        <li>I get <b>ten or more absences</b> in the year</li>
        <li>I get assigned to <b>three Administrative detentions</b> in the year</li>
    </ul>
    <br />
    <p>If you have any questions, please stop by the 8th Period Office.</p>


    <p>Press 'accept' below to acknowledge that you, <b>[<$user->fullname>]</b>, have read this information.</p>
    <form action="[<$I2_ROOT>]policyaccept" method="post">
        <center>
            <input type="hidden" name="accept" value="true" />
            <input type="submit" value="I accept" style="padding: 10px 20px" />
            <input type="button" onclick="location.href='/logout'" value="I do not accept" style="padding: 10px 20px" />
        </center>
    </form>

</div>
