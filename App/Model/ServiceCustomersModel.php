<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:16
 */

namespace App\Model;


use App\Base\BaseModel;
use App\Utility\ESTools;
use Lib\Exception\ESException;
use Lib\Logistic;

class ServiceCustomersModel extends BaseModel
{
    protected $table = 't_service_customers';

    /**
     * create a service customer
     * @param array $form
     * @return bool
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function createServiceCustomerSingle(array $form):bool
    {
        var_dump($form);
        $data = [
            'customer_name' => '',
            'customer_contact_phone' => '',
            'customer_company_name' => '',
            'customer_id' => '',
            'customer_es_key' => '',
            'is_enable' => 0,
            'stock_update_callback_url' => '',
            'comments' => '',
            'create_at' => time(),
            'update_at' => time()
        ];

        foreach ($data as $k => &$v) {
            if (isset($form[$k])) $v = $form[$k];
        }

        $data['customer_id'] = $this->buildCustomerId();
        $data['customer_es_key'] = substr(MD5($data['customer_name']), rand(100,100000)%30, 2).$data['customer_id'];

        var_dump($data);

        // 查重
        $uniqueFilterWhere = [
            'customer_name' => $data['customer_name']
        ];

        if (ESTools::checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
            throw new ESException(Logistic::getMsg(Logistic::L_RECORD_NOT_UNIQUE), Logistic::L_RECORD_NOT_UNIQUE);
        }
        unset($k, $v,$form, $uniqueFilterWhere);
        return $this->getDb()->insert($this->table, $data);
    }

    /**
     * to build a customer id
     * @return string
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    private function buildCustomerId():string
    {
        $seed = '1234567890';
        do {
            $flag = true;
            $customerId = 'ES'.ESTools::buildRandomStr(4, $seed);
            if (ESTools::checkUniqueByAField($this->getDb(), $this->table, ['customer_id' => $customerId])) {
                $flag = false;
            }
        } while ($flag);
        return $customerId;
    }
}