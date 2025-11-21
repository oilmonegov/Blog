<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PublicLayout extends Component
{
    public ?string $metaTitle;

    public ?string $metaDescription;

    public $post;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $metaTitle = null, ?string $metaDescription = null, $post = null)
    {
        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->post = $post;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.public');
    }
}
