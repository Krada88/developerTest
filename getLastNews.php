<?php

/**
 * Task 2
 */

try {
    if (php_sapi_name() != 'cli') {
        throw new Exception("Running the script is possible only in console mode!");
    }
} catch (Exception $ex) {
    exit($ex->getMessage());
}

try {
    if (extension_loaded('simplexml')) {
        $url = 'https://lenta.ru/rss';
        $content = file_get_contents($url);

        $items = new SimpleXmlElement($content);

        if (!empty($items)) {
            $i = 0;
            foreach ($items->channel->item as $item) {

                echo trim($item->title) . PHP_EOL;
                echo trim($item->link) . PHP_EOL;
                echo trim($item->description) . PHP_EOL;

                if ($i == 4) {
                    break;
                }

                $i++;
            }
        }
    } else {
        throw new Exception("RSS parsing error");
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}


/**
 * Class Elements
 */
class Elements
{

    private $select = ['*'];
    private $filter = ['ACTIVE' => 'Y'];
    private $order = ['SORT' => 'ASC'];

    private $groupBy = false;
    private $nav = ['nPageSize' => 50];

    private $cache = true;
    private $cacheTime = 3600;
    private $cacheKey = 'getList';

    private $errors = [];

    private $property = ['select', 'filter', 'order'];


    /**
     * @param $name
     * @param $value
     */
    public function setProperty($name, $value)
    {
        if (in_array($name, $this->property) && is_array($value)) {
            $this->$name = $value;
        } elseif ($name == 'cache' && is_bool($value)) {
            $this->cache = $value;
        } elseif ($name == 'cacheTime' && is_numeric($value)) {
            $this->cacheTime = $value;
        } else {
            $this->errors[] = "Invalid property type '$name' or unknown argument";
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getProperty($name)
    {
        return $this->$name;
    }


    /**
     * @return array
     */
    public function getList()
    {

        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $cache = \Bitrix\Main\Data\Cache::createInstance();
            $data = [];
            if ($this->cache && $cache->initCache($this->cacheTime, $this->cacheGeneration())) {
                $data = $cache->getVars();
            } elseif ($cache->startDataCache()) {
                $res = CIBlockElement::GetList($this->order, $this->filter, $this->groupBy, $this->nav, $this->select);
                while ($arFields = $res->GetNext()) {
                    $data[] = $arFields;
                }
                $cache->endDataCache($data);
            }

            if (empty($this->errors)) {
                return $data;
            } else {
                return $this->errors;
            }

        }


    }

    /**
     * @return string
     */
    private function cacheGeneration()
    {
        return $this->cacheKey . implode($this->select) . implode($this->filter) . implode($this->order);
    }
}


