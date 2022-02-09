@ECHO OFF
SET PHP="C:\php7\php.exe"
SET PROGRAM="D:\work\makeImgPpt\makeImgPpt.php"
SET POWERPOINT="C:\Program Files (x86)\Microsoft Office\Office12\POWERPNT.EXE"

SET N=1
    rem N = w, 1, 2, 3, 4
	rem only N = 1 is working yet.

SET OUTFILE=rs
REM set SORTBY=date

%PHP% %PROGRAM% %OUTFILE% %N%

%POWERPOINT% %OUTFILE%_%N%.htm
