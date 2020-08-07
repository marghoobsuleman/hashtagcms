<?php

namespace MarghoobSuleman\HashtagCms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CmsModuleModelCommand extends Command
{

    use Common;

    protected $name;
    protected $methods = array();

    protected $files;

    protected $currentSourceFile;

    private $sourceDir = 'hashtagcms/cmsmodule/model';
    private $sourceFile = 'index.ms';
    private $targetTempDir = 'storage/temp';
    private $targetDir = 'app/Models';

    private $paths = array(
        "sourceDir"=>"hashtagcms/cmsmodule",
        "sourceFile"=>"index.ms",
        "tempDir"=>"storage/temp",
        "targetDir"=>"Models",
        "vendor"=>"vendor/hashtagcms"
    );

    private $hasLangScope = array();


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:model
                            {name : name of the model}
                            {methods? : methods as methodName,Relation,DataSource,useLangScope~}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS]: Create Admin Model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->name = $this->argument("name");
        $methods = $this->argument("methods");

        $this->name = Str::title($this->name);

        $this->createModel($this->name, $methods);

    }

    /**
     * @param $name
     * @param string $methods
     */
    private function createModel($name, $methods="", $fixModelName=true) {

        $name = ($fixModelName) ? Str::title($name) : $name;

        $isExists = $this->isModelExists($name);

        if(!$isExists) {

            $this->alert("Creating Model $name");

            //Let's check if there is any relation/method - separated with ~
            if(empty($methods)) {
                $this->methods[$name] = NULL;
            } else {
                $this->methods[$name] = ($methods=="") ? "" : explode("~", $methods);
            }

            $fileName = $this->init("model");

            $this->replaceModelContext($name, $fileName, $fixModelName);

            $this->clean($fileName);

            $this->info("'$name' created successfully.");

            $this->line("--------------------------------");

            $this->createExtraModels($name);


        } else {

            $this->error("$name model already exists");

        }


    }

    /**
     * @param $name
     * @param $currentFileName
     */

    private function replaceModelContext($name, $currentFileName, $fixModelName=true) {
        $model_name = $name;

        $filename = $currentFileName;

        $patterns = array();
        $patterns["namespace"] = '/{{namespace}}/';
        $patterns["model"] = '/{{model}}/';
        $patterns["relationMethods"] = '/{{relationMethods}}/';
        $patterns["useModels"] = '/{{useModels}}/';
        $patterns["useLangScope"] = '/{{useLangScope}}/';
        $patterns["langScopeBoot"] = '/{{langScopeBoot}}/';


        $replacements = array();

        $replacements["namespace"] = $this->laravel->getNamespace();
        $replacements["model"] = $model_name;

        $relationData = $this->getRelationData($model_name, $fixModelName);

        $replacements["relationMethods"] = $relationData["useMethods"];
        $replacements["useModels"] = $relationData["useModels"];

        $replacements["useLangScope"] = '';
        $replacements["langScopeBoot"] = '';

        $landData = $this->getLangScope($model_name);

        $replacements["useLangScope"] = $landData["useScope"];
        $replacements["langScopeBoot"] = $landData["useMethod"];




        $replaced = preg_replace(
            $patterns,
            $replacements,
            file_get_contents($filename)
        );


        file_put_contents(
            $filename,
            $replaced,
            FILE_BINARY
        );


        $targetFileName = $this->getValidTarget($this->paths['targetDir'].'/'.$model_name.".php", 'app');
        $this->files->copy($filename, $targetFileName);

    }


    /**
     * @param $name
     * @return mixed
     */
    private function getRelationData($name, $fixModelName=true) {
        $methods = $this->methods[$name] ?? NULL;

        $namespace = $this->laravel->getNamespace();

        $useModels = "";
        $useMethods = "";

        $extraModels = [];

        if($methods!=NULL) {
            foreach ($methods as $key=>$val) {
                $current = explode(",", $val);
                $method = $current[0];
                $relation = $current[1];
                $source = $current[2];
                $hasLangScope = $current[3] ?? FALSE;

                $dataSource = $source;

                $useMethods .= "\n
    public function $method() {
        return \$this->$relation($dataSource::class);
    }\n";

                $useModels .= "use $namespace\Models\\".$source.";\n";

                //extra models
                $extraModels[] = $source;

                //Add for relation model
                if($hasLangScope!=FALSE){
                    $this->setLangScope($source);
                }

            }
        }

        $data["useModels"] = $useModels;
        $data["useMethods"] = $useMethods;
        $data["extraModels"] = $extraModels;

        return $data;
    }

    /**
     * @param $name
     */
    private function createExtraModels($name) {

        $relationData = $this->getRelationData($name);
        $models = $relationData["extraModels"];
        if(sizeof($models)>0) {
            foreach ($models as $key=>$val) {
                $modelName = $val;
                if(!$this->isModelExists($modelName)) {
                    $this->createModel($modelName, "", false);
                }

            }
        }
    }


    /**
     * Set Lang Scope
     * @param $model
     */
    private function setLangScope($model) {
        $model = strtolower($model);
        $this->hasLangScope[$model] = $model;
    }

    /**
     * Get Lang Scope
     * @param $model
     * @return mixed
     */
    private function getLangScope($model) {
        $model = strtolower($model);
        $data["useScope"] = "";
        $data["useMethod"] = "";

        if(isset($this->hasLangScope[$model])) {
            $data["useScope"] = "use MarghoobSuleman\HashtagCms\Core\Scopes\LangScope;";
            $data["useMethod"] = "protected static function boot() {

        parent::boot();
        static::addGlobalScope(new LangScope);

    }";
        }

        return $data;
    }



}


