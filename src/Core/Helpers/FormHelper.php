<?php

class FormHelper
{
    protected static $supportedTag = ['input' => ['self_closed' => 1],
        'textarea' => ['self_closed' => 0],
        'label' => ['self_closed' => 0],
        'file' => ['self_closed' => 1],
        'checkbox' => ['self_closed' => 1, 'selected' => 'checked'],
        'radio' => ['self_closed' => 1, 'selected' => 'checked'],
    ];

    /**
     * Create Input Text
     *
     * @param  string  $type
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public static function input($type = 'text', $name = '', $value = '', $attributes = [], $selected = '')
    {

        $arr['name'] = $name;
        $arr['id'] = isset($attributes['id']) ? $attributes['id'] : $name;
        $arr['type'] = $type;
        try {
            $arr['value'] = htmlentities($value, ENT_QUOTES);
        } catch (\Exception $e) {
            $arr['value'] = $value;
        }

        switch ($type) {
            case 'checkbox':
                if ($value == 1) {
                    $arr['checked'] = self::$supportedTag['checkbox']['selected'];
                }
                $arr['onClick'] = 'this.value = (this.checked==true) ? 1 : 0';
                break;
            case 'radio':
                if ($value == $selected) {
                    $arr['checked'] = self::$supportedTag['radio']['selected'];
                }
                break;
        }

        return self::makeTag('input', $arr, $attributes, $selected);

    }

    /**
     * Create Label Tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public static function label($name = '', $value = '', $attributes = [])
    {

        $arr['name'] = $name;
        $arr['value'] = $value;
        $arr['for'] = $name;

        return self::makeTag('label', $arr, $attributes);
    }

    /**
     * Create Password Tag
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array  $attributes
     * @return string
     */
    public static function password($name = '', $value = '', $attributes = [])
    {

        return self::input('password', $name, $value, $attributes);

    }

    public static function checkbox($name = '', $value = 0, $attributes = [])
    {
        return self::input('checkbox', $name, $value, $attributes);

    }

    /**
     * @param  string  $name
     * @param  array  $attributes
     * @return string
     */
    public static function file($name = '', $value = '', $attributes = [], $showPreview = false, $maxHeight = null, $maxWidth = null, $innerClass = '', $isDeletable = true)
    {

        $html[] = self::input('file', $name, '', $attributes);
        $originalFileName = $value;

        if ($showPreview && trim($value) !== '') {

            $value = (filter_var($value, FILTER_VALIDATE_URL)) ? $value : htcms_get_media($value);

            $imageSupportedByBrowsers = htcms_admin_config('imageSupportedByBrowsers', true);
            $fileInfo = pathinfo($value);
            $extension = $fileInfo['extension'] ?? '';
            $isImage = (in_array($extension, $imageSupportedByBrowsers));

            $imgWidth = ($maxWidth != null) ? " width='$maxWidth' " : '';
            $imgHeight = ($maxHeight != null) ? " height='$maxHeight' " : '';

            $maxHeight = ($maxHeight == null) ? 'max-height:100px;' : "max-height:{$maxHeight}px;";
            $maxWidth = ($maxWidth == null) ? '' : "max-width:{$maxWidth}px;";

            if ($isImage) {
                $tag = "<a target='_blank' href='{$value}'><img $imgHeight $imgWidth src='$value' /></a>";
            } else {
                $tag = "<i class='fa fa-paperclip text-danger'></i>&nbsp;<a target='_blank' href='{$value}'>{$fileInfo['filename']}.{$extension}</a>";
            }

            $deleteIcon = '';

            if ($isDeletable) {
                $deleteIcon = "&nbsp;<i style='float:left; margin-right: 10px;' title='Delete' class='fa fa-trash-o hand' onclick='document.getElementById(\"__hashtagcms_{$name}__\").style.display=\"none\";document.getElementById(\"{$name}_deleted\").value=\"{$originalFileName}\"'></i>";
            }

            $html[] = "<div id='__hashtagcms_{$name}__' style='{$maxHeight}{$maxWidth};margin-top:10px;'>
                                        <div class='col-sm-9 card;{$innerClass}'>
                                            $tag $deleteIcon
                                        </div>
                                    </div>";
        }

        $html[] = "<input type='hidden' name='{$name}_deleted' id='{$name}_deleted' value='0' />";

        return implode('', $html);

    }

