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
use Lib\Exception\ESException;

class Git extends BaseController
{
    public function pull()
    {

        $osHeaderArgs = $this->getGiteeHeaders();
        $osParams = ESTools::getArgFromRequest($this->request(), null, 'getBody');
        try {

        } catch (ESException $e) {
            $this->message = $e->report();
            $this->logisticCode = $e->getCode();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
            $this->logisticCode = $e->getCode();
        }
        ESTools::writeJsonByResponse($this->response(), $this->logisticCode, $this->message);
        return false;

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

    private function verifyGiteeHeaders():void
    {

        return
    }
}