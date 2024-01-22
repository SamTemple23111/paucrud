<?php
// Include configuration file
require_once 'config.php';

// Include and initialize Page DB class
require_once 'PageDb.class.php';
$pageDb = new PageDb();

// Set default redirect url
$redirectURL = 'index.php';

if(isset($_POST['userSubmit'])){
	// Get form fields value
	$id = $_POST['id'];
	$title = trim(strip_tags($_POST['title']));
	$content = $_POST['content'];
	
	$id_str = '';
	if(!empty($id)){
		$id_str = '?id='.$id;
	}
	
	// Fields validation
	$errorMsg = '';
    if(empty($title)){
		$errorMsg .= '<p>Please enter title.</p>';
	}elseif($pageDb->isPageExists($title, $id)){
		$errorMsg .= '<p>The page with the same title already exists.</p>';
	}

	if(empty($content)){
		$errorMsg .= '<p>Please enter page content.</p>';
	}
	
	// Submitted form data
	$pageData = array(
        'title' => $title,
        'content' => $content
    );
	
	// Store the submitted field values in the session
	$sessData['userData'] = $pageData;
	
	// Process the form data
    if(empty($errorMsg)){
        // Create page file
		$page_slug = $pageDb->generatePageUri($title);
		$page_file = $page_slug.$pageExtention;

        $html_file = 'common/cms.html';
		$html_file_content = file_get_contents($html_file);
		$html_file_content = str_replace('[PAGE_TITLE]', $title, $html_file_content);
		$html_file_content = str_replace('[PAGE_CONTENT]', $content, $html_file_content);

		if(!file_exists($pageDir)){
			mkdir($pageDir, 0777);
		}
		$filePath = $pageDir.'/'.$page_file;
		$create_page_file = file_put_contents($filePath, $html_file_content);

        if($create_page_file){
			$pageData['page_uri'] = $page_file; 
			if(!empty($id)){
                // Get previous data
                $cond = array(
                    'where' => array(
                        'id' => $id
                    ),
                    'return_type' => 'single'
                );
                $prevPageData = $pageDb->getRows($cond);

				// Update page data
                $cond = array(
                    'id' => $id
                );
				$update = $pageDb->update($pageData, $cond);

				if($update){
                    // Remove old page file
                    if($prevPageData['page_uri'] !== $page_file){
                        $filePath_prev = $pageDir.'/'.$prevPageData['page_uri'];
                        unlink($filePath_prev);
                    }

					$sessData['status']['type'] = 'success';
					$sessData['status']['msg'] = 'Page data has been updated successfully.';

					// Remote submitted fields value from session
					unset($sessData['userData']);
				}else{
					$sessData['status']['type'] = 'error';
					$sessData['status']['msg'] = 'Something went wrong, please try again.';

					// Set redirect url
					$redirectURL = 'addEdit.php'.$id_str;
				}
			}else{
				// Insert page data
				$insert = $pageDb->insert($pageData);

				if($insert){
					$sessData['status']['type'] = 'success';
					$sessData['status']['msg'] = 'Page data has been added successfully.';

					// Remote submitted fields value from session
					unset($sessData['userData']);
				}else{
					$sessData['status']['type'] = 'error';
					$sessData['status']['msg'] = 'Something went wrong, please try again.';

					// Set redirect url
					$redirectURL = 'addEdit.php'.$id_str;
				}
			}
		}else{
			$sessData['status']['msg'] = 'Page creation failed! Please try again.';
		}
    }else{
        $sessData['status']['type'] = 'error';
        $sessData['status']['msg'] = '<p>Please fill all the mandatory fields.</p>'.$errorMsg;

        // Set redirect url
        $redirectURL = 'addEdit.php'.$id_str;
    }
	
	// Store status into the session
    $_SESSION['sessData'] = $sessData;
}elseif(($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])){
    $id = base64_decode($_GET['id']);

    // Get page data
    $cond = array(
        'where' => array(
            'id' => $id
        ),
        'return_type' => 'single'
    );
    $pageData = $pageDb->getRows($cond);

    // Delete page from database
    $delete = $pageDb->delete($id);

    if($delete){
        // Remove page file
        if(!empty($pageData['page_uri'])){
            $filePath = $pageDir.'/'.$pageData['page_uri'];
            @unlink($filePath);
        }

        $sessData['status']['type'] = 'success';
        $sessData['status']['msg'] = 'Page has been deleted successfully.';
    }else{
        $sessData['status']['type'] = 'error';
        $sessData['status']['msg'] = 'Some problem occurred, please try again.';
    }

    // Store status into the session
    $_SESSION['sessData'] = $sessData;
}

// Redirect to the respective page
header("Location:".$redirectURL);
exit();
?>