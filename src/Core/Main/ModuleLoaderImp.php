<?php

namespace MarghoobSuleman\HashtagCms\Core\Main;

interface ModuleLoaderImp
{
    public function getResult():array;
    public function setResult(array $data):void;
}