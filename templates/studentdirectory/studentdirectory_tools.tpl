[<$output>]

<ul>
    <li><form action="[<$I2_ROOT>]studentdirectory/tools/randomstudent" method="post">
        <input type="submit" value="Random Student" /><br />
        <input type="checkbox" name="grades[]" value="9" checked />9 
        <input type="checkbox" name="grades[]" value="10" checked />10 
        <input type="checkbox" name="grades[]" value="11" checked />11 
        <input type="checkbox" name="grades[]" value="12" checked />12
    </form>
    </li>
    <li><form action="[<$I2_ROOT>]studentdirectory/tools/mostattended" method="get">
        Global Student Attendance for Activity:<br />
        AID: <input type="text" name="aid" value="" size="4" />
        <input type="submit" value="Submit" />

    </form>
    </li>
</ul>
