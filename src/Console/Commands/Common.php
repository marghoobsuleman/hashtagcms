<?php

namespace MarghoobSuleman\HashtagCms\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Common {

    /**
     * Get source file name
     * @param $name
     * @return string
     */
    protected function getValidSourceFileName($name) {
        return __DIR__."/../../../".$name;
    }

    /**
     * @param string $path
     * @param string $type
     * @return string
     */
    protected function getValidTarget($path='', $type='app') {
        if($type == 'app') {
            return $this->laravel['path'].'/'.$path;
        } else {
            return base_path($path);
        }
    }

    /**
     * Is Admin Controller Exists
     * @param $name
     * @return mixed
     */
    protected function isAdminControllerExists($name) {

        $path = $this->laravel['path'];
        $file_name = $path.'/Http/Controllers/Admin/'.Str::title($name).'Controller.php';

        return $this->files->exists($file_name);
    }

    /**
     * Is Model Exists
     * @param $name
     * @return mixed
     */
    protected function isModelExists($name) {
        $path = $this->laravel['path'];
        $file_name = $path.'/Models/' . Str::title($name) . '.php';
        return $this->files->exists($file_name);
    }

    /**
     * Confirm Message
     * @param $question
     * @return string
     */
    protected function confirmMessage($question) {
        $answer = $this->confirm($question);
        $answer = ($answer == 1) ? "Yes" : "No";
        $this->warn("You said $answer");
        return $answer;
    }

    /**
     * Delete temp file
     * @param $fileName
     */
    protected function clean($fileName) {
        //delete old files
        unlink($fileName);
    }

    /**
     * Create folder etc
     * @param string $what
     * @return string
     */
    protected function init($what="model") {

        $targetTemp = $this->getValidTarget($this->paths['tempDir'], 'base');
        $targetDir = $this->getValidTarget($this->paths['targetDir'], 'app');

        //Create temp dir
        if (! $this->files->isDirectory($targetTemp)) {
            $this->files->makeDirectory($targetTemp, 0777, true, true);
        }

        //Model/Controller Directory
        if (! $this->files->isDirectory($targetDir)) {
            $this->files->makeDirectory($targetDir);
        }

        $sourceFile = $this->getValidSourceFileName($this->paths['sourceDir']."/$what/".$this->paths['sourceFile']);
        $this->currentSourceFile = $tagetTempFile = $targetTemp.'/'.md5($sourceFile."".date("YY-DD-M H:i:s")).".ms";
        $this->files->copy($sourceFile, $tagetTempFile);

        return $tagetTempFile;
    }

    /******* Validator Fields ***********/
    /**
     * Get Max Length of a field
     * @param $field
     * @return string
     */
    protected function getMax($field) {
        preg_match ( '/\d{1,}/' , $field, $found, PREG_OFFSET_CAPTURE);

        if(sizeof($found) > 0) {
            return "max:".$found[0][0];
        }
        return "";
    }

    /**
     * Get data type of a field
     * @param $field
     * @return mixed|string
     */
    protected function getDataType($field) {

        preg_match ( '/[a-z].+\(/' , $field, $found, PREG_OFFSET_CAPTURE);

        $typeArray = array("varchar"=>"string",
            "int"=>"numeric",
            "decimal"=>"numeric",
            "bigint"=>"numeric",
            "float"=>"numeric",
            "double"=>"numeric",
            "tinyint"=>"integer",
            "timestamp"=>"date");

        if(sizeof($found) > 0) {
            $key = Str::replaceLast("(", "", $found[0][0]);
            return $typeArray[$key] ?? "";
        }

        return "";


    }

    /**
     * Check if field is required
     * @param $field
     * @return string
     */
    protected function getRequired($field) {

        if($field == "NO") {
            return "required";
        }
        return "";
    }

    /**
     * Get Formatted Fields Value
     * @param $table_name
     * @return array
     */
    protected function getFormattedFieldsValue($table_name) {

        $table_name = strtolower($table_name);

        if(!Schema::hasTable(Str::plural($table_name))) {
            return [];
        }

        $lang = Str::endsWith($table_name, "_langs") ? "lang_" : "";

        $isLang = ($lang == "") ? FALSE : TRUE;

        $data = DB::select("SHOW COLUMNS FROM ".Str::plural($table_name));

        $allFields = [];

        // echo $this->getDataType("int(10) unsigned");
        // echo $this->getDataType("varchar(255)");

        //dd($data);

        foreach ($data as $key=>$fields)   {

            $values = [];
            if($fields->Field != "id") {

                $max = $this->getMax($fields->Type);

                $dataType = $this->getDataType($fields->Type);

                $required = $this->getRequired($fields->Null);

                if($required != "") {
                    $values[] = $required;
                }

                if($max != "" && $dataType != "numeric" && $dataType != "integer") {
                    $values[] = $max;
                }

                if($dataType != "") {
                    $values[] = $dataType;
                }

                $fields_value = join("|", $values);

                if($fields_value != "") {
                    $nullable = (sizeof($values)>=1 && $required=="") ? "nullable|" : "";
                    $allFields[$lang.$fields->Field] = "$nullable".$fields_value;
                }

                if($isLang) {
                    //Dont need parent table id: ie. country_id if table is coutry_langs
                    $table_name_main = Str::singular(str_replace("_langs", "", $table_name));
                    unset($allFields[$lang.$table_name_main."_id"]);
                    unset($allFields["lang_lang_id"]);
                }

            }
        }
        //var_dump($allFields);
        return $allFields;
    }

    /**
     * Get Validation Fields
     * @param string $table_name
     * @param int $withLang
     * @return string
     */
    protected function getValidationFields($table_name='', $withLang=1) {

        $data = $this->getFormattedFieldsValue($table_name);

        $lang_table = Str::singular($table_name)."_langs";

        if($withLang != 0 && Schema::hasTable(Str::plural($lang_table))) {

            $lang_data = $this->getFormattedFieldsValue($lang_table);
            $data = array_merge($data, $lang_data);
        }

        $field_json_str = [];

        if(sizeof($data) > 0 ) {
            foreach ($data as $key=>$val) {
                $field_json_str[] = '"'.$key.'"'.' => "'.$val.'"';
            }

        }
        return join(",
                    ", $field_json_str);

    }

}

