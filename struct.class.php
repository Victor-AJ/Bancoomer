<?php
class StructStatus
{
	var $status;
	var $informeDetalle;
	
	function StructStatus ()
	{
		$this->status=true;
		$this->informeDetalle="inicializado";
	}
	
	function setStatus($myE)
	{
		$this->status=$myE;
	}
	
	function getStatus()
	{
		return $this->status;
	}
	
	function setInformeDetalle($myInf)
	{
		$this->informeDetalle=$myInf;
	}
	
	function getInformeDetalle()
	{
		return $this->informeDetalle;
	}
	
}
?>
