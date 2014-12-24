<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Bundle\Model\Product;

use Magento\Bundle\Model\Resource\Option\Collection;
use Magento\Catalog\Model\Product\Option\Type\DefaultType;
use Magento\Framework\Model\Exception;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $model;

    /**
     * @var \Magento\Bundle\Model\Resource\Selection\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bundleCollection;

    /**
     * @var \Magento\Catalog\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Bundle\Model\OptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $bundleOptionFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockState;

    /**
     * @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $catalogProduct;

    protected function setUp()
    {
        $this->bundleCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Selection\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogData = $this->getMockBuilder('Magento\Catalog\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->bundleOptionFactory = $this->getMockBuilder('Magento\Bundle\Model\OptionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockRegistry = $this->getMockBuilder('Magento\CatalogInventory\Model\StockRegistry')
            ->setMethods(['getStockItem'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->stockState = $this->getMockBuilder('\Magento\CatalogInventory\Model\StockState')
            ->setMethods(['getStockQty'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogProduct = $this->getMockBuilder('Magento\Catalog\Helper\Product')
            ->setMethods(['getSkipSaleableCheck'])
            ->disableOriginalConstructor()
            ->getMock();
        $bundleModelSelection = $this->getMockBuilder('\Magento\Bundle\Model\SelectionFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $bundleFactory = $this->getMockBuilder('\Magento\Bundle\Model\Resource\BundleFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject(
            'Magento\Bundle\Model\Product\Type',
            [
                'bundleModelSelection' => $bundleModelSelection,
                'bundleFactory' => $bundleFactory,
                'bundleCollection' => $this->bundleCollection,
                'bundleOption' => $this->bundleOptionFactory,
                'catalogData' => $this->catalogData,
                'storeManager' => $this->storeManager,
                'stockRegistry' => $this->stockRegistry,
                'stockState' => $this->stockState,
                'catalogProduct' => $this->catalogProduct
            ]
        );
    }

    public function testPrepareForCartAdvancedSelectRequiredOptions()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|DefaultType $group */
        $group = $this->getMockBuilder('Magento\Catalog\Model\Product\Option\Type\DefaultType')
            ->setMethods(
                ['setOption', 'setProduct', 'setRequest', 'setProcessMode', 'validateUserValue', 'prepareForCart']
            )
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Object $buyRequest */
        $buyRequest = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(
                ['__wakeup', 'getOptions', 'getSuperProductConfig', 'unsetData', 'getData', 'getQty', 'getBundleOption']
            )
            ->disableOriginalConstructor()
            ->getMock();
        /* @var $option \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Option */
        $option = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->setMethods(['groupFactory', 'getType', 'getId', 'getRequired'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(
                [
                    'getOptions',
                    'prepareCustomOptions',
                    'addCustomOption',
                    'setCartQty',
                    'setQty',
                    'getSkipCheckRequiredOption',
                    'getTypeInstance',
                    'getStoreId',
                    'hasData',
                    'getData'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Bundle\Model\Product\Type $productType */
        $productType = $this->getMockBuilder('\Magento\Bundle\Model\Product\Type')
            ->setMethods(['setStoreFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Collection $optionCollection */
        $optionCollection = $this->getMockBuilder('Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods(['getItems'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->parentClass($group, $option, $buyRequest, $product);

        $product->expects($this->at(0))
            ->method('getSkipCheckRequiredOption')
            ->willReturn(true);
        $product->expects($this->at(1))
            ->method('getSkipCheckRequiredOption')
            ->willReturn(false);
        $product->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($productType);
        $product->expects($this->once())
            ->method('hasData')
            ->willReturn(true);
        $product->expects($this->once())
            ->method('getData')
            ->willReturn($optionCollection);
        $productType->expects($this->once())
            ->method('setStoreFilter');
        $buyRequest->expects($this->once())
            ->method('getBundleOption')
            ->willReturn([1]);
        $optionCollection->expects($this->once())
            ->method('getItems')
            ->willReturn([$option]);
        $option->expects($this->once())
            ->method('getRequired')
            ->willReturn(true);
        $option->expects($this->at(1))
            ->method('getId')
            ->willReturn(3);

        $result = $this->model->prepareForCartAdvanced($buyRequest, $product);
        $this->assertEquals('Please select all required options.', $result);
    }

    public function testPrepareForCartAdvancedParentClassReturnString()
    {
        $exceptedResult = 'String message';
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Object $buyRequest */
        $buyRequest = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['getItems', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->at(0))
            ->method('getOptions')
            ->willThrowException(new Exception($exceptedResult));

        $result = $this->model->prepareForCartAdvanced($buyRequest, $product);
        $this->assertEquals($exceptedResult, $result);
    }

    public function testPrepareForCartAdvancedSpecifyProductOptions()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|DefaultType $group */
        $group = $this->getMockBuilder('Magento\Catalog\Model\Product\Option\Type\DefaultType')
            ->setMethods(
                ['setOption', 'setProduct', 'setRequest', 'setProcessMode', 'validateUserValue', 'prepareForCart']
            )
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Object $buyRequest */
        $buyRequest = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(
                ['__wakeup', 'getOptions', 'getSuperProductConfig', 'unsetData', 'getData', 'getQty', 'getBundleOption']
            )
            ->disableOriginalConstructor()
            ->getMock();
        /* @var $option \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Option */
        $option = $this->getMockBuilder('Magento\Catalog\Model\Product\Option')
            ->setMethods(['groupFactory', 'getType', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(
                [
                    'getOptions',
                    'prepareCustomOptions',
                    'addCustomOption',
                    'setCartQty',
                    'setQty',
                    'getSkipCheckRequiredOption'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->parentClass($group, $option, $buyRequest, $product);

        $product->expects($this->once())
            ->method('getSkipCheckRequiredOption')
            ->willReturn(true);
        $buyRequest->expects($this->once())
            ->method('getBundleOption')
            ->willReturn([0, '', 'str']);

        $result = $this->model->prepareForCartAdvanced($buyRequest, $product);
        $this->assertEquals('Please specify product option(s).', $result);
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->model->hasWeight(), 'This product has no weight, but it should');
    }

    public function testGetIdentities()
    {
        $identities = ['id1', 'id2'];
        $productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            ['getSelections', '__wakeup'],
            [],
            '',
            false
        );
        $optionCollectionMock = $this->getMock(
            'Magento\Bundle\Model\Resource\Option\Collection',
            [],
            [],
            '',
            false
        );
        $cacheKey = '_cache_instance_options_collection';
        $productMock->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($identities));
        $productMock->expects($this->once())
            ->method('hasData')
            ->with($cacheKey)
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getData')
            ->with($cacheKey)
            ->will($this->returnValue($optionCollectionMock));
        $optionCollectionMock
            ->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$optionMock]));
        $optionMock
            ->expects($this->exactly(2))
            ->method('getSelections')
            ->will($this->returnValue([$productMock]));
        $this->assertEquals($identities, $this->model->getIdentities($productMock));
    }

    public function testGetSkuWithType()
    {
        $sku = 'sku';
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('sku')
            ->will($this->returnValue($sku));
        $productMock->expects($this->at(2))
            ->method('getData')
            ->with('sku_type')
            ->will($this->returnValue('some_data'));

        $this->assertEquals($sku, $this->model->getSku($productMock));
    }

    public function testGetSkuWithoutType()
    {
        $sku = 'sku';
        $itemSku = 'item';
        $selectionIds = [1, 2, 3];
        $serializeIds = serialize($selectionIds);
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getData', 'hasCustomOptions', 'getCustomOption'])
            ->disableOriginalConstructor()
            ->getMock();
        $customOptionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->setMethods(['getValue', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selectionItemMock = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['getSku', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('sku')
            ->will($this->returnValue($sku));
        $productMock->expects($this->at(1))
            ->method('getCustomOption')
            ->with('option_ids')
            ->will($this->returnValue(false));
        $productMock->expects($this->at(2))
            ->method('getData')
            ->with('sku_type')
            ->will($this->returnValue(null));
        $productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->will($this->returnValue(true));
        $productMock->expects($this->at(4))
            ->method('getCustomOption')
            ->with('bundle_selection_ids')
            ->will($this->returnValue($customOptionMock));
        $customOptionMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue($serializeIds));
        $selectionMock = $this->getSelectionsByIdsMock($selectionIds, $productMock, 5, 6);
        $selectionMock->expects(($this->any()))
            ->method('getItems')
            ->will($this->returnValue([$selectionItemMock]));
        $selectionItemMock->expects($this->any())
            ->method('getSku')
            ->will($this->returnValue($itemSku));

        $this->assertEquals($sku . '-' . $itemSku, $this->model->getSku($productMock));
    }

    public function testGetWeightWithoutCustomOption()
    {
        $weight = 5;
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('weight_type')
            ->will($this->returnValue(true));
        $productMock->expects($this->at(1))
            ->method('getData')
            ->with('weight')
            ->will($this->returnValue($weight));

        $this->assertEquals($weight, $this->model->getWeight($productMock));
    }

    public function testGetWeightWithCustomOption()
    {
        $weight = 5;
        $selectionIds = [1, 2, 3];
        $serializeIds = serialize($selectionIds);
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getData', 'hasCustomOptions', 'getCustomOption'])
            ->disableOriginalConstructor()
            ->getMock();
        $customOptionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->setMethods(['getValue', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selectionItemMock = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['getSelectionId', 'getWeight', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('weight_type')
            ->will($this->returnValue(false));
        $productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->will($this->returnValue(true));
        $productMock->expects($this->at(2))
            ->method('getCustomOption')
            ->with('bundle_selection_ids')
            ->will($this->returnValue($customOptionMock));
        $customOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($serializeIds));
        $selectionMock = $this->getSelectionsByIdsMock($selectionIds, $productMock, 3, 4);
        $selectionMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$selectionItemMock]));
        $selectionItemMock->expects($this->any())
            ->method('getSelectionId')
            ->will($this->returnValue('id'));
        $productMock->expects($this->at(5))
            ->method('getCustomOption')
            ->with('selection_qty_' . 'id')
            ->will($this->returnValue(null));
        $selectionItemMock->expects($this->once())
            ->method('getWeight')
            ->will($this->returnValue($weight));


        $this->assertEquals($weight, $this->model->getWeight($productMock));
    }

    public function testGetWeightWithSeveralCustomOption()
    {
        $weight = 5;
        $qtyOption = 5;
        $selectionIds = [1, 2, 3];
        $serializeIds = serialize($selectionIds);
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getData', 'hasCustomOptions', 'getCustomOption'])
            ->disableOriginalConstructor()
            ->getMock();
        $customOptionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->setMethods(['getValue', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $qtyOptionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->setMethods(['getValue', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selectionItemMock = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['getSelectionId', 'getWeight', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('weight_type')
            ->will($this->returnValue(false));
        $productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->will($this->returnValue(true));
        $productMock->expects($this->at(2))
            ->method('getCustomOption')
            ->with('bundle_selection_ids')
            ->will($this->returnValue($customOptionMock));
        $customOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($serializeIds));
        $selectionMock = $this->getSelectionsByIdsMock($selectionIds, $productMock, 3, 4);
        $selectionMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$selectionItemMock]));
        $selectionItemMock->expects($this->any())
            ->method('getSelectionId')
            ->will($this->returnValue('id'));
        $productMock->expects($this->at(5))
            ->method('getCustomOption')
            ->with('selection_qty_' . 'id')
            ->will($this->returnValue($qtyOptionMock));
        $qtyOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($qtyOption));
        $selectionItemMock->expects($this->once())
            ->method('getWeight')
            ->will($this->returnValue($weight));

        $this->assertEquals($weight * $qtyOption, $this->model->getWeight($productMock));
    }

    public function testIsVirtualWithoutCustomOption()
    {
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->will($this->returnValue(false));

        $this->assertFalse($this->model->isVirtual($productMock));
    }

    public function testIsVirtual()
    {
        $selectionIds = [1, 2, 3];
        $serializeIds = serialize($selectionIds);

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $customOptionMock = $this->getMockBuilder('Magento\Catalog\Model\Product\Configuration\Item\Option')
            ->setMethods(['getValue', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $selectionItemMock = $this->getMockBuilder('Magento\Framework\Object')
            ->setMethods(['isVirtual', 'getItems', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->once())
            ->method('hasCustomOptions')
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getCustomOption')
            ->with('bundle_selection_ids')
            ->will($this->returnValue($customOptionMock));
        $customOptionMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($serializeIds));
        $selectionMock = $this->getSelectionsByIdsMock($selectionIds, $productMock, 2, 3);
        $selectionMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue([$selectionItemMock]));
        $selectionItemMock->expects($this->once())
            ->method('isVirtual')
            ->will($this->returnValue(true));
        $selectionItemMock->expects($this->once())
            ->method('isVirtual')
            ->will($this->returnValue(true));
        $selectionMock->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->model->isVirtual($productMock));
    }

    /**
     * @param $selectionIds
     * @param $productMock
     * @param $getSelectionsIndex
     * @param $getSelectionsIdsIndex
     * @return \PHPUnit_Framework_MockObject_MockObject
     */

    protected function getSelectionsByIdsMock($selectionIds, $productMock, $getSelectionsIndex, $getSelectionsIdsIndex)
    {
        $usedSelectionsMock = $this->getMockBuilder('Magento\Bundle\Model\Resource\Selection\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at($getSelectionsIndex))
            ->method('getData')
            ->with('_cache_instance_used_selections')
            ->will($this->returnValue($usedSelectionsMock));
        $productMock->expects($this->at($getSelectionsIdsIndex))
            ->method('getData')
            ->with('_cache_instance_used_selections_ids')
            ->will($this->returnValue($selectionIds));

        return $usedSelectionsMock;
    }

    /**
     * @param $expected
     * @param $firstId
     * @param $secondId
     * @dataProvider shakeSelectionsDataProvider
     */
    public function testShakeSelections($expected, $firstId, $secondId)
    {
        $firstItemMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getOption', 'getOptionId', 'getPosition', 'getSelectionId'])
            ->disableOriginalConstructor()
            ->getMock();
        $secondItemMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['__wakeup', 'getOption', 'getOptionId', 'getPosition', 'getSelectionId'])
            ->disableOriginalConstructor()
            ->getMock();
        $optionFirstMock = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['getPosition', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $optionSecondMock = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['getPosition', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $firstItemMock->expects($this->once())
            ->method('getOption')
            ->will($this->returnValue($optionFirstMock));
        $optionFirstMock->expects($this->once())
            ->method('getPosition')
            ->will($this->returnValue('option_position'));
        $firstItemMock->expects($this->once())
            ->method('getOptionId')
            ->will($this->returnValue('option_id'));
        $firstItemMock->expects($this->once())
            ->method('getPosition')
            ->will($this->returnValue('position'));
        $firstItemMock->expects($this->once())
            ->method('getSelectionId')
            ->will($this->returnValue($firstId));
        $secondItemMock->expects($this->once())
            ->method('getOption')
            ->will($this->returnValue($optionSecondMock));
        $optionSecondMock->expects($this->any())
            ->method('getPosition')
            ->will($this->returnValue('option_position'));
        $secondItemMock->expects($this->once())
            ->method('getOptionId')
            ->will($this->returnValue('option_id'));
        $secondItemMock->expects($this->once())
            ->method('getPosition')
            ->will($this->returnValue('position'));
        $secondItemMock->expects($this->once())
            ->method('getSelectionId')
            ->will($this->returnValue($secondId));

        $this->assertEquals($expected, $this->model->shakeSelections($firstItemMock, $secondItemMock));
    }

    /**
     * @return array
     */
    public function shakeSelectionsDataProvider()
    {
        return [
            [0, 0, 0],
            [1, 1, 0],
            [-1, 0, 1]
        ];
    }

    public function testGetSelectionsByIds()
    {
        $selectionIds = [1, 2, 3];
        $usedSelectionsIds = [4, 5, 6];
        $storeId = 2;
        $websiteId = 1;
        $storeFilter = 'store_filter';
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $usedSelectionsMock = $this->getMockBuilder('Magento\Bundle\Model\Resource\Selection\Collection')
            ->setMethods(
                [
                    'addAttributeToSelect',
                    'setFlag',
                    'addStoreFilter',
                    'setStoreId',
                    'setPositionOrder',
                    'addFilterByRequiredOptions',
                    'setSelectionIdsFilter',
                    'joinPrices'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $productGetMap = [
            ['_cache_instance_used_selections', null, null],
            ['_cache_instance_used_selections_ids', null, $usedSelectionsIds],
            ['_cache_instance_store_filter', null, $storeFilter],
        ];
        $productMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($productGetMap));
        $productSetMap = [
            ['_cache_instance_used_selections', $usedSelectionsMock, $productMock],
            ['_cache_instance_used_selections_ids', $selectionIds, $productMock],
        ];
        $productMock->expects($this->any())
            ->method('setData')
            ->will($this->returnValueMap($productSetMap));
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->setMethods(['getWebsiteId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->will($this->returnValue($websiteId));

        $this->bundleCollection->expects($this->once())
            ->method('create')
            ->will($this->returnValue($usedSelectionsMock));

        $usedSelectionsMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('*')
            ->will($this->returnSelf());
        $flagMap = [
            ['require_stock_items', true, $usedSelectionsMock],
            ['product_children', true, $usedSelectionsMock],
        ];
        $usedSelectionsMock->expects($this->any())
            ->method('setFlag')
            ->will($this->returnValueMap($flagMap));
        $usedSelectionsMock->expects($this->once())
            ->method('addStoreFilter')
            ->with($storeFilter)
            ->will($this->returnSelf());
        $usedSelectionsMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->will($this->returnSelf());
        $usedSelectionsMock->expects($this->once())
            ->method('setPositionOrder')
            ->will($this->returnSelf());
        $usedSelectionsMock->expects($this->once())
            ->method('addFilterByRequiredOptions')
            ->will($this->returnSelf());
        $usedSelectionsMock->expects($this->once())
            ->method('setSelectionIdsFilter')
            ->with($selectionIds)
            ->will($this->returnSelf());
        $usedSelectionsMock->expects($this->once())
            ->method('joinPrices')
            ->with($websiteId)
            ->will($this->returnSelf());

        $this->catalogData->expects($this->once())
            ->method('isPriceGlobal')
            ->will($this->returnValue(false));

        $this->model->getSelectionsByIds($selectionIds, $productMock);
    }

    public function testGetOptionsByIds()
    {
        $optionsIds = [1, 2, 3];
        $usedOptionsIds = [4, 5, 6];
        $productId = 3;
        $storeId = 2;
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $usedOptionsMock = $this->getMockBuilder('Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods(['getResourceCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $dbResourceMock = $this->getMockBuilder('Magento\Framework\Model\Resource\Db\Collection\AbstractCollection')
            ->setMethods(['setProductIdFilter', 'setPositionOrder', 'joinValues', 'setIdFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->at(0))
            ->method('getData')
            ->with('_cache_instance_used_options')
            ->will($this->returnValue(null));
        $productMock->expects($this->at(1))
            ->method('getData')
            ->with('_cache_instance_used_options_ids')
            ->will($this->returnValue($usedOptionsIds));
        $productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($productId));
        $this->bundleOptionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($usedOptionsMock));
        $usedOptionsMock->expects($this->once())
            ->method('getResourceCollection')
            ->will($this->returnValue($dbResourceMock));
        $dbResourceMock->expects($this->once())
            ->method('setProductIdFilter')
            ->with($productId)
            ->will($this->returnSelf());
        $dbResourceMock->expects($this->once())
            ->method('setPositionOrder')
            ->will($this->returnSelf());
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));
        $dbResourceMock->expects($this->once())
            ->method('joinValues')
            ->will($this->returnSelf());
        $dbResourceMock->expects($this->once())
            ->method('setIdFilter')
            ->with($optionsIds)
            ->will($this->returnSelf());
        $productMock->expects($this->at(3))
            ->method('setData')
            ->with('_cache_instance_used_options', $dbResourceMock)
            ->will($this->returnSelf());
        $productMock->expects($this->at(4))
            ->method('setData')
            ->with('_cache_instance_used_options_ids', $optionsIds)
            ->will($this->returnSelf());

        $this->model->getOptionsByIds($optionsIds, $productMock);
    }

    public function testIsSalableFalse()
    {
        $product = new \Magento\Framework\Object(
            [
                'is_salable' => false,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            ]
        );

        $this->assertFalse($this->model->isSalable($product));
    }

    public function testIsSalableWithoutOptions()
    {
        $optionCollectionMock = $this->getMockBuilder('\Magento\Bundle\Model\Resource\Option\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                '_cache_instance_options_collection' => $optionCollectionMock,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            ]
        );

        $this->assertFalse($this->model->isSalable($product));
    }

    public function testIsSalableWithRequiredOptionsTrue()
    {
        $option1 = $this->getRequiredOptionMock(10, 10);
        $option2 = $this->getRequiredOptionMock(20, 10);


        $this->stockRegistry->method('getStockItem')
            ->willReturn($this->getStockItem(true));
        $this->stockState
            ->expects($this->at(0))
            ->method('getStockQty')
            ->with(10)
            ->willReturn(10);
        $this->stockState
            ->expects($this->at(1))
            ->method('getStockQty')
            ->with(20)
            ->willReturn(10);

        $option3 = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(['getRequired', 'getOptionId', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $option3->method('getRequired')
            ->willReturn(false);
        $option3->method('getOptionId')
            ->willReturn(30);
        $option3->method('getId')
            ->willReturn(30);

        $optionCollectionMock = $this->getOptionCollectionMock([$option1, $option2, $option3]);
        $selectionCollectionMock = $this->getSelectionCollectionMock([$option1, $option2]);

        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                '_cache_instance_options_collection' => $optionCollectionMock,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                '_cache_instance_selections_collection10_20_30' => $selectionCollectionMock
            ]
        );

        $this->assertTrue($this->model->isSalable($product));
    }

    public function testIsSalableCache()
    {
        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                'all_items_salable' => true
            ]
        );

        $this->assertTrue($this->model->isSalable($product));
    }

    public function testIsSalableWithEmptySelectionsCollection()
    {
        $option = $this->getRequiredOptionMock(1, 10);
        $optionCollectionMock = $this->getOptionCollectionMock([$option]);
        $selectionCollectionMock = $this->getSelectionCollectionMock([]);

        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                '_cache_instance_options_collection' => $optionCollectionMock,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                '_cache_instance_selections_collection1' => $selectionCollectionMock
            ]
        );

        $this->assertFalse($this->model->isSalable($product));
    }

    public function testIsSalableWithRequiredOptionsOutOfStock()
    {
        $option1 = $this->getRequiredOptionMock(10, 10);
        $option1
            ->expects($this->atLeastOnce())
            ->method('getSelectionCanChangeQty')
            ->willReturn(false);


        $option2 = $this->getRequiredOptionMock(20, 10);
        $option2
            ->expects($this->atLeastOnce())
            ->method('getSelectionCanChangeQty')
            ->willReturn(false);

        $this->stockRegistry->method('getStockItem')
            ->willReturn($this->getStockItem(true));
        $this->stockState
            ->method('getStockQty')
            ->will(
                $this->returnValueMap(
                    [
                        [10, 10],
                        [20, 5]
                    ]
                )
            );

        $optionCollectionMock = $this->getOptionCollectionMock([$option1, $option2]);
        $selectionCollectionMock = $this->getSelectionCollectionMock([$option1, $option2]);

        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                '_cache_instance_options_collection' => $optionCollectionMock,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                '_cache_instance_selections_collection10_20' => $selectionCollectionMock
            ]
        );

        $this->assertFalse($this->model->isSalable($product));
    }

    public function testIsSalableNoManageStock()
    {
        $option1 = $this->getRequiredOptionMock(10, 10);
        $option2 = $this->getRequiredOptionMock(20, 10);

        $stockItem = $this->getStockItem(true);

        $this->stockRegistry->method('getStockItem')
            ->willReturn($stockItem);

        $this->stockState
            ->expects($this->at(0))
            ->method('getStockQty')
            ->with(10)
            ->willReturn(10);
        $this->stockState
            ->expects($this->at(1))
            ->method('getStockQty')
            ->with(20)
            ->willReturn(10);

        $optionCollectionMock = $this->getOptionCollectionMock([$option1, $option2]);
        $selectionCollectionMock = $this->getSelectionCollectionMock([$option1, $option2]);

        $product = new \Magento\Framework\Object(
            [
                'is_salable' => true,
                '_cache_instance_options_collection' => $optionCollectionMock,
                'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
                '_cache_instance_selections_collection10_20' => $selectionCollectionMock
            ]
        );

        $this->assertTrue($this->model->isSalable($product));
    }


    /**
     * @param int $id
     * @param int $selectionQty
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequiredOptionMock($id, $selectionQty)
    {
        $option = $this->getMockBuilder('Magento\Bundle\Model\Option')
            ->setMethods(
                [
                    'getRequired',
                    'isSalable',
                    'hasSelectionQty',
                    'getSelectionQty',
                    'getOptionId',
                    'getId',
                    'getSelectionCanChangeQty'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $option->method('getRequired')
            ->willReturn(true);
        $option->method('isSalable')
            ->willReturn(true);
        $option->method('hasSelectionQty')
            ->willReturn(true);
        $option->method('getSelectionQty')
            ->willReturn($selectionQty);
        $option->method('getOptionId')
            ->willReturn($id);
        $option->method('getSelectionCanChangeQty')
            ->willReturn(false);
        $option->method('getId')
            ->willReturn($id);

        return $option;
    }

    /**
     * @param array $selectedOptions
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSelectionCollectionMock(array $selectedOptions)
    {
        $selectionCollectionMock = $this->getMockBuilder('\Magento\Bundle\Model\Resource\Selection\Collection')
            ->setMethods(['getItems', 'getIterator'])
            ->disableOriginalConstructor()
            ->getMock();

        $selectionCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($selectedOptions);

        $selectionCollectionMock
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($selectedOptions));

        return $selectionCollectionMock;
    }

    /**
     * @param array $options
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getOptionCollectionMock(array $options)
    {
        $ids = [];
        foreach ($options as $option) {
            $ids[] = $option->getId();
        }

        $optionCollectionMock = $this->getMockBuilder('\Magento\Bundle\Model\Resource\Option\Collection')
            ->setMethods(['getItems', 'getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $optionCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($options);

        $optionCollectionMock
            ->expects($this->any())
            ->method('getAllIds')
            ->willReturn($ids);

        return $optionCollectionMock;
    }

    protected function getStockItem($isManageStock)
    {
        $result = $this->getMockBuilder('\Magento\CatalogInventory\Api\Data\StockItem')
            ->setMethods(['getManageStock'])
            ->disableOriginalConstructor()
            ->getMock();
        $result->method('getManageStock')
            ->willReturn($isManageStock);

        return $result;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject|DefaultType $group
     * @param \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product\Option $option
     * @param \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Object $buyRequest
     * @param \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product $product
     */
    protected function parentClass($group, $option, $buyRequest, $product)
    {
        $group->expects($this->once())
            ->method('setOption')
            ->willReturnSelf();
        $group->expects($this->once())
            ->method('setProduct')
            ->willReturnSelf();
        $group->expects($this->once())
            ->method('setRequest')
            ->willReturnSelf();
        $group->expects($this->once())
            ->method('setProcessMode')
            ->willReturnSelf();
        $group->expects($this->once())
            ->method('validateUserValue')
            ->willReturnSelf();
        $group->expects($this->once())
            ->method('prepareForCart')
            ->willReturn('someString');

        $option->expects($this->once())
            ->method('getType');
        $option->expects($this->once())
            ->method('groupFactory')
            ->willReturn($group);
        $option->expects($this->at(0))
            ->method('getId')
            ->willReturn(333);

        $buyRequest->expects($this->once())
            ->method('getData');
        $buyRequest->expects($this->once())
            ->method('getOptions');
        $buyRequest->expects($this->once())
            ->method('getSuperProductConfig')
            ->willReturn([]);
        $buyRequest->expects($this->any())
            ->method('unsetData')
            ->willReturnSelf();
        $buyRequest->expects($this->any())
            ->method('getQty');

        $product->expects($this->once())
            ->method('getOptions')
            ->willReturn([$option]);
        $product->expects($this->once())
            ->method('prepareCustomOptions');
        $product->expects($this->any())
            ->method('addCustomOption');
        $product->expects($this->once())
            ->method('setCartQty');
        $product->expects($this->once())
            ->method('setQty');

        $this->catalogProduct->expects($this->once())
            ->method('getSkipSaleableCheck')
            ->willReturn(false);
    }
}
