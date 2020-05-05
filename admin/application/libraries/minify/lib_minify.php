<?php

use MatthiasMullie\Minify;

Class lib_minify
{
    private $CI;
    private $UPLOAD_PATH;
    private $CURRENT_HOME;
    private $OGADMIN_HOME;
    private $OGPLAYER_HOME;
    private $CURRENT_UPLOAD_PATH;
    private $OGADMIN_UPLOAD_PATH;
    private $OGPLAYER_UPLOAD_PATH;

    public function __construct()
    {
        $this->CI = &get_instance();

        # Project Folder Path
        $OGHOME = realpath(dirname(__DIR__  . '/../../../../..'));
        $this->OGADMIN_HOME = $OGHOME . '/admin';
        $this->OGPLAYER_HOME = $OGHOME . '/player';

        # Network Drive Folder Path
        $UPLOAD_PATH = $this->CI->utils->getUploadPath();
        $this->UPLOAD_PATH = $UPLOAD_PATH;
        $this->OGADMIN_UPLOAD_PATH  = $UPLOAD_PATH . '/tmp/admin';
        $this->OGPLAYER_UPLOAD_PATH = $UPLOAD_PATH . '/tmp/player';
    }

    public function minify($project)
    {
        $projectList = ['admin', 'player'];

        if (!in_array($project, $projectList)) {
            return;
        }

        $this->init($project);

        $minifyList = $this->CI->config->item('minify_setting');
        $proConfig = $minifyList[$project];

        if ($minifyList && isset($proConfig['css'])) {
            $this->addCssFile($proConfig['css']);
        }
        if ($minifyList && isset($proConfig['js'])) {
            $this->addJsFile($proConfig['js']);
        }
    }

    private function init($project)
    {
        switch ($project) {
            case 'admin' :
                $this->CURRENT_HOME = $this->OGADMIN_HOME;
                $this->CURRENT_UPLOAD_PATH = $this->OGADMIN_UPLOAD_PATH;
                $this->removeTmpFolder();
                $this->createCssJsFolder($this->OGADMIN_UPLOAD_PATH);
            break;
            case 'player':
                $this->CURRENT_HOME = $this->OGPLAYER_HOME;
                $this->CURRENT_UPLOAD_PATH = $this->OGPLAYER_UPLOAD_PATH;
                $this->removeTmpFolder();
                $this->createCssJsFolder($this->OGPLAYER_UPLOAD_PATH);
            break;
        }
    }

    public function removeTmpFolder($UPLOAD_PATH = null)
    {
        if (!$UPLOAD_PATH) {
            $UPLOAD_PATH = $this->UPLOAD_PATH . '/tmp';
        }

        $files = glob($UPLOAD_PATH . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeTmpFolder($file) : unlink($file);
        }

        rmdir($UPLOAD_PATH);
    }

    private function addCssFile($cssList)
    {
        foreach ($cssList as $path) {
            $minifier = new Minify\CSS();

            $filePath = explode('.', $path);
            if ($filePath[count($filePath) -2] == 'min') {
                continue;
            }
            $filePath[count($filePath) -1] = "min.css";
            $tmpfilePath = join('.', $filePath);

            $orgPath = $this->CURRENT_HOME . '/public/';
            $tmpPath = $this->CURRENT_UPLOAD_PATH . '/css/';

            $this->createSubCssJsFolder($tmpPath . $filePath[0]);

            $minifier->add($orgPath . $path);
            $minifier->minify($tmpPath . $tmpfilePath);
        }
    }

    private function addJsFile($jsList)
    {
        foreach ($jsList as $path) {
            $filePath = explode('.', $path);
            if ($filePath[count($filePath) -2] == 'min') {
                continue;
            }
            $filePath[count($filePath) -1] = "min.js";
            $tmpfilePath = join('.', $filePath);

            $orgPath = $this->CURRENT_HOME . '/public/';
            $tmpPath = $this->CURRENT_UPLOAD_PATH . '/js/';

            $this->createSubCssJsFolder($tmpPath . $filePath[0]);
            $this->minifyJS($orgPath . $path, $tmpPath . $tmpfilePath);
        }
    }

    private function createCssJsFolder($tmpPath)
    {
        $pathList[] = $tmpPath . '/js';
        $pathList[] = $tmpPath . '/css';

        foreach ($pathList as $path) {
            if(!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }
    }

    private function createSubCssJsFolder($tmpPath)
    {
        $path = explode('/', $tmpPath);
        array_pop($path);
        $tmpPath = join('/', $path);

        if(!file_exists($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }
    }

    public function minifyJS($file_path, $output_path = NULL){
        $minifier = new Minify\JS();

        if(file_exists($file_path)){
            $content = file_get_contents($file_path);

            $extensions = explode('.', $file_path);
            if ($extensions[count($extensions) -2] == 'min') {
                return $content;
            }
        }else{
            $content = $file_path;
        }

        $minifier->add($content);
        return $minifier->minify($output_path);
    }

    public function minifyCSS($file_path, $output_path = NULL){
        $minifier = new Minify\CSS();

        if(file_exists($file_path)){
            $content = file_get_contents($file_path);

            $extensions = explode('.', $file_path);
            if ($extensions[count($extensions) -2] == 'min') {
                return $content;
            }
        }else{
            $content = $file_path;
        }

        $minifier->add($content);
        return $minifier->minify($output_path);
    }
}