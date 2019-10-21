<?php

if(! defined('SPACER')) define('SPACER', '&nbsp;&nbsp;&nbsp;&nbsp;');
if(! defined('SPACER_TIGHT')) define('SPACER_TIGHT', '&nbsp;&nbsp;');
if(! defined('SPACER_WIDE')) define('SPACER_WIDE', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

// date format
if(! defined('DATE_SHORT')) define('DATE_SHORT', "L");
if(! defined('DATE_MEDIUM')) define('DATE_MEDIUM', "ll");
if(! defined('DATE_LONG')) define('DATE_LONG', "LL");
if(! defined('DATE_FULL')) define('DATE_FULL', "LLL");
if(! defined('DATE_FULL_SHORT')) define('DATE_FULL_SHORT', "lll");

use Carbon\Carbon;

if (!function_exists('f')) {
    function f($string = '')
    {
        return stripslashes(nl2br($string));
    }
}

if (!function_exists('pretty_size')) {
    /**
     * Returns a human readable file size.
     *
     * @param int $bytes
     *                      Bytes contains the size of the bytes to convert
     * @param int $decimals
     *                      Number of decimal places to be returned
     *
     * @return string a string in human readable format
     *
     * */
    function pretty_size($bytes, $decimals = 2)
    {
        $sz = 'BKMGTPE';
        $factor = (int) floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).$sz[$factor];
    }
}

if (!function_exists('fromResource')) {
    /**
     * Generate an collection from resource.
     *
     * @param $resource
     *
     * @return mixed|object
     */
    function fromResource($resource)
    {
        return json_decode(json_encode($resource));
    }
}

