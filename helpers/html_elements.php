<?php

if (!function_exists('button')) {
    function button($type = 'button.primary', $text = '', $properties = '')
    {
        $btnThemes = ['primary', 'secondary', 'info', 'warning', 'danger', 'dark', 'light', 'link'];
        $type = strtolower(trim($type));
        $class = [];
        $btnClass = '';
        $btnHref = '#';
        if (preg_match('/(:\/\/)+/i', $text, $matches, 0, 1)) {
            $btnHref = $text;
            $text = '';
        }
        $btnText = trim($text) ?: 'Button';

        $btnProperties = $properties;

        if (strpos($type, '.', 1)) {
            $class = explode('.', $type);

            $btnType = $class[0];
            array_shift($class);
            $btnClass = 'btn-'.implode(' btn-', $class);
        } else {
            $btnType = $type;
        }

        switch ($btnType) {
            case 'a':
                $btnClass = (array_intersect($class, $btnThemes) ? '' : 'btn-link ').$btnClass;
                $button = sprintf('<a href="%s" class="btn %s" %s>%s</a>', $btnHref, $btnClass, $btnProperties, $btnText);
                break;
            case 'save':
                $btnText = trim($text) ?: __('save');
                $btnClass = (array_intersect($class, $btnThemes) ? '' : 'btn-primary ').$btnClass;
                $button = sprintf('<button type="submit" class="btn %s" %s>%s</button>', $btnClass, $btnProperties, $btnText);
                break;
            case 'cancel':
                $btnText = trim($text) ?: __('cancel');
                $btnClass = (array_intersect($class, $btnThemes) ? '' : 'btn-link ').$btnClass;
                $button = sprintf('<a href="%s" class="btn %s" %s>%s</a>', $btnHref, $btnClass, $btnProperties, $btnText);
                break;
            default:
                $button = sprintf('<button type="%s" class="btn %s" %s>%s</button>', $btnType, $btnClass, $btnProperties, $btnText);
        }

        return $button;
    }
}
