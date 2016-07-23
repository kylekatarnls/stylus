<?php

namespace NodejsPhpFallback;

use Stylus\Stylus as PhpStylusEngine;

class Stylus
{
    protected $path;
    protected $contents;
    protected $compress;
    protected $node;
    protected $stylus;

    public function __construct($file, $compress = false)
    {
        $key = file_exists($file) ? 'path' : 'contents';
        $this->$key = $file;
        $this->node = new NodejsPhpFallback();
        $this->stylus = new PhpStylusEngine();
        $this->compress = $compress;
    }

    protected function stylusExec($arguments, $fallback)
    {
        return $this->node->execModuleScript(
            'stylus',
            'bin/stylus',
            ($this->compress ? '-c ' : '') . $arguments,
            $fallback
        );
    }

    protected function getTempPath()
    {
        return sys_get_temp_dir() . '/stylus.compilation';
    }

    protected function getPath()
    {
        return isset($this->path)
            ? $this->path
            : $this->getTempPath();
    }

    protected function cleanTempFiles()
    {
        if (!isset($this->path)) {
            unlink($this->getTempPath());
        }
    }

    protected function toString()
    {
        return $this->stylus->toString();
    }

    protected function toFile()
    {
        return $this->stylus->toString($this->getPath());
    }

    public function getStylus()
    {
        return isset($this->contents)
            ? $this->contents
            : file_get_contents($this->path);
    }

    public function getCss()
    {
        $css = $this->stylusExec('--print ' . escapeshellarg($this->getPath()), array($this, 'toString'));
        $this->cleanTempFiles();

        return $css;
    }

    public function write($file)
    {
        $path = isset($this->path)
            ? $this->path
            : sys_get_temp_dir() . '/stylus.compilation';
        $this->stylusExec(' < ' . escapeshellarg($path) . ' > ' . escapeshellarg($file), array($this, 'toFile'));
        $this->cleanTempFiles();
    }

    public function __toString()
    {
        return $this->getCss();
    }
}
