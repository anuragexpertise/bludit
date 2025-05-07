<?php

class multiFields extends Plugin
{
	private $url;
	public function init()
	{
		global $url;
		$this->url = $url;
		$pathContent = PATH_CONTENT . 'multiFields/';
		if (!file_exists($pathContent)) {
			mkdir($pathContent, 0755);
		}

		// Create video directories if they don't exist
		$videoDirs = [
			PATH_UPLOADS . 'videos',
			PATH_UPLOADS . 'videos/thumbs'
		];

		foreach ($videoDirs as $dir) {
			if (!file_exists($dir)) {
				mkdir($dir, 0755, true);
			}
		}
	}

	public function afterPageModify()
	{
		global $security;

		if (!isset($_POST['tokenCSRF']) || !$security->validateTokenCSRF($_POST['tokenCSRF'])) {
			return false;
		}

		if ($this->isAjaxRequest() && !empty($_FILES)) {
			$this->handleAjaxUpload();
			exit;
		}

		// 2. Sanitize slug using Bludit's Text class
		$slug = Text::cleanUrl($_POST['slug'] ?? '');
		if (empty($slug)) {
			return false;
		}

		$pathContent = PATH_CONTENT . 'multiFields/';
		$file = $pathContent . $slug . '.json';

		// Initialize arrays safely
		$multiFieldType = $_POST['type-multiFields'] ?? [];
		$multiFieldLabel = $_POST['label-multiFields'] ?? [];
		$multiFields = $_POST['multiFields'] ?? [];

		// // Handle video uploads
		// if (!empty($_FILES['multiFields-video'])) {
		// 	require_once(__DIR__ . '/helpers/video.class.php');

		// 	foreach ($_FILES['multiFields-video']['name'] as $key => $value) {
		// 		if (
		// 			$multiFieldType[$key] === 'video' &&
		// 			$_FILES['multiFields-video']['error'][$key] === UPLOAD_ERR_OK
		// 		) {

		// 			$video = new Video();
		// 			$video->setUUID($slug);

		// 			if (
		// 				$video->upload([
		// 					'name' => $_FILES['multiFields-video']['name'][$key],
		// 					'tmp_name' => $_FILES['multiFields-video']['tmp_name'][$key],
		// 					'error' => $_FILES['multiFields-video']['error'][$key]
		// 				])
		// 			) {
		// 				$multiFields[$key] = DOMAIN_UPLOADS . 'videos/' . $video->getFilename();
		// 				header('Content-Type: application/json');

		// 				echo json_encode([
		// 					'status' => 'success',
		// 					'filename' => $$video->getFilename(),
		// 					// whatever you're returning
		// 				]);
		// 			}
		// 		}

		// 	}
		// }
		// Handle video deletions
		// if (isset($_POST['delete-image'])) {
		// 	foreach ($_POST['delete-image'] as $imagePath) {
		// 		$filePath = str_replace(DOMAIN_UPLOADS . 'images/', PATH_UPLOADS . 'images/', $imagePath);
		// 		if (file_exists($filePath)) {
		// 			unlink($filePath);
		// 		}
		// 	}
		// }

		// if (isset($_POST['delete-video'])) {
		// 	foreach ($_POST['delete-video'] as $videoPath) {
		// 		$filePath = str_replace(DOMAIN_UPLOADS . 'videos/', PATH_UPLOADS . 'videos/', $videoPath);
		// 		if (file_exists($filePath)) {
		// 			unlink($filePath);
		// 		}
		// 	}
		// }

		$ars = array();
		foreach ($multiFields as $key => $value) {
			$ars[$multiFieldLabel[$key]] = [
				"label" => $multiFieldLabel[$key],
				"value" => htmlentities(htmlentities($value)),
				"type" => $multiFieldType[$key]
			];
		}

		file_put_contents($file, json_encode($ars, true));
	}

