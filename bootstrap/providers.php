<?php
use Illuminate\Support\ServiceProvider;
use MarghoobSuleman\HashtagCms\HashtagCmsServiceProvider;

ServiceProvider::addProviderToBootstrapFile(HashtagCmsServiceProvider::class);
