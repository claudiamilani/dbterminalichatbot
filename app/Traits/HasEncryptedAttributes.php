<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

/**
 * Created by PhpStorm.
 * User: francesco
 * Date: 15/09/18
 * Time: 17:07
 */

namespace App\Traits;

use Exception;

trait HasEncryptedAttributes
{
    protected function getEncrypted() {
        return property_exists($this, 'encrypted') ? $this->encrypted : [];
    }

    public function attributesToArray() {
        $attributes = parent::attributesToArray();
        foreach($this->getEncrypted() as $key) {
            if(array_key_exists($key, $attributes)) {
                $attributes[$key] = decrypt($attributes[$key]);
            }
        }
        return $attributes;
    }

    public function getAttributeValue($key) {
        if(in_array($key, $this->getEncrypted())) {
            return decrypt($this->attributes[$key]);
        }
        return parent::getAttributeValue($key);
    }

    public function setAttribute($key, $value): static
    {
        if(in_array($key, $this->getEncrypted())) {
            $this->attributes[$key] = encrypt($value);
        } else {
            parent::setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public static function hash($value): string
    {
        if(empty(config('lft.bidx_key'))){
            throw new Exception('Missing bidx_key. Please configure bidx_key in LFT configuration file.');
        }
        return hash_hmac('sha256', strtoupper($value), config('lft.bidx_key'));
    }
}