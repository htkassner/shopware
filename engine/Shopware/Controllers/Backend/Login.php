<?php
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
 * @package    Shopware_Controllers
 * @subpackage Login
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author     $Author$
 */

/**
 * Shopware Login Controller
 *
 * todo@all: Documentation
 */
class Shopware_Controllers_Backend_Login extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Do authentication and return result in json-format
     * Check if account is blocked
     */
    public function loginAction()
    {
        $username = $this->Request()->get('username');
        $password = $this->Request()->get('password');

        if(empty($username) || empty($password)) {
            $this->View()->assign(array('success' => false));
            return;
        }

        $user = Shopware()->Auth()->login($username, $password);
        if(!empty($user->roleID)) {
            $user->role = Shopware()->Models()->find(
                'Shopware\Models\User\Role',
                $user->roleID
            );
        }
        if(($locale = $this->Request()->get('locale')) !== null) {
            $user->locale = Shopware()->Models()->getRepository(
                'Shopware\Models\Shop\Locale'
            )->find($locale);
        }
        if(!isset($user->locale) && !empty($user->localeID)) {
            $user->locale = Shopware()->Models()->find(
                'Shopware\Models\Shop\Locale',
                $user->localeID
            );
        }
        if(!isset($user->locale)) {
            $user->locale = Shopware()->Models()->getRepository(
                'Shopware\Models\Shop\Locale'
            )->find($this->getPlugin()->getDefaultLocale());
        }

        $this->View()->assign(array(
            'success' => !empty($user),
            'user' => $user->username,
            'locale' => isset($user->locale) ? $user->locale->toString() : null,
            'lockedUntil' => null
        ));
    }

    /**
     * On logout destroy session and redirect to auth controller
     */
    public function logoutAction()
    {
        Shopware()->Auth()->clearIdentity();
        $this->redirect('backend');
    }

    /**
     * @return Shopware_Plugins_Backend_Locale_Bootstrap
     */
    public function getPlugin()
    {
        return Shopware()->Plugins()->Backend()->Auth();
    }

    /**
     * Gets the available backend locales and returns them in an ExtJS
     * friendly format
     *
     * Note that this function returns sample data to build up the module.
     */
    public function getLocalesAction()
    {
        $current = Shopware()->Locale();
        $locales = $this->getPlugin()->getLocales();
        $locales = Shopware()->Db()->quote($locales);
        $sql = 'SELECT id, locale FROM s_core_locales WHERE id IN (' . $locales . ')';
        $locales = Shopware()->Db()->fetchPairs($sql);

        $data = array();
        foreach($locales as $id => $locale) {
            list($l, $t) = explode('_', $locale);
            $l = $current->getTranslation($l, 'language', $current);
            $t = $current->getTranslation($t, 'territory', $current);
            $data[] = array(
                'id' => $id,
                'name' => "$l ($t)"
            );
        }

        $this->View()->assign(array(
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ));
    }

    /**
     * Gets the current login status of the user.
     */
    public function getLoginStatusAction()
    {
        $auth = Shopware()->Auth();
        if($auth->hasIdentity()) {
            $refresh = $auth->refresh();
        }
        if($auth->hasIdentity()) {
            $messages = $refresh->getMessages();
            $this->View()->assign(array(
                'success' => true,
                'message' => $messages[0]
            ));
        } else {
            $auth->clearIdentity();
            $this->View()->assign(array(
                'success' => false
            ));
        }
    }
}