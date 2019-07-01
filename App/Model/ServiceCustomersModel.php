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

        // 查重
        $uniqueFilterWhere = [
            'customer_name' => $data['customer_name'],
            'stock_update_callback_url' => [$data['stock_update_callback_url'], '=', 'OR']
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
            $customerId = 'ES'.ESTools::buildRandomStr(4, $seed);
            if (!ESTools::checkUniqueByAField($this->getDb(), $this->table, ['customer_id' => $customerId])) {
                break;
            }
        } while (1);
        return $customerId;
    }

    /**
     * to update the customer
     * @param array $customer
     * @return bool
     * @throws ESException
     * @throws \Throwable
     */
    public function update(array $customer):bool
    {
        $where = [
            'id' => $customer['id']
        ];
        $data = [
            'customer_company_name' => $customer['customer_company_name'],
            'is_enable' => $customer['is_enable'],
            'stock_update_callback_url' => $customer['stock_update_callback_url'],
            'comments' => $customer['comments']
        ];
        $oldCustomer = $this->getOne(['stock_update_callback_url'], $where);
        if (!$oldCustomer) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_RECORD_NOT_FOUND),
                Logistic::L_RECORD_NOT_FOUND
            );
        } else {
            if ($oldCustomer['stock_update_callback_url'] !== $data['stock_update_callback_url']) {
                $uniqueFilterWhere = [
                    'stock_update_callback_url' => [$data['stock_update_callback_url'], '=']
                ];

                if (ESTools::checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
                    throw new ESException(Logistic::getMsg(Logistic::L_RECORD_NOT_UNIQUE), Logistic::L_RECORD_NOT_UNIQUE);
                }
            }
        }

        ESTools::quickParseArr2WhereMap($this->db, $where, true);
        $data['update_at'] = time();
        return $this->db->update($this->table, $data, 1);
    }
}