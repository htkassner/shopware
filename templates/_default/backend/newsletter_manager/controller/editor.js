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
 * @package    NewsletterManager
 * @subpackage Controller
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/newsletter_manager/main}

/**
 * Shopware Controller - Overview controller
 * For events and actions fired in the overview tab
 */
//{block name="backend/newsletter_manager/controller/editor"}
Ext.define('Shopware.apps.NewsletterManager.controller.Editor', {

    extend: 'Ext.app.Controller',

    snippets: {
        saveNewsletter: {
            successTitle: '{s name=saveNewsletter/successTitle}Successfully saved{/s}',
            successMessage: '{s name=saveNewsletter/successMessage}Successfully saved the newsletter{/s}',
            errorTitle: '{s name=saveNewsletter/errorTitle}Error{/s}',
            errorMessage: '{s name=saveNewsletter/errorMessage}An error occured while saving the newsletter{/s}'
        },
        testNewsletter: {
            successTitle: '{s name=testNewsletter/successTitle}Successfully sended{/s}',
            successMessage: '{s name=testNewsletter/successMessage}Successfully mailed the newsletter{/s}',
            errorTitle: '{s name=testNewsletter/errorTitle}Error{/s}',
            errorMessage: '{s name=testNewsletter/errorMessage}An error occured while sending the newsletter{/s}'
        },
    growl: '{s name=title}Newsletter Manager{/s}'
    },

    refs:[
        { ref:'newsletterEditor', selector:'newsletter-manager-newsletter-editor' },
        { ref:'newsletterSettings', selector:'newsletter-manager-newsletter-settings' },
        { ref:'newsletterWindow', selector:'newsletter-manager-newsletter-window' }
    ],


    /**
     * A template method that is called when your application boots. It is called before the Application's
     * launch function is executed so gives a hook point to run any code before your Viewport is created.
     */
    init: function() {
        var me = this;

        me.control(                {
            'newsletter-manager-newsletter-editor': {
                'sendTestMail': me.onSendTestMail,
                'openPreview': me.onOpenPreview
            },
            'newsletter-manager-newsletter-settings': {
                'formChanged': me.onFormChanged
            },
            'newsletter-manager-bottom-toolbar': {
                'backToOverview': me.onBackToOverview,
                'saveMail': me.onSaveMail
            }
        });

        me.callParent(arguments);
    },

    /**
     * Called when the user clicked the 'back to overview' button
     */
    onBackToOverview: function() {
        var me = this,
            window = me.getNewsletterWindow();

        window.destroy();


    },

    /**
     * Called when a field in the settings form changes
     * @param field
     */
    onFormChanged: function(form) {
        var me = this,
            window = me.getNewsletterWindow();

        if(form.isValid()){
            window.toolbar.saveButton.enable();
            return;
        }
        window.toolbar.saveButton.disable();
    },

    /**
     * Called when the user clicks the 'save' button in the settings view
     */
    onSaveMail: function() {
        var me = this,
              form = me.getNewsletterSettings().form,
              newsletter = me.createNewsletterModel(me.getNewsletterEditor().record);

        // Only save if the form is valid. As formBind: true only works for buttons on the form
        // and as we want to have buttons in the editor as well as in the settings (sw-3195)
        // we use a msgbox here
        if(!form.isValid()) {
            Ext.Msg.show({
                title: '{s name=fillForm/title}Required fields missing{/s}',
                msg: '{s name=fillForm/msg}You need to set all required field in the settings first{/s}',
                buttons: Ext.Msg.OK
            });
            return;
        }

          // persist
          newsletter.save({
              callback: function(data, operation){
                  var records = operation.getRecords(),
                      record = records[0],
                      rawData = record.getProxy().getReader().rawData;

                  if(operation.success){
                      Shopware.Notification.createGrowlMessage(me.snippets.saveNewsletter.successTitle, me.snippets.saveNewsletter.successMessage, me.snippets.growl);
                      me.subApplication.mailingStore.reload();

                  }else{
                      Shopware.Notification.createGrowlMessage(me.snippets.saveNewsletter.errorTitle, me.snippets.saveNewsletter.errorMessage, me.snippets.growl);
                  }
              }
          });
    },

    /**
     * Called when the user clicked the 'send test mail' button
     */
    onSendTestMail: function() {
        var me = this,
            address = me.getNewsletterEditor().down('textfield').getValue(),
            newsletter = me.createNewsletterModel();

        newsletter.set('status', -1); // mark the newsletter as preview
        newsletter.set('id', null); // don't overwrite existing newsletters

        // The mail-method of the pre-existing newsletter controller reads the newsletters from db.
        // In order not to add additional overhead, simply write the newsletter to db and clean it up next time
        newsletter.save({
            callback: function(data, operation){
                var records = operation.getRecords(),
                    record = records[0],
                    rawData = record.getProxy().getReader().rawData;
                if(operation.success){
                    // do the actual ajax query to send the mail
                    Ext.Ajax.request({
                        url: '{url controller=Newsletter action="mail"}',
                        method: 'POST',
                        params: {
                            id: newsletter.get('id'),
                            testmail: address
                        },
                        success: function(response) {
                            Shopware.Notification.createGrowlMessage(me.snippets.testNewsletter.successTitle, me.snippets.testNewsletter.successMessage, me.snippets.growl);
                        },
                        failure: function(response) {
                            Shopware.Notification.createGrowlMessage(me.snippets.testNewsletter.errorTitle, me.snippets.testNewsletter.errorMessage, me.snippets.growl);
                        }
                    });
                }else{
                    Shopware.Notification.createGrowlMessage(me.snippets.saveNewsletter.errorTitle, me.snippets.saveNewsletter.errorMessage, me.snippets.growl);
                }
            }
        });
    },


    /**
     * A simple helper function which reads out the settings from the newsletters window.
     * It it used, as the mailing-model cannot be easily written into the settings form as some field-types do not match
     * e.g. dispatch/plaintext or groups
     */
    getSettings: function() {
        var me = this,
            record,
            count, totalCount = 0,
            editor = me.getNewsletterEditor(),
            settingsForm = me.getNewsletterSettings(),
            newsletterGroups = settingsForm.newsletterGroups, customerGroups = settingsForm.customerGroups,
            groups = Ext.create('Shopware.apps.NewsletterManager.store.RecipientGroup'),
            content = editor.tinyMce.getEditor().getContent();

        // Iterate the checkboxes and populate the RecipientGroupStore with checked customer groups
        Ext.each(customerGroups, function(checkbox) {
            var record = checkbox.record,
                count = checkbox.count,
                value = checkbox.getValue();

            //todo@dn: set count to the number of users in the given group
            if(value === true) {
                totalCount += count;

                record = Ext.create('Shopware.apps.NewsletterManager.model.RecipientGroup', {
                    internalId: record.get('id'),
                    number: count,
                    name: record.get('name'),
                    groupkey: record.get('key'),
                    isCustomerGroup: true
                });
                groups.add(record);
            }
        });

        // Iterate the checkboxes and populate the RecipientGroupStore with checked newsletter groups
        Ext.each(newsletterGroups, function(checkbox) {
            var record = checkbox.record,
                    count = checkbox.count,
                value = checkbox.getValue();

            //todo@dn: set count to the number of users in the given group
            if(value === true) {
                totalCount += count;

                record = Ext.create('Shopware.apps.NewsletterManager.model.RecipientGroup', {
                    internalId: record.get('id'),
                    number: count,
                    name: record.get('name'),
                    groupkey: false,
                    isCustomerGroup: false
                });
                groups.add(record);
            }

        });

        var settings = Ext.create('Shopware.apps.NewsletterManager.model.Settings'),
            values = settingsForm.getValues();

        // Copy the values from the form into the settings model
        settings.set(values);

        if(settingsForm.record.get('id') !== null){
            settings.set('id', settingsForm.record.get('id'));
        }
        settings.set('content', content);
        settings.set('groups', groups);
        settings.set('recipients', totalCount);
        if(values['dispatch'] == 1){
            settings.set('plaintext', false);
        }else{
            settings.set('plaintext', true);
        }
        var senderRecord = me.subApplication.senderStore.getById(values['senderId']);
        settings.set('senderName', senderRecord.get('name'));
        settings.set('senderMail', senderRecord.get('email'));

        return settings;
        

    },

    /**
     * Helper function that creates and returns a newsletter model from the current fields
     * @param record, optional. If given, the given record will be updated instead of creating a new
     */
    createNewsletterModel: function(record) {
        var me = this,
            newsletter, container, ctText, config,
            textStore, containerStore,
            settings = me.getSettings();

        // Create model and store for the text-field (content)
        ctText = Ext.create('Shopware.apps.NewsletterManager.model.ContainerTypeText', {
            headline: settings.get('subject'),
            content:settings.get('content'),
            image: '',
            link: '',
            alignment: 'left'
        });
        textStore = Ext.create('Ext.data.Store', { model: 'Shopware.apps.NewsletterManager.model.ContainerTypeText' });
        textStore.add(ctText);

        // Create model and store for the containers.
        container = Ext.create('Shopware.apps.NewsletterManager.model.Container', {
            value: '',
            type: 'ctText',
            description: '',
            position: 1
        });
        container.getTextStore = textStore;
        containerStore = Ext.create('Ext.data.Store', { model: 'Shopware.apps.NewsletterManager.model.Container' });
        containerStore.add(container);

        config =  {
            subject: settings.get('subject'),
            customerGroup: settings.get('customerGroup'),
            recipients: settings.get('recipients'),
            plaintext: settings.get('plaintext'),
            senderName: settings.get('senderName'),
            senderMail: settings.get('senderMail'),
            groups: settings.get('groups'),
            languageId: settings.get('languageId'),

            date :  null,
            locked: null

        };

        // Create model for the newsletter
        if(record == null) {
            newsletter = Ext.create('Shopware.apps.NewsletterManager.model.Mailing', config);
        }else{
            record.set(config);
            newsletter = record;
        }

        if(settings.get('id') !== null){
            newsletter.set('id', settings.get('id'));
        }

        newsletter.getContainersStore = containerStore;
        newsletter.getGroupsStore = settings.get('groups');

        return newsletter;
    },

    /**
     * Called after the user clicked the 'create preview' button in the newsletter window
     * @param tinyMCE
     */
    onOpenPreview: function(tinyMCE) {
        var me = this,
            newsletter = me.createNewsletterModel();

        newsletter.set('status', -1); // mark the newsletter as preview
        newsletter.set('id', null); // don't overwrite existing newsletters

        // The mail-method of the pre-existing newsletter controller reads the newsletters from db.
        // In order not to add additional overhead, simply write the newsletter to db and clean it up next time
        newsletter.save({
            callback: function(data, operation){
                var records = operation.getRecords(),
                    record = records[0],
                    rawData = record.getProxy().getReader().rawData;
                if(operation.success){
                    if(!Ext.isEmpty(rawData.data)){
                        // Open up a iframe and show the preview
                        var pos = location.href.search("/backend");
                        var url = location.href.substr(0, pos) + "/backend/Newsletter/view?id=" + rawData.data.id;

                        new Ext.Window({
                            title : '{s name=preview}Preview: {/s}' + newsletter.get('subject'),
                            width : 600,
                            height: 480,
                            layout : 'fit',
                            items : [{
                                xtype : "component",
                                id    : 'iframe-win',  // Add id
                                autoEl : {
                                    tag : "iframe",
                                    src : url
                                }
                            }]
                        }).show();

                    }

                }else{
                    Shopware.Notification.createGrowlMessage(me.snippets.saveNewsletter.errorTitle, me.snippets.saveNewsletter.errorMessage, me.snippets.growl);
                }
            }
        });


    }

});
//{/block}