<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImageUpload extends Component
{
    public $name;
    public $label;
    public $currentImage;
    public $required;
    public $accept;
    public $maxSize;
    public $allowedFormats;
    public $previewMaxHeight;
    public $containerClass;
    public $error;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param string $label
     * @param string|null $currentImage
     * @param bool $required
     * @param string $accept
     * @param string $maxSize
     * @param string $allowedFormats
     * @param string $previewMaxHeight
     * @param string $containerClass
     * @param string|null $error
     */
    public function __construct(
        $name = 'image',
        $label = 'Image',
        $currentImage = null,
        $required = false,
        $accept = 'image/*',
        $maxSize = '20MB',
        $allowedFormats = 'PNG, JPG, JPEG, GIF',
        $previewMaxHeight = '200px',
        $containerClass = '',
        $error = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->currentImage = $currentImage;
        $this->required = $required;
        $this->accept = $accept;
        $this->maxSize = $maxSize;
        $this->allowedFormats = $allowedFormats;
        $this->previewMaxHeight = $previewMaxHeight;
        $this->containerClass = $containerClass;
        $this->error = $error;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image-upload');
    }
}
