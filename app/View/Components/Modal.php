<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $title;
    public $name;
    public $show;
    public $maxWidth;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $name
     * @param  bool  $show
     * @param  string  $maxWidth
     * @param  string  $title
     */
    public function __construct($name = null, $show = false, $maxWidth = '2xl', $title = 'Modal')
    {
        $this->name = $name;
        $this->show = $show;
        $this->maxWidth = $maxWidth;
        $this->title = $title;
    }

    public function render()
    {
        return view('components.modal');
    }
}
