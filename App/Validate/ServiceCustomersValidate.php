<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:19
 */

namespace App\Validate;


use App\Base\BaseValidate;

class ServiceCustomersValidate extends BaseValidate
{
    protected function setIdColumn():void
    {
        $this->addColumn('id','主键')->required()->notEmpty()->integer();
        return ;
    }

    protected function setCustomerNameColumn():void
    {
        $this->addColumn('customer_name', '客户名')->required()->notEmpty()->alphaDash()->betweenLen(2, 10);
        return ;
    }

    protected function setCustomerContactPhoneColumn():void
    {
        $this->addColumn('customer_contact_phone', '手机号')->required()->notEmpty()->length(11)->phone();
        return ;
    }

    protected function setCustomerCompanyNameColumn():void
    {
        $this->addColumn('customer_company_name', '客户公司名')->required()->notEmpty()->alphaDash()->betweenLen(2, 100);
        return ;
    }

    protected function setCustomerIdColumn():void
    {
        $this->addColumn('customer_id', '客户ID')->required()->notEmpty()->regex('/ES\d{4}/')->length(6);
        return ;
    }

    protected function setCustomerEsKeyColumn():void
    {
        $this->addColumn('customer_es_key', '客户KEY')->required()->notEmpty()->alphaDash()->length(8);
        return ;
    }

    protected function setIsEnableColumn():void
    {
        $this->addColumn('is_enable','是否激活')->required()->inArray([0,1], false, '不在可选值[0,1]内');
        return ;
    }

    protected function setStockUpdateCallbackUrlColumn():void
    {
        $this->addColumn('stock_update_callback_url', '回调地址')->required()->notEmpty()->activeUrl()->betweenLen(2, 20);
        return ;
    }

    protected function setCommentsColumn():void
    {
        $this->addColumn('comments', '备注')->required()->notEmpty()->alphaDash()->betweenLen(2, 50);
        return ;
    }
}