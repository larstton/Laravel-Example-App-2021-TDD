<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CenteredCard extends Component
{
    public $title;
    public $linkTarget = null;
    public $linkText = null;
    public $formAction = null;
    public $formMethod = null;
    public $formButtonText = null;

    public function __construct($title = 'Great!', $linkTarget = null, $linkText = null, $formAction = null, $formMethod = 'POST', $formButtonText = 'Yes')
    {
        $this->title = $title;
        $this->linkTarget = $linkTarget;
        $this->linkText = $linkText;
        $this->formAction = $formAction;
        $this->formMethod = $formMethod;
        $this->formButtonText = $formButtonText;
    }

    public function render()
    {
        return view('klick.components.centered-card');
    }

    public function hasLink()
    {
        return $this->linkTarget && $this->linkText;
    }

    public function hasForm()
    {
        return $this->formAction && $this->formMethod && $this->formButtonText;
    }

    public function getLinkTarget()
    {
        return $this->linkTarget;
    }

    public function getLinkText()
    {
        return $this->linkText;
    }

    public function getFormAction()
    {
        return $this->formAction;
    }

    public function getFormMethod()
    {
        return $this->formMethod;
    }

    public function getFormButtonText()
    {
        return $this->formButtonText;
    }
}
