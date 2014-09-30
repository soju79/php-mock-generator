php-mock-generator
==================

PHP mock data generator

example
---
```php
<?php
require_once('class.Mock.php');

$columns = array(
  array('name'=> 'id', 'type'=> 'index'),
  array('name'=> 'name', 'type'=> 'string', 'max'=> 45),
  array('name'=> 'subject', 'type'=> 'string', 'max'=> 255),
  array('name'=> 'count', 'type'=> 'number', 'min'=>0, 'max'=>100),
  array('name'=> 'ip', 'type'=> 'ip'),
  array('name'=> 'grade', 'type'=> 'enum', 'data'=> ['a', 'b', 'c', 'd', 'e', 'f']),
  array('name'=> 'passed', 'type'=> 'boolean'),
  array('name'=> 'reg_date', 'type'=> 'date')
);
$get_columns = array_merge($columns, array(array('name'=> 'content', 'type'=> 'string')));
$lang = 'kor';
$type = 'list';
$page = 2;
$size = 15;
$total = 300;

$Mock = new Mock($columns, $lang);
$mock_array_list = $Mock->gen($type, $page, $size, $total);
$mock_array_get = (new Mock($get_columns, $lang))->gen('get');
$mock_array_edit = $Mock->gen('edit');
$mock_array_del = $Mock->gen('del');
$mock_array_menu = $Mock->gen('menu', null, null, 20);

header('Content-Type: application/json; charset=utf-8');
$response = json_encode(array(
	'list'=> $mock_array_list,
	'get'=> $mock_array_get,
	'edit'=> $mock_array_edit,
	'del'=> $mock_array_del,
	'menu'=> $mock_array_menu
));
//$response = $Mock->json($type, $page, $size, $total);
if (!empty($_GET['callback'])) {
	echo preg_replace('/[^a-zA-Z0-9_.]+/', '', $_GET['callback']) . '(' . $response . ')';
} else {
	echo $response;
}
?>
```
