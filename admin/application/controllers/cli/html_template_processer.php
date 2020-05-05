<?php
/**
 * only cli
 *
 *
 *
 */
class Html_template_processer extends CI_Controller {

	const TEMPLATE_DIR = 'black_and_red';
	const TMP_DIR = 'tmp';

	public function __construct() {
		parent::__construct();
		// load gearman library
		$this->load->library(array('utils'));
		//only cli
		if (!$this->input->is_cli_request()) {
			//quit
			// echo 'Not allowed';
			show_error('Not allowed', 405);
			exit;
		}

	}

	const ASSETS_URL_TAG = 'http://www.og.local';
	const AFF_URL_TAG = 'http://aff.og.local';
	const PLAYER_URL_TAG = '//og.local';
	const PLAYER_FULL_URL_TAG = 'http://og.local';

	public function replace_template($template_dir, $target_dir, $assets_url, $player_url, $aff_url, $player_full_url) {
		$assets_url = urldecode($assets_url);
		$player_url = urldecode($player_url);
		$aff_url = urldecode($aff_url);
		$player_full_url = urldecode($player_full_url);

		echo 'template_dir:' . $template_dir . ' target_dir:' . $target_dir . ' assets_url:' . $assets_url . ' player_url:' . $player_url . ' aff_url:' . $aff_url . ' player_full_url:' . $player_full_url;

		$path = realpath(APPPATH . '/../../sites/' . $template_dir);

		$targetPath = realpath(APPPATH . '/../../sites/' . $target_dir);

		$objects = new RecursiveIteratorIterator(
			new OnlyHtmlFilterIterator(new RecursiveDirectoryIterator($path)), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($objects as $name => $object) {
			if (!$object->isDir()) {
				echo "replace url $name to $targetPath\n";
				$this->replace_url_and_save($name, $assets_url, $player_url, $aff_url, $player_full_url, $path, $targetPath);
			}
		}
	}

	private function replace_url_and_save($name, $assets_url, $player_url, $aff_url, $player_full_url, $path, $targetPath) {
		// $relPath = str_replace($targetPath, '', $name);
		$relPath = str_replace($path, '', $name);
		$html = file_get_contents($name);
		file_put_contents($targetPath . $relPath,
			str_replace(array(self::AFF_URL_TAG, self::ASSETS_URL_TAG, self::PLAYER_FULL_URL_TAG, self::PLAYER_URL_TAG),
				array($aff_url, $assets_url, $player_full_url, $player_url), $html));
	}

	public function template_to($template_dir, $target_dir, $assets_url, $player_url, $aff_url, $header_file = 'header.html', $footer_file = 'footer.html') {
		//replace header/footer
		// $this->fix_template(self::TEMPLATE_DIR, self::TMP_DIR, $header_file, $footer_file);
		$assets_url = urldecode($assets_url);
		$player_url = urldecode($player_url);
		$aff_url = urldecode($aff_url);

		echo 'template_dir:' . $template_dir . ' target_dir:' . $target_dir . ' assets_url:' . $assets_url . ' player_url:' . $player_url . ' aff_url:' . $aff_url . ' header_file:' . $header_file . ' footer_file:' . $footer_file . "\n";
		// return;
		//template to target
		//replace aff_url first
		$path = realpath(APPPATH . '/../../sites/' . $template_dir);

		$targetPath = realpath(APPPATH . '/../../sites/' . $target_dir);

		if (!file_exists($path) || !file_exists($targetPath)) {
			show_error("path don't exists, check $path and $targetPath");
		}

		// mkdir($targetPath);

		$this->fix_template($template_dir, $target_dir, $header_file, $footer_file);

		$objects = new RecursiveIteratorIterator(
			new OnlyHtmlFilterIterator(new RecursiveDirectoryIterator($path)), RecursiveIteratorIterator::SELF_FIRST);

		// $dom = new PHPHtmlParser\Dom;

		// $doc = new DOMDocument();

		// $this->load->library('SmartDOMDocument');
		// $domHeader = new DOMDocument();
		// $domFooter = new DOMDocument();

		// $header = file_get_contents(APPPATH . '/../../sites/templates/' . $header_file);
		// $domHeader->loadHTML($header);
		// $headerNode = $domHeader->getElementsByTagName('body')->item(0)->firstChild;

		// $footer = file_get_contents(APPPATH . '/../../sites/templates/' . $footer_file);
		// $domFooter->loadHTML($footer);
		// $footerNode = $domFooter->getElementsByTagName('body')->item(0)->firstChild;

		// foreach ($objects as $name => $object) {
		// 	if (!$object->isDir()) {
		// 		//remove root path
		// 		// $relPath = str_replace($path, '', $name);
		// 		//only html
		// 		echo "change header/footer $name to $targetPath\n";
		// 		$this->change_header($path, $name, $targetPath, $header_file, $footer_file);

		// 	}
		// }

		$objects = new RecursiveIteratorIterator(
			new OnlyHtmlFilterIterator(new RecursiveDirectoryIterator($targetPath)), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($objects as $name => $object) {
			if (!$object->isDir()) {
				echo "replace url $name to $targetPath\n";
				$this->replace_url($name, $assets_url, $player_url, $aff_url);
			}
		}

	}

	private function replace_url($name, $assets_url, $player_url, $aff_url) {
		// $relPath = str_replace($targetPath, '', $name);
		$html = file_get_contents($name);
		file_put_contents($name, str_replace(array(self::AFF_URL_TAG, self::ASSETS_URL_TAG, self::PLAYER_URL_TAG), array($aff_url, $assets_url, $player_url), $html));
	}

	// private function change_header($path, $name, $targetPath, $header_file, $footer_file) {
	// 	$domHeader = new DOMDocument();
	// 	$domFooter = new DOMDocument();

	// 	$header = file_get_contents(APPPATH . '/../../sites/templates/' . $header_file);
	// 	$domHeader->loadHTML($header);
	// 	$headerNode = $domHeader->getElementsByTagName('body')->item(0)->firstChild;

	// 	$footer = file_get_contents(APPPATH . '/../../sites/templates/' . $footer_file);
	// 	$domFooter->loadHTML($footer);
	// 	$footerNode = $domFooter->getElementsByTagName('body')->item(0)->firstChild;
	// 	echo "load footer:" . $footerNode->getAttribute('class') . "\n";

	// 	$doc = new DOMDocument();
	// 	//remove root path
	// 	$relPath = str_replace($path, '', $name);
	// 	//only html
	// 	// echo "$name:" . $relPath . "\n";
	// 	// $this->SmartDOMDocument();
	// 	$doc->loadHTMLFile($name);
	// 	$finder = new DomXPath($doc);
	// 	//for header
	// 	$classname = "header";
	// 	// $nodes = $finder->query("*[class~=\"$classname\"]");
	// 	$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

	// 	foreach ($nodes as $node) {
	// 		echo $node->getAttribute('class') . "\n";
	// 		$headerNode = $doc->importNode($headerNode, true);
	// 		$parentNode = $node->parentNode;
	// 		// $parentNode->removeChild($node);
	// 		$parentNode->replaceChild($headerNode, $node);
	// 	}

	// 	//for footer
	// 	$classname = "footer";
	// 	// $nodes = $finder->query("*[class~=\"$classname\"]");
	// 	$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

	// 	foreach ($nodes as $node) {
	// 		echo $node->getAttribute('class') . "\n";
	// 		$footerNode = $doc->importNode($footerNode, true);
	// 		$parentNode = $node->parentNode;
	// 		// $parentNode->removeChild($node);
	// 		$rlt = $parentNode->replaceChild($footerNode, $node);
	// 		echo "replace result:" . ($rlt === FALSE) . "\n";
	// 	}

	// 	// $encoding = "UTF-8";
	// 	// $html = file_get_contents($name);
	// 	// $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
	// 	// $doc->loadHTML($html);
	// 	// $doc->saveHTMLFile($targetPath . $relPath);
	// 	echo "write to " . $targetPath . $relPath . "\n";
	// 	file_put_contents($targetPath . $relPath, html_entity_decode($doc->saveHTML(), ENT_QUOTES, 'utf-8'));

	// }

	/**
	 *
	 *
	 */
	public function fix_template($template_dir = self::TEMPLATE_DIR, $target_dir = self::TEMPLATE_DIR, $header_file = 'header.html', $footer_file = 'footer.html') {

		echo 'template_dir:' . $template_dir . ' target_dir:' . $target_dir . ' header_file:' . $header_file . ' footer_file:' . $footer_file . "\n";

		// $this->utils->loadComposerLib();
		//fix header
		$path = realpath(APPPATH . '/../../sites/' . $template_dir);

		$targetPath = realpath(APPPATH . '/../../sites/' . $target_dir);

		// mkdir($targetPath);

		$objects = new RecursiveIteratorIterator(
			new OnlyHtmlFilterIterator(new RecursiveDirectoryIterator($path)), RecursiveIteratorIterator::SELF_FIRST);

		// $dom = new PHPHtmlParser\Dom;

		$doc = new DOMDocument();

		// $this->load->library('SmartDOMDocument');
		$domHeader = new DOMDocument();
		$domFooter = new DOMDocument();

		$header = file_get_contents(APPPATH . '/../../sites/templates/header.html');
		$domHeader->loadHTML($header);
		$headerNode = $domHeader->getElementsByTagName('body')->item(0)->firstChild;

		$footer = file_get_contents(APPPATH . '/../../sites/templates/footer.html');
		$domFooter->loadHTML($footer);
		$footerNode = $domFooter->getElementsByTagName('body')->item(0)->firstChild;

		foreach ($objects as $name => $object) {
			if (!$object->isDir()) {
				//remove root path
				$relPath = str_replace($path, '', $name);
				//only html
				echo "$name:" . $relPath . "\n";
				// $this->SmartDOMDocument();
				$doc->loadHTMLFile($name);
				$finder = new DomXPath($doc);
				//for header
				$classname = "header";
				// $nodes = $finder->query("*[class~=\"$classname\"]");
				$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

				foreach ($nodes as $node) {
					echo $node->getAttribute('class') . "\n";
					$headerNode = $doc->importNode($headerNode, true);
					$parentNode = $node->parentNode;
					// $parentNode->removeChild($node);
					$parentNode->replaceChild($headerNode, $node);
				}

				//for footer
				$classname = "footer";
				// $nodes = $finder->query("*[class~=\"$classname\"]");
				$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

				foreach ($nodes as $node) {
					echo $node->getAttribute('class') . "\n";
					$footerNode = $doc->importNode($footerNode, true);
					$parentNode = $node->parentNode;
					// $parentNode->removeChild($node);
					$parentNode->replaceChild($footerNode, $node);
				}

				// $encoding = "UTF-8";
				// $html = file_get_contents($name);
				// $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
				// $doc->loadHTML($html);
				// $doc->saveHTMLFile($targetPath . $relPath);
				file_put_contents($targetPath . $relPath, html_entity_decode($doc->saveHTML(), ENT_QUOTES, 'utf-8'));
				// $dom->loadFromFile($name);
				// $header = $dom->find('div.header');
				// echo $header->getAttribute('class') . "\n";
				echo "write to " . $targetPath . $relPath . "\n";

			}
		}
	}

}

class OnlyHtmlFilterIterator extends RecursiveFilterIterator {

	public static $FILTERS = array(
		'html',
		'htm',
	);

	public function accept() {
		if ($this->isDir()) {
			return true;
		}
		// $pathInfo = pathinfo($this->current()->getFilename());
		// echo $this->current()->getFilename() . "\n";
		return in_array($this->getExtension(), self::$FILTERS, true);
	}

}

///END OF FILE//////