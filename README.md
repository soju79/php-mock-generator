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
  array('name'=> 'content', 'type'=> 'string'), 
  array('name'=> 'ip', 'type'=> 'ip'),
  array('name'=> 'reg_date', 'type'=> 'date')
);
$lang = 'kor';
$type = 'list';
$page = 2;
$size = 15;
$total = 300;

$Mock = new Mock($columns, $lang);
$mock_json = $Mock->gen($type, $page, $size, $total);

header('Content-Type: application/json; charset=utf-8');
echo $mock_json;
?>
```
