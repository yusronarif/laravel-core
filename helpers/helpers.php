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
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Yusronarif\Core\Support\Str;

if (!function_exists('f')) {
    /**
     * @param string $text
     * @return string
     */
    function f(string $text = '')
    {
        return stripslashes(nl2br($text));
    }
}

if (!function_exists('pretty_size')) {
    /**
     * Human readable file size.
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    function pretty_size(int $bytes, int $decimals = 2)
    {
        $sz = 'BKMGTPE';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).$sz[$factor];
    }
}

if (! function_exists('setDefaultRequest')) {
    /**
     * Set Default Value for Request Input
     *
     * @param string|array $name
     * @param null $value
     */
    function setDefaultRequest($name, $value = null) {
        $request = request();

        if (is_array($name)) {
            foreach ($name as $key => $val) {
                if (!empty($key) && !empty($val)) {
                    setDefaultRequest($key, $val);
                }
            }
        }
        else {
            if (!$request->session()->hasOldInput($name)) {
                $request->session()->flash('_old_input.' . $name, $value);
            }

            if (strpos($name, '.', 1) > 0) {
                $names = [];
                Arr::set($names, $name, $value);
                $request->request->set(key($names), Arr::first($names));
            } else $request->request->set($name, $value);
        }
    }
}

if (!function_exists('fromResource')) {
    /**
     * Generate an collection from resource.
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource $resource
     * @return mixed
     */
    function fromResource(JsonResource $resource)
    {
        return json_decode(json_encode($resource));
    }
}

if (!function_exists('avatar')) {
    /**
     * Avatar Generator Helper.
     *
     * @param string $imagePath
     * @return string
     */
    function avatar(string $imagePath)
    {
        $path = preg_replace('/(\/)+$/i', '', config('arkid.app.avatar_path', 'assets/img/avatar'));

        $avatar = asset($path.'/'.$imagePath);

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
     * @return string
     */
    function vendor(string $path)
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
     * @param string $path
     * @return string
     */
    function document(string $path)
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
     * @param string $name
     * @param string $base
     * @param array|null $type
     * @return bool
     */
    function plugins(string $name, string $base = 'vendor', array $type = null)
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
    function is_dev(string $is_true = '', string $is_false = '')
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
    function has_route($name, array $parameters = [], bool $absolute = true)
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
    function routed($name, array $parameters = [], bool $absolute = true)
    {
        if (app('route')::has($name)) {
            return app('url')->route($name, $parameters, $absolute);
        }

        return '#';
    }
}

if (!function_exists('money_format')) {
    /*
    That it is an implementation of the function money_format for the
    platforms that do not it bear.

    The function accepts to same string of format accepts for the
    original function of the PHP.

    (Sorry. my writing in English is very bad)

    The function is tested using PHP 5.1.4 in Windows XP
    and Apache WebServer.
    */
    function money_format($format, $number) {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format(
                $value,
                $right,
                $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']
            );
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad(
                    $value,
                    $width,
                    $flags['fillchar'],
                    $flags['isleft'] ?
                        STR_PAD_RIGHT : STR_PAD_LEFT
                );
            }

            $format = str_replace($fmatch[0], $value, $format);
        }

        return $format;
    }
}

if (!function_exists('currency_format')) {
    /*
    That it is an implementation of the function money_format for the
    platforms that do not it bear.

    The function accepts to same string of format accepts for the
    original function of the PHP.

    (Sorry. my writing in English is very bad)

    The function is tested using PHP 5.1.4 in Windows XP
    and Apache WebServer.
    */
    function currency_format($format, $number)
    {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : 0;
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? Chr(32) : '';

            $value = number_format(
                $value,
                $right,
                $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']
            );
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad(
                    $value,
                    $width,
                    $flags['fillchar'],
                    $flags['isleft'] ?
                        STR_PAD_RIGHT : STR_PAD_LEFT
                );
            }

            $format = str_replace($fmatch[0], $value, $format);
        }

        return $format;
    }
}

if (!function_exists('get_raw_sql')) {
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return Generator
     */
    function get_raw_sql($query)
    {
        return Str::replaceArray('?', $query->getBindings(), $query->toSql());
    }
}
