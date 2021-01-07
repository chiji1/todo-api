<?php

namespace App\Helpers;


use App\Models\Admin\Network;
use App\Models\General\Role;
use App\Models\User\Flip;
use App\Models\User\Pin;
use App\Models\User\Wallet;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\Response;

trait Utilities
{
//    use ApiResponse;

    public function array_flatten($array) {
        if (!is_array($array)) {
            return false;
        }
        $return = array();
        foreach ($array as $key => $value) {
            if (is_array($value)){
                $return = array_merge($return, $this->array_flatten($value));
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * @param $table
     * @param array $where conditions to check where retrieving
     * @param array $values values to return
     * @return \Illuminate\Database\Query\Builder
     */
    public function getOneColumn($table, $where = [], $values = []) {
        return DB::table($table)->where($where)->select($values)->first();
    }

    /**
     * @param $table
     * @param array $where conditions to check where retrieving
     * @param array $values values to return
     * @return \Illuminate\Support\Collection
     */
    public function getManyColumn($table, $where = [], $values = []) {
        return DB::table($table)->where($where)->select($values)->get();
    }

}
