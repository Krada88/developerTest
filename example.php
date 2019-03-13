<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require($_SERVER['DOCUMENT_ROOT'] . '/elements.php');

$elem = new Elements();

$elem->setProperty('select', ['ID', 'NAME']);
$elem->setProperty('filter', ['IBLOCK_ID' => 4]);
$elem->setProperty('order', ['ID' => 'ASC']);

$data = $elem->getList();

var_dump($data);