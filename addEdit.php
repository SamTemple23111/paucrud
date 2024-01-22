<?php
// Include configuration file
require_once 'config.php';

// Retrieve session data
$sessData = !empty($_SESSION['sessData'])?$_SESSION['sessData']:'';

// Get status message from session
if(!empty($sessData['status']['msg'])){
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Get page data
$page_id = '';
$pageData = $userData = array();
if(!empty($_GET['id'])){
    $page_id = base64_decode($_GET['id']);

	// Include and initialize Page DB class
    require_once 'PageDb.class.php';
    $pageDb = new PageDb();
	
	// Fetch data from database by row ID
    $cond = array(
        'where' => array(
            'id' => $page_id
        ),
        'return_type' => 'single'
    );
    $pageData = $pageDb->getRows($cond);
}
$userData = !empty($sessData['userData'])?$sessData['userData']:$pageData;
unset($_SESSION['sessData']['userData']);

$actionLabel = !empty($page_id)?'Edit':'Add';

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Pages Management with PHP and MySQL by CodexWorld</title>
<meta charset="utf-8">

<!-- Bootstrap library -->
<link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">

<!-- Stylesheet file -->
<link rel="stylesheet" href="assets/css/style.css"/>

<!-- jQuery library -->
<script src="assets/js/jquery.min.js"></script>

<!-- TinyMCE plugin library -->
<script src="assets/js/tinymce/tinymce.min.js"></script>

<!-- Initialize TinyMCE -->
<script>
tinymce.init({
	selector: '#page_content',
	plugins: [
	  'lists', 'link', 'image', 'preview', 'anchor',
      'visualblocks', 'code', 'fullscreen',
      'table', 'code', 'help', 'wordcount'
    ],
	toolbar: 'undo redo | formatselect | ' +
	  'bold italic underline strikethrough | alignleft aligncenter ' +
	  'alignright alignjustify | bullist numlist outdent indent | ' +
	  'forecolor backcolor | link image | preview | ' +
	  'removeformat | help',
    menubar: 'edit view format help'
});
</script>
</head>
<body>
<div class="container">
	<h1>Pages Management with PHP and MySQL</h1>
    
    <!-- Display status message -->
    <?php if(!empty($statusMsg) && ($statusMsgType == 'success')){ ?>
    <div class="col-xs-12">
        <div class="alert alert-success"><?php echo $statusMsg; ?></div>
    </div>
    <?php }elseif(!empty($statusMsg) && ($statusMsgType == 'error')){ ?>
    <div class="col-xs-12">
        <div class="alert alert-danger"><?php echo $statusMsg; ?></div>
    </div>
    <?php } ?>
    
    <div class="row">
		<div class="col-md-12">
			<h2><?php echo $actionLabel; ?> Page</h2>
		</div>
        <div class="col-md-9">
             <form method="post" action="userAction.php">
				<div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" name="title" placeholder="Enter page title" value="<?php echo !empty($userData['title'])?$userData['title']:''; ?>" required="">
                </div>
				<div class="form-group">
                    <label>Content</label>
                    <textarea class="form-control" name="content" id="page_content" placeholder="Enter page content here..."><?php echo !empty($userData['content'])?$userData['content']:''; ?></textarea>
                </div>
                
                <a href="index.php" class="btn btn-secondary">Back</a>
                <input type="hidden" name="id" value="<?php echo !empty($pageData['id'])?$pageData['id']:''; ?>">
                <input type="submit" name="userSubmit" class="btn btn-success" value="Submit">
            </form>
        </div>
    </div>
</div>
</body>
</html>