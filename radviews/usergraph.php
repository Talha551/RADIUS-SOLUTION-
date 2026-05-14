
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Opening Links in an iFrame</title>
	<style>
		iframe {
			width: 100%;
			height: 780px;
		}
	</style>
</head>
<?php echo $urlAddress; 
exit;
?>

<body>
    <iframe src="<?php echo $urlAddress; ?>" name="myFrame"></iframe>
</body>
</html>