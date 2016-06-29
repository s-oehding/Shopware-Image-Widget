/**
 * Shopware 4
 * Copyright © shopware AG
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
 */

/**
 * ExtJS Component for Media Widget Plugin
 */
Ext.define('Shopware.apps.Emotion.view.components.SoImageWidget', {

    /**
     * Extend from the base emotion component.
     */
    extend: 'Shopware.apps.Emotion.view.components.Base',

    /**
     * Set the defined xtype of the component as the new widget alias.
     */
    alias: 'widget.emotion-media-widget',

    /**
     * Initialize the component.
     */
    initComponent: function() {
        var me = this;

        // Call the parent component for init.
        me.callParent(arguments);

        // Create the media manager field.
        me.createMediaManagerField();

        // Create a new fieldset for the custom fields.
        me.createMediaWidgetFieldset();

        // Get the already created hidden input field.
        me.mediaManagerStoreField = me.getMediaStoreField();

        // Add the new fieldset to the emotion component.
        me.add(me.widgetFieldset);

        /**
         * Get single fields you've created with the helper functions in your `Bootstrap.php` file.
         */
        me.imageThumbnailField = me.getForm().findField('image_thumbnail');
        me.imageSrcField = me.getForm().findField('media_widget_store');
    },


    /**
     * Creates a new custom ExtJS component field.
     * In this example we create a Shopware MediaSelection field.
     *
     * @returns Shopware.form.field.MediaSelection
     */
    createMediaManagerField: function() {
        var me = this;

        return me.mediaManagerField = Ext.create('Shopware.form.field.MediaSelection', {
            buttonText: '{s name=emotion/component/image_widget/media/button_text}Select a file{/s}',
            listeners: {
                scope: this,
                selectMedia: me.onMediaSelection
            }
        });
    },

    /**
     * Creates a new fieldset for the emotion component configuration.
     *
     * @returns Ext.form.FieldSet
     */
    createMediaWidgetFieldset: function() {
        var me = this;

        return me.widgetFieldset = Ext.create('Ext.form.FieldSet', {
            title: '{s name=emotion/component/article_teaser/fieldset/title}Teaser Image{/s}',
            layout: 'anchor',
            defaults: { anchor: '100%' },
            items: [
                me.mediaManagerField
            ]
        });
    },

    /**
     * Event handler for the media selection field.
     * Will be fired when the user selected some files.
     * Gets the data of the selected files and saves them
     * to the hidden field as a json encoded string.
     *
     * @param field
     * @param records
     */
    onMediaSelection: function(field, records) {
        var me = this,
            cache = [];

        Ext.each(records, function(record) {
            cache.push(record.data);
        });

        me.mediaManagerStoreField.setValue(Ext.JSON.encode(cache));
        me.imageThumbnailField.setValue(cache[0]["path"]);
    },

    /**
     * Search the fieldset of the component
     * for the hidden input field and return it.
     *
     * @returns Ext.form.field.Hidden
     */
    getMediaStoreField: function() {
        var me = this,
            items = me.elementFieldset.items.items,
            storeField;

        Ext.each(items, function(item) {
            if(item.name === 'media_widget_store') {
                storeField = item;
            }
        });

        return storeField;
    }
});