<?php

namespace Povils\PHPMND;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Extension\DefaultExtension;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Visitor\DetectorVisitor;
use Povils\PHPMND\Visitor\ParentConnectorVisitor;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Detector
 *
 * @package Povils\PHPMND
 */
class Detector
{
    /**
     * @param Option $option
     */
    public function __construct(Option $option)
    {
        $this->option = $option;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return FileReport
     */
    public function detect(SplFileInfo $file)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $fileReport = new FileReport($file);

        $traverser->addVisitor(new ParentConnectorVisitor());
        $traverser->addVisitor(new DetectorVisitor($fileReport, $this->option));

        $stmts = $parser->parse($file->getContents());
        $traverser->traverse($stmts);

        return $fileReport;
    }
}
