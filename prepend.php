<?php
if (function_exists('php_sapi_name') && php_sapi_name() != 'cli') {

    if (!function_exists('getallheaders'))
    {
        function getallheaders()
        {
            $headers = '';
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }

    $data_file = __DIR__ . DIRECTORY_SEPARATOR . 'data.bin';
    if (file_exists($data_file) &&
        false !== ($data = file_get_contents($data_file)) &&
        false !== ($records = unserialize($data)) &&
        is_array($records)
    ) {
        $cfg = [];
        $request = explode('?', $_SERVER['REQUEST_URI']);
        $path = $request[0];
        foreach ($records as $record) {
            if (!isset($record['url'])) {
                continue;
            }
            if (strpos($path, $record['url'])) {
                $cfg = $record;
                break;
            }
        }

        if (isset($cfg['name']) && isset($cfg['frequency']) && isset($cfg['start_at']) || isset($cfg['start_at']) || isset($cfg['end_at'])) {
            $now = time();
            if (strtotime($cfg['start_at']) < $now && $now < strtotime($cfg['end_at'])) {
                // random capture
                if ($cfg['frequency'] > 1) {
                    $cfg['frequency'] = 1;
                }
                if ($cfg['frequency'] < 0.01) {
                    $cfg['frequency'] = 0.01;
                }
                $frequency *= 100;
                if ((mt_rand(0, 100) <= $frequency) {
                    $GLOBALS['xhprof_vars'] = [
                        'get' => $_GET,
                        'post' => $_POST,
                        'cookie' => $_COOKIE,
                        'headers' => getallheaders()
                    ];
                    xhprof_enable();
                    $app_name = array_search($path, $api_list);
                    if (false === $app_name) {
                        $app_name = 'unknow';
                    }
                    register_shutdown_function(function() use ($app_name) {
                        $data = xhprof_disable();
                        $runs = new XHProfRuns_Default();
                        $runs->save_run($data, $app_name);
                    });
                }
            }
        }
    }
}
