<?php
/**
 * php-mock-generator
 *
 * @author soju79@gmail.com
 * @site https://github.com/soju79/php-mock-generator
 */
class Mock {
	private $lang = 'kor';
	private $type = 'list';
	private $doc_path = null;
	private $doc_ext = '.txt';
	private $doc = null;
	private $doc_max = null;
	private $page = 1;
	private $size = 10;
	private $total = 1000;
	private $type_arr = array('list', 'get', 'edit', 'del', 'menu');
	private $defined_type = array('id', 'ip', 'date', 'string');
	private $fields = array();

	public function __construct($fields, $lang=null) {
		$this->doc_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR;
		$this->fields = $fields;
		$this->lang = null === $lang ? $this->lang : strtolower($lang);
		$this->setDoc();
		$this->doc_max = strlen($this->doc);
	}

	private function setDoc() {
		if ('all' !== $this->lang && is_file($this->doc_path . $this->lang . $this->doc_ext)) {
			$this->doc = file_get_contents($this->doc_path . $this->lang . $this->doc_ext);
		} else {
			$d = dir($this->doc_path);
			while (false !== ($entry = $d->read())) {
				if($entry != '.' && $entry != '..' && is_file($this->doc_path . $entry)) {
					$file = $this->doc_path . $entry;
					$this->doc .= ' ' . file_get_contents($file);
				}
			}
			$d->close();
		}
	}

	private function enc($str) {
		$s = strpos($str, ' ')+1;
		$e = strrpos($str, ' ');
		return str_replace('?', '', mb_convert_encoding(substr($str, $s, $e-$s), 'UTF-8', 'UTF-8'));
	}

	private function menuItem($id=null) {
		$id = null === $id ? rand(0, $this->total) : $id;
		$text = $this->enc(substr($this->doc, rand(0, $this->doc_max), rand(0, 50)));
		$children = 1 === rand(0, 1) ? $this->menuItem() : array();
		if (strlen($text) < 1) {
			return $this->menuItem($id);
		}
		$item = array(
			'id'=> $id,
			'children'=> $children,
			'url'=> 'http://testurl.xxx?id=' . $id,
			'text'=> $text
		);
		return $item;
	}

	private function menu($total=null) {
		$total = null === $total ? rand(0, $this->total) : $total;
		$menu = array();
		for ($i=0; $i<$total; $i++) {
			array_push($menu, $this->menuItem($i+1));
		}
		return $menu;
	}

	private function make($index=null) {
		$data = array();
		foreach ($this->fields as $field) {
			switch($field['type']) {
				case 'index':
					$mock = null === $index ? rand(0, $this->total) : $index;
					break;
				case 'string':
					$max = $this->doc_max;
					if (isset($field['max']) && !empty($field['max']) && 'integer' === gettype($field['max'])) {
						$max = $field['max'];
					}
					$s = rand(0, $this->doc_max);
					$length = rand(0, $max);
					$mock = $this->enc(substr($this->doc, $s, $length));
					break;
				case 'ip':
					$mock = implode('.', array(rand(0, 255), rand(0, 255), rand(0, 255), rand(0, 255)));
					break;
				case 'date':
					$mock = date('Y-m-d H:i:s', rand(strtotime('1/1/2014'), strtotime('now')));
					break;
				default:
					$s = rand(0, $this->doc_max);
					$mock = $this->enc(substr($this->doc, $s, rand($s, $this->doc_max)));
					break;
			}
			if (strlen($mock) < 1) {
				return $this->make($index);
			}
			$data[$field['name']] = $mock;
		}
		return $data;
	}

	public function gen($type=null, $page=null, $size=null, $total=null) {
		if ('menu' !== $type && empty($this->fields)) {
			return array(
				'result'=> false,
				'error'=> 'empty fields'
			);
		}
		$type = null === $type ? $this->type : $type;
		$page = null === $page ? $this->page : $page;
		$size = null === $size ? $this->size : $size;
		$this->total = null === $total ? $this->total : $total;
		$response = null;
		if (in_array($type, $this->type_arr)) {
			if (in_array($type, array('edit', 'del'))) {
				$response = array('result'=> true);
			} else if ('get' === $type) {
				$response = array(
					'result'=> true, 
					'data'=> $this->make()
				);
			} else if ('list' === $type) {
				$list = array();
				$max=$page*$size;
				$max = $this->total < $max ? $this->total : $max;
				for ($i=($page-1)*$size; $i<$max; $i++) {
					array_push($list, $this->make($i+1));
				}
				$response = array(
					'result'=> true, 
					'total'=> $this->total,
					'list'=> $list
				);
			} else if ('menu' === $type) {
				$response = array(
					'result'=> true, 
					'data'=> $this->menu($this->total)
				);
			}
		}
		return $response;
	}

	public function json($type=null, $page=null, $size=null, $total=null) {
		return json_encode($this->gen($type, $page, $size, $total));
	}
}
?>