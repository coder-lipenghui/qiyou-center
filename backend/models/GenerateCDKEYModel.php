<?php
/**
 * Created by PhpStorm.
 * User: lipenghui
 * Date: 2019-05-28
 * Time: 14:46
 */

namespace backend\models;


class GenerateCDKEYModel extends TabCdkey
{
    public $generateNum;
    public function rules()
    {
        $parentRules= parent::rules();
        $myRules=[
            [['generateNum'],'required'],
            [['generateNum'],'integer','max'=>100000,'min'=>1],
        ];
        return array_merge($parentRules,$myRules);
    }
}