<?php
namespace GSCP;

trait GSCPDownloadTrait
{
    private $url = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.%locale%.txt';

    private function download(): void
    {
        if(!file_exists($this->filename)) {
            $url = str_replace('%locale%', $this->getLocale(), $this->url);
            copy($url, $this->filename);
        }
    }
}