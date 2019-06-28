<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:23
 */

namespace App\HttpController\Open;


use App\Base\BaseController;
use App\Utility\ESTools;

class Git extends BaseController
{
    public function pull()
    {

        $osHeaderArgs = $this->getGiteeHeaders();
        $osParams = ESTools::getArgFromRequest($this->request(), null, 'getBody');
        var_dump($osHeaderArgs);
        var_dump($osParams);
    }

    /**
     * to get header args from request of gitee
     * @return array|null
     */
    private function getGiteeHeaders():?array
    {
        $headersIdx = [
            'user-agent',
            'x-gitee-token',
            'x-gitee-event'
        ];
        $osHeaderArgs = ESTools::getArgFromRequest($this->request(), $headersIdx, 'getHeaders');
        unset($headersIdx);
        return $osHeaderArgs;
    }
}