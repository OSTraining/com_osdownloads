<?php
require 'src/site/router.php';

use Codeception\Example;
use Codeception\Util\Stub;

class RouterCest
{
    protected $router;

    public function _before(UnitTester $I)
    {
        global $files;
        global $categories;

        /**
         *
         * Dummy categories
         *
         */
        $categories = [
            1 => (object) [
                'id'        => 1,
                'alias'     => 'category_1',
                'path'      => 'category_1',
                'parent_id' => null,
            ],
            2 => (object) [
                'id'        => 2,
                'alias'     => 'category_2',
                'path'      => 'category_1/category_2',
                'parent_id' => 1,
            ],
            3 => (object) [
                'id'        => 3,
                'alias'     => 'category_3',
                'path'      => 'category_1/category_2/category_3',
                'parent_id' => 2,
            ],
        ];
        // Add index by alias
        $categories['category_1'] = &$categories[1];
        $categories['category_2'] = &$categories[2];
        $categories['category_3'] = &$categories[3];

        /**
         *
         * Dummy files
         *
         */
        $files = [
            1 => (object) [
                'id'          => 1,
                'alias'       => 'file_1',
                'category_id' => 3,
            ],
            2 => (object) [
                'id'          => 2,
                'alias'       => 'file_2',
                'category_id' => 1,
            ],
        ];
        $files['file_1'] = &$files[1];
        $files['file_2'] = &$files[2];

        /**
         *
         * Dummy router
         *
         */
        $container = new class {
            public $helperSEF;
        };

        $container->helperSEF = new class {
            public function appendCategoriesToSegments(&$segments, $catid)
            {
                global $categories;

                $category = $categories[$catid];

                $segments = array_merge($segments, explode('/', $category->path));
            }

            public function getCategoryFromAlias($alias)
            {
                global $categories;

                return $categories[$alias];
            }

            public function getCategoryIdFromFile($alias)
            {
                global $files;

                return $files[$alias]->category_id;
            }

            public function getFileAlias($id)
            {
                global $files;

                return $files[$id]->alias;
            }

            public function getFileIdFromAlias($alias)
            {
                global $files;

                return $files[$alias]->id;
            }
        };

        $this->router  = Stub::make(
            'OsdownloadsRouter',
            [
                'container' => $container,
            ]
        );
    }

    /**
     * Try to build route segments and check if the used query elements were
     * removed from the query.
     */
    public function tryToBuildRouteSegmentsAndCheckIfQueryArgumentsWereRemoved(UnitTester $I)
    {
        $query = [
            'view'      => 'item',
            'layout'    => 'edit',
            'id'        => '1',
            'task'      => 'download',
            'tmpl'      => 'component',
            'extra_arg' => '1',
        ];

        $segments = $this->router->build($query);

        $I->assertArrayNotHasKey('view', $query, 'The key view should be removed from the query');
        $I->assertArrayNotHasKey('layout', $query, 'The key layout should be removed from the query');
        $I->assertArrayNotHasKey('id', $query, 'The key id should be removed from the query');
        $I->assertArrayNotHasKey('task', $query, 'The key task should be removed from the query');
        $I->assertArrayNotHasKey('tmpl', $query, 'The key tmpl should be removed from the query');

        $I->assertArrayHasKey('extra_arg', $query, 'Extra arguments should no be removed from the query');
    }

    /**
     * Try to build route segments for the routedownload and download tasks.
     *
     * @example {"task": "routedownload", "layout": "any-layout", "id": "1", "route": "routedownload/category_1/category_2/category_3/file_1"}
     * @example {"task": "routedownload", "id": "1", "route": "routedownload/category_1/category_2/category_3/file_1"}
     * @example {"task": "download", "layout": "any-layout", "id": "1", "route": "download/category_1/category_2/category_3/file_1"}
     * @example {"task": "download", "id": "1", "route": "download/category_1/category_2/category_3/file_1"}
     */
    public function tryToBuildRouteSegmentsForRoutedownloadAndDownloadTasks(UnitTester $I, Example $example)
    {
        $query = [
            'task' => $example['task'],
            'id'   => $example['id'],
        ];

        if (isset($example['layout'])) {
            $query['layout'] = $example['layout'];
        }

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    /**
     * Try to build route segments for the confirmemail task.
     *
     * task: 'confirmemail' ==> 0: 'confirmemail', 1: [query_data]
     *
     * @example {"task": "confirmemail", "data": "889ec873b0e085c1724ec0ca560d3cfe", "route": "confirmemail/889ec873b0e085c1724ec0ca560d3cfe"}
     * @example {"task": "confirmemail", "data": "4d43e82c9633e2c57df71042d9976135", "view": "any-view", "route": "confirmemail/4d43e82c9633e2c57df71042d9976135"}
     */
    public function tryToBuildRouteSegmentsForConfirmEmailTask(UnitTester $I, Example $example)
    {
        $query = [
            'task' => $example['task'],
            'data' => $example['data'],
        ];

        if (isset($example['view'])) {
            $query['view'] = $example['view'];
        }

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    /**
     * Try to build route segments for the thank you page in the item view.
     *
     * @example {"view": "item", "layout": "thankyou", "id": 1, "route": "category_1/category_2/category_3/files/file_1/thankyou"}
     * @example {"view": "item", "layout": "thankyou", "id": 2, "route": "category_1/files/file_2/thankyou"}
     */
    public function tryToBuildRouteSegmentsForViewItemThankYouPage(UnitTester $I, Example $example)
    {
        $query = [
            'view'   => $example['view'],
            'layout' => $example['layout'],
            'id'     => $example['id'],
        ];

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    /**
     * Try to build route segments for a single file.
     *
     * @example {"view": "item", "id": 1, "route": "category_1/category_2/category_3/files/file_1"}
     * @example {"view": "item", "id": 2, "route": "category_1/files/file_2"}
     */
    public function tryToBuildRouteSegmentsForASingleFile(UnitTester $I, Example $example)
    {
        $query = [
            'view'   => $example['view'],
            'id'     => $example['id'],
        ];

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    /**
     * Try to build route segments for a list of files.
     *
     * @example {"view": "downloads", "id": 1, "route": "category_1/files"}
     * @example {"view": "downloads", "id": 2, "route": "category_1/category_2/files"}
     * @example {"view": "downloads", "id": 3, "route": "category_1/category_2/category_3/files"}
     */
    public function tryToBuildRouteSegmentsForAListOfFiles(UnitTester $I, Example $example)
    {
        $query = [
            'view'   => $example['view'],
            'id'     => $example['id'],
        ];

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    /**
     * Try to build route segments for a list of categories. (Pro version)
     *
     * @example {"view": "categories", "id": 1, "route": "category_1"}
     * @example {"view": "categories", "id": 2, "route": "category_1/category_2"}
     * @example {"view": "categories", "id": 3, "route": "category_1/category_2/category_3"}
     */
    public function tryToBuildRouteSegmentsForAListOfCategories(UnitTester $I, Example $example)
    {
        $query = [
            'view'   => $example['view'],
            'id'     => $example['id'],
        ];

        $route = implode('/', $this->router->build($query));

        $I->assertEquals($example['route'], $route);
    }

    // PARSE
    // thankyou/category_1/category_2/category_3/file1

    // category
    // category/category_1

    // file/category_1/category_2/category_3/file1

    // download/category_1/category_2/category_3/file1

    // routedownload/category_1/category_2/category_3/file1

    // confirmemail/889ec873b0e085c1724ec0ca560d3cfe
}
