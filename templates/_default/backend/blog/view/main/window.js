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
 * @package    Blog
 * @subpackage View
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/blog/view/blog}

/**
 * Shopware UI - blog main window.
 *
 * Displays the main window
 */
//{block name="backend/blog/view/main/window"}
Ext.define('Shopware.apps.Blog.view.main.Window', {
	extend: 'Enlight.app.Window',
    title: '{s name=window/main_title}Blog-Overview{/s}',
    alias: 'widget.blog-main-window',
    border: false,
    autoShow: true,
    layout: 'border',
    height: 650,
    minHeight: 400,
    width: 925,

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.items = [
            {
                xtype:'blog-blog-list', listStore:me.listStore
            },
            {
                xtype:'blog-blog-tree', treeStore:me.treeStore
            }
        ];

        me.callParent(arguments);
    }
});
//{/block}