	private function handleAjaxUpload()
	{
		global $security;

		// Set headers first to ensure JSON response
		header('Content-Type: application/json');
		http_response_code(200);

		try {
			// Validate CSRF token
			if (!$security->validateTokenCSRF($_POST['tokenCSRF'] ?? '')) {
				throw new Exception('Invalid CSRF token');
			}

			// Check if we have any files
			if (empty($_FILES)) {
				throw new Exception('No files were uploaded');
			}

			// Handle video upload
			if (!empty($_FILES['multiFields-video'])) {
				require_once(__DIR__ . '/helpers/video.class.php');
				$video = new Video();
				$video->setUUID(uniqid());

				$uploadResult = $video->upload([
					'name' => $_FILES['multiFields-video']['name'],
					'tmp_name' => $_FILES['multiFields-video']['tmp_name'],
					'error' => $_FILES['multiFields-video']['error']
				]);

				if (!$uploadResult) {
					throw new Exception('Video upload failed');
				}
				
				echo json_encode([
					'success' => true,
					'path' => DOMAIN_UPLOADS . 'videos/' . $video->getFilename(),
					'type' => 'video'
				]);
				exit;
			}

			// Handle image upload (now using 'image' as the field name consistently)
			if (!empty($_FILES['image'])) {
				$targetDir = PATH_UPLOADS . 'images/';
				if (!file_exists($targetDir)) {
					mkdir($targetDir, 0755, true);
				}

				// Validate file
				$file = $_FILES['image'];
				$fileMime = mime_content_type($file['tmp_name']);
				$validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

				if (!in_array($fileMime, $validTypes)) {
					throw new Exception('Invalid image type');
				}

				$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				$filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
				$targetFile = $targetDir . $filename;

				if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
					throw new Exception('Failed to move uploaded file');
				}

				echo json_encode([
					'success' => true,
					'path' => DOMAIN_UPLOADS . 'images/' . $filename,
					'type' => 'image'
				]);
				exit;
			}

			throw new Exception('No valid file upload detected');
		} catch (Exception $e) {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'error' => $e->getMessage()
			]);
			exit;
		}
	}

	public function adminController()
	{
		$link = DOMAIN_ADMIN . 'plugin/multiFields';
		$pathContent = PATH_CONTENT . 'multiFields/';

		// Handle uploads via dedicated endpoint
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isAjaxRequest()) {
			$this->handleAjaxUpload();
			exit;
		}
		;
		//add new
		if (isset($_POST['submit'])) {

			$multiFieldType = $_POST['multi-field-type'];
			$multiFieldLabel = $_POST['multi-field-label'];

			$ars = array();

			foreach ($multiFieldLabel as $key => $value) {
				$ars[$key] = ["label" => $multiFieldLabel[$key], "value" => "", "type" => $multiFieldType[$key]];
			}
			;

			$final = json_encode($ars);


			if (file_exists($pathContent) == null) {
				mkdir($pathContent, 0755);
			}


			file_put_contents($pathContent . $_POST['filename'] . '-settings.json', $final);

			echo ("<meta http-equiv='refresh' content='0'>");
			echo "<script> window.location.href = '" . $link . "?&creator=" . $_POST['filename'] . "-settings'</script>";
		}
		;


		if (isset($_POST['changeURL'])) {
			foreach (glob(PATH_CONTENT . 'multiFields/*.json') as $file) {
				$fileContent = file_get_contents($file);
				$oldurl = str_replace('/', '\/', $_POST['oldurl']);
				$newurl = str_replace('/', '\/', $_POST['newurl']);
				$newContent = str_replace([$oldurl, $oldurl . '/'], [$newurl, $newurl . '/'], $fileContent);
				file_put_contents($file, $newContent);
			}
			echo '<div class="mf-alert">Done!</div>';
		}
		;


		//delete

		if (isset($_GET['delete'])) {
			unlink($pathContent . $_GET['delete'] . '.json');
			echo "<script> window.location.href = '" . $link . "'</script>";
		}
		;


		$pathContent = PATH_CONTENT . 'multiFields/';

		if (file_exists($pathContent) == null) {
			mkdir($pathContent, 0755);
		}
		;


	}
	private function isAjaxRequest()
	{
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}


	private function generateVideoThumbnail($videoPath)
	{
		$thumbDir = PATH_UPLOADS . 'videos/thumbs/';
		if (!file_exists($thumbDir)) {
			mkdir($thumbDir, 0755, true);
		}

		$thumbPath = $thumbDir . basename($videoPath) . '.jpg';

		// Use FFmpeg to generate thumbnail (ensure FFmpeg is installed)
		$cmd = "ffmpeg -i {$videoPath} -ss 00:00:01 -vframes 1 {$thumbPath}";
		exec($cmd, $output, $returnCode);

		if ($returnCode === 0 && file_exists($thumbPath)) {
			return DOMAIN_UPLOADS . 'videos/thumbs/' . basename($thumbPath);
		}
		return null;
	}
	public function adminView()
	{
		// Token for send forms in Bludit
		global $security;
		$tokenCSRF = $security->getTokenCSRF();
		$thisPath = $this->phpPath();
		$thisDomainPath = $this->domainPath();
		$pathContent = PATH_CONTENT . 'multiFields/';

		$link = DOMAIN_ADMIN . 'plugin/multiFields';



		if (isset($_GET['creator'])) {
			include($thisPath . 'PHP/addNew.inc.php');
		} elseif (isset($_GET['migrate'])) {
			include($thisPath . 'PHP/migrate.inc.php');
		} elseif (isset($_GET['howtouse'])) {
			include($thisPath . 'PHP/howtouse.php');
		} elseif (isset($_GET['browser'])) {
			include($thisPath . 'PHP/imagebrowser.inc.php');
		} else {
			include($thisPath . 'PHP/list.inc.php');
		}

		echo '<div id="paypal" style="margin-top:10px; background: #fafafa; border:solid 1px #ddd; padding: 10px;box-sizing: border-box; text-align: center;">
		<p style="margin-bottom:10px;">If you want to see new plugins, buy me a ☕ :) </p>
		<a href="https://www.paypal.com/donate/?hosted_button_id="><img alt="" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0"></a>
	</div>';
	}

	public function adminSidebar()
	{
		$pluginName = Text::lowercase(__CLASS__);
		$url = HTML_PATH_ADMIN_ROOT . 'plugin/' . $pluginName;
		$html = '<a id="current-version" class="nav-link" href="' . $url . '">🏖️ multiFields Settings</a>';
		return $html;
	}


	public function adminBodyEnd()
	{
		global $page;

		if ($page !== false) {
			$thisPath = $this->phpPath();
			$thisDomainPath = $this->domainPath();
			$pathContent = PATH_CONTENT . 'multiFields/';
			$slug = $page->slug();

			include($thisPath . 'PHP/extras.inc.php');
		}

		// Check if we're on content editing pages using the class property
		if (
			$this->url && method_exists($this->url, 'whereAmI') &&
			($this->url->whereAmI() === 'edit-page' || $this->url->whereAmI() === 'new-page')
		) {
			return '
        <script>
        $(document).ready(function() {
			// Handle video preview for video fields
			$(document).on("change", "input[type=file].video-field", function() {
				const container = $(this).closest(".multiFields-video-container");
				container.find(".video-preview").remove();
				
				if (this.files && this.files[0]) {
					const preview = $("<div>").addClass("video-preview mt-2");
					const video = $("<video>", {
						controls: true,
						src: URL.createObjectURL(this.files[0]),
						width: "100%"
					});
					
					preview.append(video);
					container.append(preview);
				}
			});
		});
		</script>';
		}
		return false;
	}
}



function multiFields($name)
{

	global $page;

	$file = PATH_CONTENT . 'multiFields/' . $page->slug() . '.json';

	if (file_exists($file)) {
		$final = json_decode(file_get_contents($file), true);
		foreach ($final as $key => $value) {

			if ($final[$key]['label'] == $name) {
				echo html_entity_decode(html_entity_decode($final[$key]['value']));
			}
		}
	}
}

function multiFields_r($name)
{

	global $page;

	$file = PATH_CONTENT . 'multiFields/' . $page->slug() . '.json';

	if (file_exists($file)) {
		$final = json_decode(file_get_contents($file), true);
		foreach ($final as $key => $value) {

			if ($final[$key]['label'] == $name) {
				return html_entity_decode(html_entity_decode($final[$key]['value']));
			}
		}
	}
}
