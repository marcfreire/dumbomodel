<?

class Ann{

     public function __construct (){
     
     
     
     }


}


class ReflectionClassAnnotation{


      public $reflection = null;
      public $annotations;

      public function __construct ($object){


        $this->reflection = new ReflectionClass($object);
        $doc = $this->reflection->getDocComment();
        
        $doc = str_replace("*","",$doc);
        $doc = str_replace("/","",$doc);
        $doc = split ("\n",$doc);
        
        $cont =0;
        for($i=0;$i<count($doc);$i++){
        
               $line = $doc[$i];
               $line = str_replace(" ","",$line);
               $len = $i== count($doc)-1?strlen($line):strlen($line)-1;
               if($len>0 && $line[0]=='@'){
               

                  #$annotations[$cont] = $line;
                  $pos = stripos ($line ,"(");
                  $chave = substr($line,1,$pos-1);
                  echo "$line:".$chave."<br>";
               }
               

        
        }
        #print_r($annotations);

     }
     
     public function getAnnotation($annotation){
     

     
     }

}

