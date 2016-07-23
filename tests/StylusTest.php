<?php

use NodejsPhpFallback\Stylus;

class StylusTest extends PHPUnit_Framework_TestCase
{
    public function testGetStylusFromRaw()
    {
        $expected = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";
        $stylus = new Stylus($expected);
        $stylus = trim($stylus->getStylus());

        $this->assertSame($expected, $stylus, 'Stylus can be get as it with a raw input.');
    }

    public function testGetStylusFromPath()
    {
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $stylus = trim(str_replace("\r", '', $stylus->getStylus()));
        $expected = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";

        $this->assertSame($expected, $stylus, 'Stylus can be get with a file path input too.');
    }

    public function testGetCss()
    {
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $css = trim($stylus);
        $expected = "body {\n" .
            "  color: #f00;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }

    public function testWrite()
    {
        $file = sys_get_temp_dir() . '/test.css';
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $stylus->write($file);
        $css = trim(file_get_contents($file));
        unlink($file);
        $expected = "body {\n" .
            "  color: #f00;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }
}