if (!function_exists('avatar')) {
    /**
     * Avatar Generator Helper.
     *
     * @param $img
     *
     * @return string
     */
    function avatar($img)
    {
        $path = preg_replace('/(\/)+$/i', '', config('arkid.app.avatar_path', 'assets/img/avatar'));

        $avatar = asset($path.'/'.$img);

        /*print_r(@get_headers($avatar));
        echo PHP_EOL;
        print_r(@get_headers($avatar)[0]);
        echo PHP_EOL;
        if(strstr(@get_headers($avatar)[0], '200')) {
            echo 'found';
        }
        else echo 'not found';
        echo PHP_EOL;*/

        stream_context_set_default([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        if (!strstr(@get_headers($avatar)[0], '200')) {
            $avatar = vendor('assets/img/_blank_avatar.png');
        }

        return $avatar;
    }
}

if (!function_exists('vendor')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     *
     * @return string
     */
    function vendor($path)
    {
        $vendorPath = config('app.vendor_url') ?? '';
        $vendorPath = $vendorPath !== '' ? $vendorPath : asset('vendor');

        if (preg_match('/(:\/\/)+/i', $path, $matches, PREG_UNMATCHED_AS_NULL, 1)) {
            $replacedCount = 0;
            $pattern = '/^(vendor:\/\/)/i';
            $path = preg_replace($pattern, '', $path, -1, $replacedCount);
            if ($replacedCount > 0) {
                $vendorPath .= '/assets';
            }

            $replacedCount = 0;
            $pattern = '/^(asset:\/\/)/i';
            $path = preg_replace($pattern, '', $path, -1, $replacedCount);
            if ($replacedCount > 0) {
                $vendorPath = asset('');
            }
        }

        if (is_dev() && preg_match('/(app)((\.min)?\.css)$/i', $path)) {
            $path = preg_replace('/(app)((\.min)?\.css)$/i', '$1-dev$2', $path);
        }

        return $vendorPath.'/'.$path;
    }
}

if (!function_exists('document')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     *
     * @return string
     */
    function document($path)
    {
        $root = config('app.document_url');
        $root = $root !== '' ? $root : asset('files');

        return $root.'/'.$path;
    }
}

if (!function_exists('plugins')) {
    /**
     * Retrive Application Plugins.
     * retriving from config's definitions.
     *
     * @param string     $name
     * @param mixed|path $base
     * @param mixed      $type
     *
     * @return mixed
     */
    function plugins($name, $base = 'vendor', $type = null)
    {
        if (!$name) {
            return false;
        }
        if (!in_array($base, ['vendor', 'local'])) {
            return false;
        }

        if (!is_array($name)) {
            $name = [$name];
        }
        if (!$type) {
            $type = ['css', 'js'];
        }
        if (!is_array($type)) {
            $type = [$type];
        }
        sort($type);

        $rs = [];

        foreach ($name as $packKey => $packVal) {
            if (is_array($packVal)) {
                $rs = array_merge_recursive($rs, plugin_assets($packKey, $base, $type));

                foreach ($packVal as $pkey => $pval) {
                    $rs = array_merge_recursive($rs, plugin_assets($pval, $base, $type, $packKey.'.'.$pkey.'.'));
                }
            } else {
                $rs = array_merge_recursive($rs, plugin_assets($packVal, $base, $type));
            }
        }

        if (is_array($rs['css'])) {
            $rs['css'] = implode('', $rs['css']);
        }
        if (is_array($rs['js'])) {
            $rs['js'] = implode('', $rs['js']);
        }

        return View::share(['pluginCss' => $rs['css'], 'pluginJs' => $rs['js']]);
    }
}

if (!function_exists('plugin_assets')) {
    /**
     * Retrive Application Plugins's Assets.
     * retriving from config's definitions.
     *
     * @param string     $name
     * @param mixed|path $base
     * @param mixed      $type
     *
     * @return mixed
     */
    function plugin_assets($names, $base = 'vendor', $type = null, $parent = '')
    {
        if (!is_array($names)) {
            $names = [$names];
        }

        $localPath = 'plugins/';
        $package = 'arkid.plugins.'.$parent;
        $httpPattern = '/^(http[s?]:)/i';

        $rs = [];
        foreach ($names as $name) {
            foreach ($type as $t) {
                $rs[$t] = '';
                if (config()->has($package.$name.'.'.$t)) {
                    foreach (config($package.$name.'.'.$t) as $file) {
                        if ($t === 'css') {
                            if (preg_match($httpPattern, $file)) {
                                $src = $file;
                            } else {
                                if ($base === 'vendor') {
                                    $src = vendor($file);
                                } else {
                                    $src = asset($localPath.$file);
                                }
                            }

                            $rs[$t] .= '<link href="'.$src.'" rel="stylesheet">';
                        }

                        if ($t === 'js') {
                            if (preg_match($httpPattern, $file)) {
                                $src = $file;
                            } else {
                                if ($base === 'vendor') {
                                    $src = vendor($file);
                                } else {
                                    $src = asset($localPath.$file);
                                }
                            }

                            $rs[$t] .= '<script src="'.$src.'"></script>';
                        }

                        unset($src);
                    }
                }

                if ($lgc = config($package.$name.'.legacy')) {
                    $rs[$t] .= $lgc['condition'][0];
                    foreach ($lgc['src'] as $file) {
                        if (preg_match($httpPattern, $file)) {
                            $src = $file;
                        } else {
                            if ($base === 'vendor') {
                                $src = vendor($file);
                            } else {
                                $src = asset($localPath.$file);
                            }
                        }

                        $rs[$t] .= '<script src="'.$src.'"></script>';
                        unset($src);
                    }
                    $rs[$t] .= $lgc['condition'][1];
                }
            }
        }

        return $rs;
    }
}

if (!function_exists('carbon')) {
    /**
     * Carbon helper.
     *
     * @param null|datetime $datetime
     *
     * @return Carbon|null
     */
    function carbon($datetime = null)
    {
        if (!$datetime) {
            $datetime = Carbon::now();
        } else {
            $datetime = Carbon::parse($datetime);
        }

        return $datetime;
    }
}

if (!function_exists('is_dev')) {
    /**
     * Development Mode Checker.
     *
     * @param string $is_true
     * @param string $is_false
     *
     * @return bool|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|string
     */
    function is_dev($is_true = '', $is_false = '')
    {
        if (session('dev_mode') != null) {
            $return = session('dev_mode');
        } else {
            $return = false;
            $dev = env('APP_DEVMODE', 'off');

            if ($dev && in_array(strtolower($dev), ['true', '1', 'on'])) {
                $return = true;
            }
        }

        if ($is_true) {
            if ($return) {
                return $is_true;
            } else {
                return $is_false;
            }
        }

        return $return;
    }
}

if (!function_exists('has_route')) {
    /**
     * Existing Route by Name.
     *
     * @param $name
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return bool
     */
    function has_route($name, $parameters = [], $absolute = true)
    {
        return app('route')::has($name);
    }
}

if (!function_exists('routed')) {
    /**
     * Existing Route by Name
     * with '#' fallback.
     *
     * @param $name
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return mixed
     */
    function routed($name, $parameters = [], $absolute = true)
    {
        if (app('route')::has($name)) {
            return app('url')->route($name, $parameters, $absolute);
        }

        return '#';
    }
}
