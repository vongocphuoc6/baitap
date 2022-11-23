<?php
// Include the CKEditor class.
include_once "ckeditor/ckeditor.php";

// Create a class instance.
$CKEditor = new CKEditor();

// Path to the CKEditor directory.
$CKEditor->basePath = 'ckeditor';

// Replace a textarea element with an id (or name) of "textarea_id".
//$CKEditor->replace("tomtat");
$CKEditor->replaceall();

?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>

<body>
<form action="" method="post"><textarea name="tomtat" cols="" rows="" ></textarea>
<textarea name="tomtat1" cols="" rows="" ></textarea>

<?php
// Include the CKEditor class.
include_once "ckeditor/ckeditor.php";

// Create a class instance.
$CKEditor = new CKEditor();

// Path to the CKEditor directory.
$CKEditor->basePath = '/ckeditor/';

// Replace a textarea element with an id (or name) of "textarea_id".
//$CKEditor->replace("tomtat");
$CKEditor->replaceall();

?>
			<input name="ok" type="submit" value="Ok" />
</form>
<?php
if(isset($_POST["tomtat"]))
echo stripslashes($_POST["tomtat"]);
if(isset($_POST["tomtat1"]))
echo $_POST["tomtat1"];
?>
</body>
</html>