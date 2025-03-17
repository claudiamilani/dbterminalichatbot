<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class ExternalRole extends Model
{
    use Searchable, Sortable;

    protected $guarded = ['id'];


    public function searchFilter($query, $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function($query)use($search){
            $query->where('external_role_id', 'ILIKE', "%$search%");
        });
    }


    public function authType()
    {
        return $this->belongsTo(AuthType::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'external_role_role','external_role_id','role_id');
    }
}
