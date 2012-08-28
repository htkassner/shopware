/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Article
 * @subpackage Detail
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

/**
 * Shopware Application - Article module
 *
 * todo@all: Documentation
 */
//{block name="backend/article/application"}
Ext.define('Shopware.apps.Article', {

    /**
     * The name of the module. Used for internal purpose
     * @string
     */
    name:'Shopware.apps.Article',

    /**
     * Extends from our special controller, which handles the sub-application behavior and the event bus
     * @string
     */
    extend:'Enlight.app.SubApplication',

    /**
     * Enable bulk loading
     * @boolean
     */
    bulkLoad:true,

    /**
     * Sets the loading path for the sub-application.
     *
     * @string
     */
    loadPath:'{url action=load}',

    /**
     * Array of views to require from AppName.view namespace.
     * @array
     */
    views:[
        'detail.Window',
        'detail.Settings',
        'detail.Properties',
        'detail.Prices',
        'detail.Base',
        'detail.Sidebar',
        'detail.sidebar.Option',
        'detail.sidebar.Link',
        'detail.sidebar.Similar',
        'detail.sidebar.Accessory',
        'category.Tree',
        'category.List',
        'category.DropZone',
        'image.Upload',
        'image.List',
        'image.Info',
        'image.DropZone',
        'image.Mapping',
        'image.NewRule',
        'variant.List',
        'variant.Configurator',
        'variant.Detail',
        'variant.Progress',
        'variant.configurator.Dependency',
        'variant.configurator.Sets',
        'variant.configurator.GroupEdit',
        'variant.configurator.Mapping',
        'esd.List',
        'esd.Detail',
        'esd.Serials',
        'statistics.List'
    ],

    /**
     * Array of stores to require from AppName.store namespace.
     * @array
     */
    stores:[
        'Batch',
        'CategoryPath',
        'CategoryTree',
        'Variant',
        'Group',
        'Option',
        'Esd',
        'EsdFile',
        'Statistic',
        'Serial',
        'Surcharge',
        'Dependency',
        'ConfiguratorSet',
        'MediaMapping'
    ],

    /**
     * Array of models to require from AppName.model namespace.
     * @array
     */
    models: [
        'Batch',
        'PriceGroup',
        'Attribute',
        'PropertyGroup',
        'PropertyOption',
        'PropertyValue',
        'Template',
        'Category',
        'Media',
        'Unit',
        'Similar',
        'Accessory',
        'Article',
        'Price',
        'Detail',
        'Link',
        'Download',
        'Esd',
        'EsdFile',
        'Statistic',
        'Serial',
        'LinkAttribute',
        'DownloadAttribute',
        'EsdAttribute',
        'PriceAttribute',
        'MediaAttribute',
        'Configurator',
        'ConfiguratorGroup',
        'ConfiguratorSet',
        'ConfiguratorOption',
        'Dependency',
        'PriceSurcharge',
        'Field',
        'MediaMapping',
        'MediaMappingRule'
    ],

    /**
     * Requires controllers for sub-application
     * @array
     */
    controllers: [ 'Main', 'Detail', 'Category', 'Media', 'Variant', 'Esd', 'Statistic' ],

    /**
     * Returns the main application window for this is expected
     * by the Enlight.app.SubApplication class.
     * The class sets a new event listener on the "destroy" event of
     * the main application window to perform the destroying of the
     * whole sub application when the user closes the main application window.
     *
     * This method will be called when all dependencies are solved and
     * all member controllers, models, views and stores are initialized.
     *
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
    launch: function() {
//        var me = this,
//            mainController = me.getController('Main');
//
//        return mainController.mainWindow;
    }
});
//{/block}

