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

// Include and initialize Page DB class
require_once 'PageDb.class.php';
$pageDb = new PageDb();

// Fetch page data from database
$pages = $pageDb->getRows();
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
        <div class="col-md-12 head">
            <h5>Pages</h5>
            <!-- Add link -->
            <div class="float-right">
                <a href="addEdit.php" class="btn btn-success"><i class="plus"></i> New Page</a>
            </div>
        </div>
        
        <!-- List the pages -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th width="3%">#</th>
                    <th width="27%">Title</th>
                    <th width="36%">Content</th>
                    <th width="16%">Created</th>
                    <th width="18%">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($pages)){ $count = 0; foreach($pages as $row){ $count++; ?>
                <tr>
                    <td><?php echo $count; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td>
                        <?php 
                            $content = strip_tags($row['content']);
                            echo (strlen($content)>$list_excerpt_length)?substr($content, 0, $list_excerpt_length).'...':$content;
                        ?>
                    </td>
                    <td><?php echo $row['created']; ?></td>
                    <td>
                        <a href="<?php echo $pageDir.'/'.$row['page_uri']; ?>" class="btn btn-outline-primary" target="_blank">view</a>
                        <a href="addEdit.php?id=<?php echo base64_encode($row['id']); ?>" class="btn btn-outline-warning">edit</a>
                        <a href="userAction.php?action_type=delete&id=<?php echo base64_encode($row['id']); ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure to delete?');">delete</a>
                    </td>
                </tr>
                <?php } }else{ ?>
                <tr><td colspan="5">No page(s) found...</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>