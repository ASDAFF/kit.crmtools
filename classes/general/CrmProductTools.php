<?
if(!CModule::includeModule('rest'))
    return false;
class CrmProductTools extends CRestServer{

    public function __construct($params)
    {
        parent::__construct($params);
    }

    protected function processCall()
    {
        $start = 0;
        if(isset($this->query['start']))
        {
            $start = ($this->query['start']);
            unset($this->query['start']);
        }

        $callback = $this->getMethodCallback();
        
        if(!$callback)
        {
            throw new RestException('Method not found!', RestException::ERROR_METHOD_NOT_FOUND, self::STATUS_NOT_FOUND);
        }

        $result = call_user_func_array($callback, array($this->query, $start, $this));

        $result = array("result" => $result);
        if(is_array($result['result']))
        {
            if(isset($result['result']['next']))
            {
                $result["next"] = ($result['result']['next']);
                unset($result['result']['next']);
            }

            //Using array_key_exists instead isset for process NULL values
            if(array_key_exists('total', $result['result']))
            {
                $result['total'] = ($result['result']['total']);
                unset($result['result']['total']);
            }
        }

        if($this->securityClientState != null && $this->securityMethodState != null)
        {
            $result['signature'] = $this->getApplicationSignature();
        }

        return $result;
    }
    protected function getMethodCallback(){
        return array('CrmServiceTools', 'onRestServiceMethod');
    }
}
?>