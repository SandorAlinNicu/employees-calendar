<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;

class MarkDownType extends AbstractType {

    public function renderMarkDownType() {

        return $this->render('markdowntype.html.twig');
    }

}