<?php

namespace Core\Admin\Form;

/**
 * Basic File-Upload Element
 * @package Core\Admin\Form
 */
class File implements Element {

    /**
     * The Title of the opening modal-box
     * @var string
     */
    private $title;

    /**
     * The Button-Text in the opening modal-box
     * @var string
     */
    private $button;

    /**
     * The Upload-Button Text
     * @var string
     */
    private $upload;

    /**
     * Array of accepted File-Types Only dispaly items of a specific MIME type (Ex: 'image', 'image/png', 'application/pdf')
     * @var string[]
     */
    private $types;

    /**
     * The name attribute of the File-Upload
     * @var string
     */
    private $name;

    /**
     * The File-ID of the currently selected Image
     * @var null|int
     */
    private $value;

    /**
     * File constructor.
     * @param string $name The name attribute of the File-Upload
     * @param string $upload The Upload-Button Text
     * @param array $types Array of accepted File-Types Only dispaly items of a specific MIME type (Ex: 'image', 'image/png', 'application/pdf')
     * @param string $title The Title of the opening modal-box
     * @param string $button The Button-Text in the opening modal-box
     */
    public function __construct(string $name, string $upload = "Datei hochladen", array $types = [], string $title = "Datei auswählen", string $button="Auswahl bestätigen"){
        $this->name = $name;
        $this->upload = $upload;
        $this->button = $button;
        $this->types = $types;
        $this->title = $title;
        $this->value = null;
    }

    /**
     * Renders the File Upload Element
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $hasValue = false;
        $image = '';

        if( isset($this->value) && !empty($this->value) ) {
            $image .= '<img src="' . wp_get_attachment_image_src($this->value, 'thumbnail', true)[0] . '" />';
            $hasValue = true;
        }

        $dataString = '';
        $data = [
            'title' => $this->title,
            'button' => $this->button,
            'text' => base64_encode($this->upload),
            'types' => implode('', $this->types)
        ];
        foreach ($data as $key => $value){
            $dataString .= ' data-' . $key . '="' . addslashes($value) . '"';
        }

        echo '
        <div class="core-file">
            <a class="' . (($hasValue)?'':'button') . ' core-file__button" ' . trim($dataString) . '>' . (($hasValue)?$image:$this->upload) . '</a>
            <input class="core-file__input" type="hidden" name="' . $this->name . '" value="' . $this->value . '" />
            <a href="#" class="core-file__remove" style="' . ((!$hasValue)?'display: none':'') . '">entfernen</a>
        </div>
        ';
    }

    /**
     * Saves the File on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void{
         $value = $dispatcher->getValue($this->name);
         $dispatcher->save($this->name,$value);
     }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $this->value = $dispatcher->get($this->name);
    }
}