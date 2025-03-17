<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * Author: Francesco Tesone
 * Email: tesone@medialogic.it
 * Date: 04/09/2018
 * Time: 12:45
 */

namespace App\Traits;


trait TranslatedValidation
{
    abstract public function getTranslationFile() :string;

    private function getTranslatedAttributes($attributes){
        foreach($attributes as $attribute => $replacement){
            $attributes[$attribute] = trans($this->getTranslationFile().".attributes.$attribute");
        }
        return $attributes;
    }
}