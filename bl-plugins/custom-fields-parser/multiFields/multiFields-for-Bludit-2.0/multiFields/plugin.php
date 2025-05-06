<?php

class multiField extends Plugin
{


	public function afterPageModify()
	{
		$pathContent = PATH_CONTENT . 'multiField/';
		$slug = $_POST['slug'];
		$file = $pathContent . $slug . '.json';
		$multiFieldType = $_POST['type-multifield'];
		$multiFieldLabel = $_POST['label-multifield'];
		$multiField = $_POST['multifield'];
		$ars = array();
		foreach ($multiField as $key => $value) {
			$ars[$multiFieldLabel[$key]] = ["label" => $multiFieldLabel[$key], "value" => htmlentities(htmlentities($value)), "type" => $multiFieldType[$key]];
		};
		$final = json_encode($ars, true);
		file_put_contents($file, $final);
	}



	public function adminController()
	{
		$link = DOMAIN_ADMIN . 'plugin/multiField';

		$pathContent = PATH_CONTENT . 'multiField/';

		//add new
		if (isset($_POST['submit'])) {

			$multiFieldType = $_POST['multi-field-type'];
			$multiFieldLabel = $_POST['multi-field-label'];

			$ars = array();

			foreach ($multiFieldLabel as $key => $value) {
				$ars[$key] = ["label" => $multiFieldLabel[$key], "value" => "", "type" => $multiFieldType[$key]];
			};

			$final = json_encode($ars);


			if (file_exists($pathContent) == null) {
				mkdir($pathContent, 0755);
			}


			file_put_contents($pathContent . $_POST['filename'] . '-settings.json', $final);

			echo ("<meta http-equiv='refresh' content='0'>");
			echo "<script> window.location.href = '" . $link . "?&creator=" . $_POST['filename'] . "-settings'</script>";
		};


		if (isset($_POST['changeURL'])) {
			foreach (glob(PATH_CONTENT . 'multiField/*.json') as $file) {
				$fileContent = file_get_contents($file);
				$oldurl = str_replace('/', '\/', $_POST['oldurl']);
				$newurl = str_replace('/', '\/', $_POST['newurl']);
				$newContent = str_replace([$oldurl, $oldurl . '/'], [$newurl, $newurl . '/'], $fileContent);
				file_put_contents($file, $newContent);
			}
			echo '<div class="mf-alert">Done!</div>';
		};


		//delete

		if (isset($_GET['delete'])) {
			unlink($pathContent . $_GET['delete'] . '.json');
			echo "<script> window.location.href = '" . $link . "'</script>";
		};


		$pathContent = PATH_CONTENT . 'multiField/';

		if (file_exists($pathContent) == null) {
			mkdir($pathContent, 0755);
		};
	}

	public function adminView()
	{
		// Token for send forms in Bludit
		global $security;
		$tokenCSRF = $security->getTokenCSRF();
		$thisPath = $this->phpPath();
		$thisDomainPath = $this->domainPath();
		$pathContent = PATH_CONTENT . 'multiField/';

		$link = DOMAIN_ADMIN . 'plugin/multiField';



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
		<a href="https://www.paypal.com/donate/?hosted_button_id=TW6PXVCTM5A72"><img alt="" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0"></a>
	</div>';
	}

	public function adminSidebar()
	{
		$pluginName = Text::lowercase(__CLASS__);
		$url = HTML_PATH_ADMIN_ROOT . 'plugin/' . $pluginName;
		$html = '<a id="current-version" class="nav-link" href="' . $url . '">🏖️ MultiField Settings</a>';
		return $html;
	}


	public function adminBodyEnd()
	{
		global $page;

		if ($page !== false) {
			$thisPath = $this->phpPath();
			$thisDomainPath = $this->domainPath();
			$pathContent = PATH_CONTENT . 'multiField/';

			$slug = $page->slug();
			include($thisPath . 'PHP/extras.inc.php');
		};
	}
}



function multiFields($name)
{

	global $page;

	$file = PATH_CONTENT . 'multiField/' . $page->slug()  . '.json';

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

	$file = PATH_CONTENT . 'multiField/' . $page->slug()  . '.json';

	if (file_exists($file)) {
		$final = json_decode(file_get_contents($file), true);
		foreach ($final as $key => $value) {

			if ($final[$key]['label'] == $name) {
				return html_entity_decode(html_entity_decode($final[$key]['value']));
			}
		}
	}
}
