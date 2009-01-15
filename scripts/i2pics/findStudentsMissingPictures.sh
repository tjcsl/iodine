#!/bin/bash
#
# Figure out who is missing pictures from this year.
# This script is even crappier than the one for import!
#
# - Seniors get pictures some years.  Search this file for keyword "senior"
#     and uncomment as appropriate if this is one of those years.
# - Set the pswd and pathtopics variables.
# - Change the 4 graduationyear= search filters to the correct years.
# - Run from an empty scratchdir to make sure that the temporary files
#     don't clobber legitimate files.
#
# Written by William Yang (2008)
# 2009/01/15
#

pswd=MANAGERPSWDGOESHERE
pathtopics=/home/wyang/200809pics/LIFETOUCH/IMAGES

lds="ldapsearch -D cn=manager,dc=tjhsst,dc=edu -w $pswd -LLLx"

echo "These DNs don't have a picture for this year:"

#get all dns and format them
$lds '(&(objectclass=tjhsststudent)(graduationyear=2012))' dn|awk '{print $2}'|grep iodineUid > freshman_dns.ldif
$lds '(&(objectclass=tjhsststudent)(graduationyear=2011))' dn|awk '{print $2}'|grep iodineUid > sophomore_dns.ldif
$lds '(&(objectclass=tjhsststudent)(graduationyear=2010))' dn|awk '{print $2}'|grep iodineUid > junior_dns.ldif
#$lds '(&(objectclass=tjhsststudent)(graduationyear=2009))' dn|awk '{print $2}'|grep iodineUid > senior_dns.ldif

#find where the photo is missing
for i in freshman sophomore junior #senior
do
	while read line
	do
		$lds -b cn="$i"Photo,$line dn > /dev/null 2>&1
		if [ "$?" = "32" ]
		then
			echo $line
		fi
	done < "$i"_dns.ldif
done

#cleanup
rm -f freshman_dns.ldif sophomore_dns.ldif junior_dns.ldif senior_dns.ldif

#check for filenames that have an ID not in LDAP
echo "These files exist but have no corresponding ID in LDAP (noid's excluded from checking):"
cd $pathtopics
for i in `ls -1|grep -v noid|cut -d. -f1`
do
	if [ "`$lds tjhsststudentid=$i dn`" = "" ]
	then
		echo $i
	fi
done
