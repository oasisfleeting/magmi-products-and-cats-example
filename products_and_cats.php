<?php

require_once("../../inc/magmi_defs.php");
require_once("../inc/magmi_datapump.php");


         
         class TestLogger
         {
                public function log($data,$type)
                {
                        echo "$type:$data\n";
                }  
         }
   
         $dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport");
         
         $dp->beginImportSession("products_and_cats","create",new TestLogger());
       

        function xml2array ( $xmlObject, $out = array () )
        {
            foreach ( (array) $xmlObject as $index => $node )
                $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
     
            return $out;
        }
                  
        $xml= simplexml_load_file("products_and_cats.xml");
        $obj = array();
        $obj = xml2array($xml,$obj);
        //$pobj = new stdClass();
        $i=0;
        foreach($obj['wrap'] as $ob)
        {
               $o = get_object_vars($ob);
               foreach($o as $key=>$val)
               {
			
                       if(!empty($val) || !isset($val))
                       {				
                                if(!stristr($key,'image') && !stristr($key,'thumbnail'))
				{			
					$val = ucwords(strtolower($val));					
				}
				if($key == 'store')
				{
					$val = strtolower($val);
				}				
				
			        $val = str_replace('"','',strip_tags($val));
				/*if($key == 'categories')
				{
					$catarray = explode(' ',$val);
					$val = $catarray[0];
				}*/
                       }
                       else
                       {
                          unset($o[$key]);			  
			  //continue;
			  //$val = '';
			  
                       }
		       
		       $o[$key] = $val;
               }               
              //echo "<pre>";
              //print_r($o);
              //echo "</pre>";
              $item = $o;
	      //exit();
              $dp->ingest($item);            
        }
      
        $dp->endImportSession();
   

?>
