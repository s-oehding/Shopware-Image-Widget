<?php

/**
 * Shopware_Plugins_Frontend_SoImageWidget_Bootstrap
 *
 * Shopware emotion Element Plugin
 * Create a custom ExtJS component for the emotion element configuration.
 */
class Shopware_Plugins_Frontend_SoImageWidget_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public $component;

    /**
     * Returns all necessary information about the plugin.
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'author' => 'Sören Oehding',
            'supplier' => 'SO_DSGN',
            'description' => 'Einfaches Bilder Widget für die Einkaufswelten',
            'support' => 'Shopware Forum',
            'link' => 'https://so-dsgn.de'
        );
    }

    /**
     * Returns the name of the plugin.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Bilder Widget';
    }

    /**
     * Returns the version of the plugin.
     *
     * @return string
     */
    public function getVersion()
    {
        return "1.0.0";
    }

    /**
     * The install function of the plugin.
     *
     * @return bool
     */
    public function install()
    {
        // Save the new component
        $this->component = $this->createComponent();

        // Create the necessary fields for the component
        $this->createComponentFields();

        // Register all necessary events to handle the data.
        $this->registerEvents();

        return true;
    }

    /**
     * The enable function of the plugin.
     *
     * @return bool
     */
    public function enable()
    {
        // Set Cache invalid to recompile all less files
        return [
            'success' => true,
            'invalidateCache' => ['template', 'theme']
        ];
    }

    /**
     * The uninstall function of the plugin.
     *
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * Create and return the new emotion component with custom xtype.
     *
     * @return \Shopware\Models\Emotion\Library\Component
     */
    public function createComponent()
    {
        return $this->createEmotionComponent(array(
            'name' => 'Bilder',
            'xtype' => 'emotion-media-widget',
            'template' => 'image_widget',
            'cls' => 'emotion-image-widget',
            'description' => 'Einfaches Einkaufswelten-Element für Bilder' 
        ));
    }

    /**
     * Create a hidden input field for the emotion component configuration.
     * The hidden field will be used to store the data of the custom ExtJS Component.
     * Data will be stored as a JSON string.
     */
    public function createComponentFields()
    {
        $this->component->createTextField(
            array(
                'name' => 'teaser_text',
                'fieldLabel' => 'Teaser Text',
                'supportText' => 'Der Banner Text',
                'allowBlank' => true
            )
        );

        $this->component->createTextField(
            array(
                'name' => 'image_link',
                'fieldLabel' => 'Image Link',
                'supportText' => 'Banner-verlinkung',
                'allowBlank' => true
            )
        );

        $this->component->createTextField(
            array(
                'name' => 'image_title',
                'fieldLabel' => 'Image Title-Tag',
                'supportText' => 'Title Tag des Bildes. Wird im Browser bei mouseover als Tooltip angezeigt und hilft screenreadern bei der Orientierung',
                'allowBlank' => true
            )
        );

        $this->component->createTextField(
            array(
                'name' => 'image_alt_tag',
                'fieldLabel' => 'Image Alt-Tag',
                'supportText' => 'Der Alt-Text wird, wie der Name vermuten läßt, alternativ angezeigt. Also nur, wenn das Bild aus welchen Gründen auch immer, nicht zu sehen ist. Das kann verschiedene Gründe haben: Server-Problem, falschen Pfadangabe, Bilder per Browser deaktiviert, oder eben eine „Lesehilfe“ für Benutzer mit Sehschwäche oder Blindheit. Aus Sicht des W3C ist der Alt-Text daher auch eine „Pflicht-Angabe“. Der Browser kann das Bild in aller Regel auch ohne Alt-Text anzeigen, aber es ist dringend anzuraten, den Alt-Text zu setzen.',
                'allowBlank' => true
            )
        );

        $this->component->createHiddenField([
            'name' => 'image_thumbnail'
        ]);

        $this->component->createHiddenField(array(
            'name' => 'media_widget_store',
            'allowBlank' => true
        ));
    }

    /**
     * Register on the emotion filter event to handle
     * the saved data before passing it to the template.
     */
    public function registerEvents()
    {
        $this->subscribeEvent(
            'Shopware_Controllers_Widgets_Emotion_AddElement',
            'onEmotionAddElement'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Widgets_Campaign',
            'extendsEmotionTemplates'
        );

        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'addLessFiles'
        );

        /**
         * Subscribe to the post dispatch event of the emotion backend module to extend the components.
         */
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Emotion',
            'onPostDispatchBackendEmotion'
        );
    }

     /**
     * Extends the backend template to add the grid component for the emotion designer.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchBackendEmotion(Enlight_Controller_ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Emotion $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('backend/emotion/image_widget/view/detail/elements/image_widget.js');
    }

    /**
     * Event handler for the emotion filter event.
     * It decodes the stored JSON string and passes the values to the emotion data.
     *
     * @param Enlight_Event_EventArgs $arguments
     * @return mixed
     */
    public function onEmotionAddElement(Enlight_Event_EventArgs $arguments)
    {
        $data = $arguments->getReturn();
        $files = array();

        if (isset($data['media_widget_store']) &&
            !empty($data['media_widget_store'])
        ) {
            $files = json_decode($data['media_widget_store'], true);
        }

        $data['files'] = $files;

        return $data;
    }

    /**
     * Provide the file collection for less
     *
     * @param Enlight_Event_EventArgs $args
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function addLessFiles(Enlight_Event_EventArgs $args)
    {
        $less = new \Shopware\Components\Theme\LessDefinition(
        //configuration
            array(),

            //less files to compile
            array(
                __DIR__ . '/Views/frontend/_public/src/less/all.less'
            ),

            //import directory
            __DIR__
        );

        return new Doctrine\Common\Collections\ArrayCollection(array($less));
    }
}
