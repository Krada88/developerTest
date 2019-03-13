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

