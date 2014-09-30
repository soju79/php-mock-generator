<?php
require_once('class.Mock.php');

$type = !empty($_GET['type']) ? $_GET['type'] : null;
$lang = !empty($_GET['lang']) ? $_GET['lang'] : null;
$page = !empty($_GET['page']) ? (int) $_GET['page'] : null;
$size = !empty($_GET['size']) ? (int) $_GET['size'] : null;
$total = !empty($_GET['total']) ? (int) $_GET['total'] : null;
$columns = array();
if(!empty($_GET['columns']) && is_array($_GET['columns'])) {
	foreach ($_GET['columns'] as $column) {
		array_push($columns, json_decode($column, true));
	}
}

$response = (new Mock($columns, $lang))->json($type, $page, $size, $total);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
if (!empty($_GET['callback'])) {
	echo preg_replace('/[^a-zA-Z0-9_.]+/', '', $_GET['callback']) . '(' . $response . ')';
} else {
	echo $response;
}
?>