<?php
/**
 * Tool for testing webservice connection (wsdl acccess).
 * Structured/Script version
 * @author Bruno Wesley Correa de Almeida (bruno.correa.at@gmail.com)
 */

//set timeout 0
ini_set('max_execution_time', 0);
set_time_limit(0);

//set webservices (name => wsdl)
//@todo: read webservice list in config file (.ini)
$webservices = array(
  'correios' => 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl'
);

//set options
$options = array('encoding'=>'ISO-8859-1','cache_wsdl'=> WSDL_CACHE_NONE);

//statuslist empty
$statusList = array();

//errorlist empty
$errorList = array();

//check with new SoapClient

foreach($webservices as $name => $wsdl){
    try {
        $cliente = new SoapClient($wsdl,$options);

        //webservice response ok
        $statusList[$name] = true;
      } catch(Exception $e){
        //webservice no response
        $statusList[$name] = false;

        //get exception
        $errorList[$name]['message'] = $e->getMessage();
      }
}

//@todo: implements check connection with curl
/*
foreach($webservices as $name => $wsdl){
    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $wsdl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);

        $http_code =  curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $statusList[$name] = $http_code == 200;

        //set http code response in error message
        $errorList[$name]['message'] = $http_code != 200 ? "HTTP_CODE: {$http_code}" : '';
        curl_close($curl);
      } catch(Exception $e){
        //get exception
        $errorList[$name]['message'] = $e->getMessage();
      }
}*/

//basic-show status list
//------------------------------------------------------------------------
$isCommandLine = php_sapi_name() === 'cli';
$breakLine = ($isCommandLine ? "\n":"<br>");
$debug = false;

if($isCommandLine){
    $debug = in_array('--show-error',$argv);
} elseif(isset($_GET['show_error'])){
    $debug = $_GET['show_error'] == '1';
}
//------------------------------------------------------------------------
echo ":::: PING WEBSERVICE SOAP - BEGIN ::::$breakLine ";

foreach($statusList as $name => $status){
  echo "$breakLine- [{$name}]: " . ($status ? "[OK]" : "[OFF-LINE]");

    //show exception message
    if($debug && array_key_exists($name, $errorList)){
      echo "$breakLine $breakLine [";
      echo $errorList[$name]['message'];
      echo "] $breakLine $breakLine";
    }
}
echo "$breakLine $breakLine:::: PING WEBSERVICE SOAP - END ::::";
//------------------------------------------------------------------------
