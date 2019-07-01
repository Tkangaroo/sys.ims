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
use Lib\Logistic;

class Git extends BaseController
{
    public function pull()
    {

        $osParams = ESTools::getArgFromRequest($this->request(), null, 'getBody');
        try {
            $this->verifyGiteeHeaders();
            $ref = array_pop(explode('/', $osParams['ref']));
            if ($osParams['repository']['full_name'] !== 'speauty/ims') {
                throw new ESException('the repository must be speauty/ims, not '.$osParams['repository']['full_name'], Logistic::L_FAIL);
            }
            if ($ref !== 'es') {
                throw new ESException('the branch must be es, not '.$ref, Logistic::L_FAIL);
            }

            system('/bin/sh '.EASYSWOOLE_ROOT.'/bin/pull.sh');

            $this->logisticCode = Logistic::L_OK;
            $this->message = 'ok';
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

    /**
     * to verify the request headers
     * @throws ESException
     */
    private function verifyGiteeHeaders():void
    {
        $osHeaderArgs = $this->getGiteeHeaders();
        if (!isset($osHeaderArgs['user-agent']) || !$osHeaderArgs['user-agent']) {
            throw new ESException('the user-agent is undefined or empty', Logistic::L_FAIL);
        }
        if (!isset($osHeaderArgs['x-gitee-token']) || !$osHeaderArgs['x-gitee-token']) {
            throw new ESException('the x-gitee-token is undefined or empty', Logistic::L_FAIL);
        }
        if (!isset($osHeaderArgs['x-gitee-event']) || !$osHeaderArgs['x-gitee-event']) {
            throw new ESException('the x-gitee-event is undefined or empty', Logistic::L_FAIL);
        }
        if ($osHeaderArgs['user-agent'][0] !== 'git-oschina-hook') {
            throw new ESException(
                'the user-agent must be git-oschina-hook, not '.$osHeaderArgs['user-agent'][0],
                Logistic::L_FAIL);
        }
        if ($osHeaderArgs['x-gitee-token'][0] !== 'cat-bug') {
            throw new ESException(
                'the x-gitee-token is wrong, not '.$osHeaderArgs['user-agent'][0],
                Logistic::L_FAIL);
        }
        if ($osHeaderArgs['x-gitee-event'][0] !== 'Push Hook') {
            throw new ESException(
                'the x-gitee-event must be Push Hook, not '.$osHeaderArgs['user-agent'][0],
                Logistic::L_FAIL);
        }
        return ;
    }
}