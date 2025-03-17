<?php
/*
 * Copyright (c) 2024. Medialogic S.p.A.
 */

namespace App\LFT\Menu;

use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;

class SplitBtn
{
    private $routes_prefix;
    private $model;
    private $lang_prefix;
    private $policy_prefix;

    private $routes_with_modal = ['delete'];
    private $no_policy = false;


    public static function configure($model)
    {
        $instance = new self;
        $instance->model = $model;
        $instance->setRoutesPrefix('admin::');
        return $instance;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    public function setRoutesPrefix($prefix)
    {
        $this->routes_prefix = $prefix;
        return $this;
    }

    public function setLangPrefix($prefix)
    {
        $this->lang_prefix = $prefix;
        return $this;
    }

    public function noPolicy()
    {
        $this->no_policy = true;
        return $this;
    }

    public function setPolicyPrefix($prefix)
    {
        $this->policy_prefix = $prefix;
        return $this;
    }

    public function withModals(?array $routes_with_modal = [],$overwrite = false)
    {
        $this->routes_with_modal = $overwrite ? $routes_with_modal : array_merge($this->routes_with_modal, $routes_with_modal);
        return $this;
    }

    public function defaultOptions(?array $except_routes = [])
    {
        $modal_attributes = ['data-toggle' => 'modal','data-target' => '#myModalLg'];
        throw_unless($this->model instanceof Model, new Exception('A Model instance is required!'));
        $actions = [['policy_method' => 'view','lang_key' => 'view', 'route_name' => 'show','icon' => 'fas fa-search fa-fw'],['policy_method' => 'update','lang_key' => 'edit', 'route_name' => 'edit','icon' => 'fas fa-pen fa-fw','backTo' => route(join('.',[$this->routes_prefix,'index']))],['policy_method' => 'delete','lang_key' => 'delete', 'route_name' => 'delete','icon' => 'fas fa-trash-alt fa-fw']];

        $options = [];
        foreach($actions as $action){
            if(in_array($action['route_name'],$except_routes)) continue;
            $option = [];
            //$option['title'] = trans(strtolower(join('.', [$this->lang_prefix ?? str_plural(class_basename($this->model)), $action['lang_key'],'title'])));
            $option['label'] = trans('common.form.'.$action['lang_key']);
            $option['url'] = route(join('.',[$this->routes_prefix,$action['route_name']]),paramsWithBackTo($this->model->{$this->model->getKeyName()},$action['backTo'] ?? null));
            $option['show_if'] = $this->no_policy ? null : Auth::user()->can($action['policy_method'],$this->model);
            $option['icon'] = $action['icon'];
            $option['attributes'] = in_array($action['route_name'],$this->routes_with_modal) ? $modal_attributes : [];
            $options[$action['lang_key']] = $option;
        }

        return $options;
    }

}