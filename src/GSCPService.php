<?php
namespace GSCP;

use Exception;

class GSCPService
{
    use GSCPSettersTrait;
    use GSCPDownloadTrait;

    private const DEPTH = 6;

    /** @var string */
    private $filename;

    /** @var string */
    private $locale;

    /** @var array */
    private $categories;

    /** @var string[] */
    private $columns;

    public function __construct(array $options = [])
    {
        $this->filename = $options['filename'] ?? 'gscp.txt';
        $this->locale = $options['locale'] ?? 'en-US';
        $this->columns = $options['columns'] ?? [
            'id',
            'name',
            'parentId',
            'parents',
            'children',
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->getCategories();

        $this->parse();
        if (in_array('children', $this->columns, true)) {
            $this->addChildren();
        }
        if (in_array('parents', $this->columns, true)) {
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
        for ($depth = 0; $depth < self::DEPTH; $depth++) {
            foreach ($this->categories as $id => $category) {
                if (count($category) !== $depth + 1) {
                    continue;
                }

                $parentId = 0;
                $parentCategoryDepth = $depth - 1;
                if ($parentCategoryDepth >= 0) {
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

            $item['parents'] = $parents;
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
        $this->download();
        $content = (string) file_get_contents($this->filename);
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
