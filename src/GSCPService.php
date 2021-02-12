<?php
namespace App\Services;

use Exception;

class GSCPService
{
    private const URL = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.%locale%.txt';
    private const DEPTH = 6;

    /** @var string */
    private $locale = 'en-US';

    /** @var array */
    private $categories;

    /** @var string[] */
    private $columns = [
        'id',
        'name',
        'parentId',
        'parents',
        'children',
    ];

    /**
     * @param string $locale
     * @return self
     */
    public function setLocale(string $locale): self
    {
        $this->locale = str_replace('_', '-', $locale);
        return $this;
    }

    /**
     * @param array $columns
     * @return self
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->getCategories();

        $this->parse();
        if (in_array('children', $this->columns)) {
            $this->addChildren();
        }
        if (in_array('parents', $this->columns)) {
            $this->addParents();
        }
        $this->format();

        return $this->categories;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    private function parse(): void
    {
        $parsedCategories = [];
        for ($depth=0; $depth<self::DEPTH; $depth++) {
            foreach ($this->categories as $id => $category) {
                if(count($category) !== $depth + 1) {
                    continue;
                }

                $parentId = 0;
                $parentCategoryDepth = $depth-1;
                if($parentCategoryDepth >= 0) {
                    foreach ($parsedCategories as $item) {
                        if ($item['name'] === $category[$parentCategoryDepth]) {
                            $parentId = $item['id'];
                            break;
                        }
                    }
                }

                $parsedCategories[$id] = [
                    'id' => $id,
                    'parentId' => $parentId,
                    'parents' => [],
                    'children' => [],
                    'name' => $category[$depth]
                ];
            }
        }

        $this->categories = $parsedCategories;
    }

    private function addChildren(): void
    {
        foreach ($this->categories as $id => &$category) {
            $children = [];

            foreach ($this->categories as $row) {
                if ($row['parentId'] === $id) {
                    $children[] = $row['id'];
                }
            }

            $category['children'] = $children;
        }
    }

    private function addParents(): void
    {
        foreach ($this->categories as &$item) {

            $parents = [];
            $parentId = $item['parentId'];
            while ($parentId !== 0) {
                $parents[] = (int) $parentId;
                $parentId = $this->categories[$parentId]['parentId'];
            }

            $item['parents'] =  $parents;
        }
    }

    private function format(): void
    {
        $formattedCategories = [];
        foreach ($this->categories as $id => $category) {
            $formattedCategory = [];
            foreach ($this->columns as $column) {
                $formattedCategory[$column] = $category[$column];
            }
            $formattedCategories[$id] = $formattedCategory;
        }
        $this->categories = $formattedCategories;
    }

    private function getCategories(): void
    {
        $url = str_replace('%locale%', $this->getLocale(), self::URL);
        $content = (string) file_get_contents($url);
        $list = explode(PHP_EOL, $content);

        $this->categories = [];

        foreach ($list as $row) {
            if ('' === $row) {
                continue;
            }

            if ('#' === $row[0]) {
                continue;
            }

            [$id, $text] = explode(' - ', $row);

            $this->categories[(int) $id] = explode(' > ', trim($text));
        }
    }

    private function getLocale(): string
    {
        if (!preg_match('/^[a-z]{2}-[A-Z]{2}$/i', $this->locale)) {
            throw new Exception('Please setup correct locale');
        }
        return $this->locale;
    }
}
