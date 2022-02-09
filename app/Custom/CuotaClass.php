<?php namespace App\Custom;

class CuotaClass
{
    private $_tasaInteres;

    private $_tasaTaxes;

    private $_costoPlaca;

    private $_costoDocs;

    private $_precio;

    private $_downpayment;

    private $_capital;

    private $_nmeses;

    private function clean_valor($valor)
    {
        return str_replace(array('US$ ',',', ' %'),array('','',''),$valor);
    }

    public function setTasaInteres($tasa)
    {
        //$this->_tasaInteres     =   ($this->clean_valor($tasa)/100)/12;
        $this->_tasaInteres     =   number_format(floatval($this->clean_valor($tasa)/100),4,'.','');
    }

    public function setTasaTaxes($taxes)
    {
        $this->_tasaTaxes     =   number_format(floatval($this->clean_valor($taxes)/100),4,'.','');
    }

    public function setPeriodos($nmeses)
    {
        $this->_nmeses          =  $this->clean_valor($nmeses);
    }

    public function setCostoPlaca($valor)
    {
        $this->_costoPlaca         =   number_format(floatval($this->clean_valor($valor)),2,'.','');
    }

    public function setCostoDocs($valor)
    {
        $this->_costoDocs         =   number_format(floatval($this->clean_valor($valor)),2,'.','');
    }

    public function setPrecio($valor)
    {
        $this->_precio         =   number_format(floatval($this->clean_valor($valor)),2,'.','');
    }

    public function setDownPayment($valor)
    {
        $this->_downpayment         =   number_format(floatval($this->clean_valor($valor)),2,'.','');
    }

    public function calcularFinanceND()
    {
        $subtotal   =   floatval($this->_precio) + floatval($this->_costoPlaca) + floatval($this->_costoDocs);
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaInteres));
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaTaxes));

        return number_format($subtotal,2,'.','');
    }

    public function calcularFinance()
    {
        $subtotal   =   floatval($this->_precio) + floatval($this->_costoPlaca) + floatval($this->_costoDocs);
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaInteres));
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaTaxes));

        return number_format(floatval($subtotal) - floatval($this->_downpayment),2,'.','');
    }

    public function getPagoMensual()
    {
        $subtotal   =   floatval($this->_precio) + floatval($this->_costoPlaca) + floatval($this->_costoDocs);
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaInteres));
        $subtotal   =   floatval($subtotal) + (floatval($subtotal) * floatval($this->_tasaTaxes));
        $subtotal   =   floatval($subtotal) - floatval($this->_downpayment);

        return number_format(($subtotal / $this->_nmeses),2,'.','');
    }



    public function setCapital($capital)
    {
        $this->_capital         =   number_format(floatval($this->clean_valor($capital)),2,'.','');
    }

    public function getPagoMensual3()
    {
        $pago_mensual   =   (($this->_capital*$this->_tasaInteres) + $this->_capital) / $this->_nmeses;
        return $pago_mensual;
    }

    public function getPagoMensual2()
    {
        $pago_mensual   =   ($this->_capital*$this->_tasaInteres*(pow((1+$this->_tasaInteres),($this->_nmeses))))/((pow((1+$this->_tasaInteres),($this->_nmeses)))-1);
        return $pago_mensual;
    }
}