    public static function textarea($name = '', $value = '', $attributes = [])
    {
        $arr['name'] = $name;
        $arr['id'] = isset($attributes['id']) ? $attributes['id'] : $name;
        $arr['value'] = ($value == '') ? '' : $value;
        $arr['rows'] = (isset($attributes['rows'])) ? $attributes['rows'] : 4; //default

        return self::makeTag('textarea', $arr, $attributes);

    }

    public static function safeValue($value, $defaultValue = '')
    {
        return $value ?? $defaultValue;
    }

    /**
     * Create SELECT element
     *
     * @param  array  $attributes
     * @param  string  $selected
     * @param  array  $keyValue
     * @return string
     */
    public static function select($name,
        $options,
        $attributes = null,
        $selected = '',
        $keyValue = null, $prependSelect = null)
    {

        $attributes = ($attributes == null) ? [] : $attributes;
        $keyValue = ($keyValue == null) ? ['value' => 'id', 'label' => 'name'] : $keyValue;

        //dd("options ", $options);
        $array[] = "<select name='$name' ";

        foreach ($attributes as $key => $attribute) {
            $array[] = "$key='$attribute'  ";
        }

        $isMuliple = false;

        if (Str::contains($name, '[]')) {
            $array[] = " multiple='multiple' ";
            $isMuliple = true;
        }
        if (! isset($attributes['id'])) {
            $array[] = " id = '$name'";
        }

        $array[] = '>';

        if ($isMuliple == false) {
            $prependOption = ($prependSelect === null) ? 'Select ' : $prependSelect;

            if ($prependOption != '') {
                $array[] = "<option value=''>$prependOption</option>";
            }

        }

        //dd($options);
        // print_r($options);
        foreach ($options as $value => $row) {

            if ($keyValue == 'plain_array') {
                $optValue = $row;
                $optLabel = ucfirst($row);

            } else {

                $row = (is_array($row)) ? $row : $row->toArray();

                $optValue = addslashes($row[$keyValue['value']]);

                $parentStr = (isset($row['parent_id']) && $row['parent_id'] > 0) ? '&nbsp;&nbsp;&nbsp;&nbsp;' : ''; //add some space for child

                if (strpos($keyValue['label'], '.') > 0) {
                    //something like: row->lang[array("name"=>value)];
                    $tmp = explode('.', $keyValue['label']);

                    $optLabel = $parentStr.''.$row[$tmp[0]][$tmp[1]];
                } else {
                    $optLabel = $parentStr.''.$row[$keyValue['label']];
                }
            }

            $isSelected = '';
            if ($isMuliple == false) {
                $isSelected = ($optValue == $selected && $selected != '') ? "selected='selected' " : '';
            } else {
                $isSelected = (in_array($optValue, ($selected ?? []))) ? "selected='selected' " : '';
            }

            $array[] = "<option $isSelected value='$optValue'>$optLabel</option>";
        }

        $array[] = '</select>';

        return implode('', $array);
    }

    /**
     * Make Tags
     *
     * @param  string  $tagName
     * @param  array  $requiredAttributes
     * @param  array  $attributes
     * @return string
     */
    private static function makeTag($tagName = '', $requiredAttributes = [], $attributes = [])
    {

        $array = [];
        $array[] = "<$tagName";

        $attributes = array_merge($requiredAttributes, $attributes);

        if (isset(self::$supportedTag[$tagName]) && self::$supportedTag[$tagName]['self_closed'] === 0) {
            $value = $attributes['value'];
            unset($attributes['value']);
        }

        $hasId = false;
        foreach ($attributes as $key => $attribute) {

            $array[] = $key."='".$attribute."'";
        }

        if (isset(self::$supportedTag[$tagName]) && self::$supportedTag[$tagName]['self_closed'] === 0) {
            $array[] = '>';
        }

        $array[] = (! isset(self::$supportedTag[$tagName]) || self::$supportedTag[$tagName]['self_closed'] === 1) ? '/>' : "$value</$tagName>";

        return implode(' ', $array);

    }
}
