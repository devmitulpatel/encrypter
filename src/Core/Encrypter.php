<?php


namespace M\Core;
ini_set('memory_limit', '-1');


set_time_limit(-1);
class Encrypter
{


    public static function test(){


        $a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+=-";

        $d=str_split($a);

        $array=[];

        for ($x=0;$x<100;$x++){
            $array[$x]=[];
            foreach ($d as $k){
               $rByte=random_bytes(1);

               while (array_key_exists($rByte,$array[$x])){
                   $rByte=random_bytes(1);

               }

                $array[$x][$rByte]=$k;
            }

          //  var_dump(count($array[$x]));
        }
        file_put_contents(str_replace('\\','/',__DIR__).'/algo.bin', serialize($array));
    }


    public static function encrypt($str=""){
        $rstr="";
        $data=  unserialize(file_get_contents(str_replace('\\','/',__DIR__).'/algo.bin'));




        $strEx=str_split($str);
        $strSecret=[];


        $randdomKey=rand(0,count($data)-1);


        $sData=$data[$randdomKey];
        while(strlen($randdomKey) !=2){
            $randdomKey=rand(0,count($data)-1);
        }

        $randdomKeyEx=str_split($randdomKey);



        foreach ($strEx as  $k){
            if(in_array($k,$sData))$strSecret[]=array_search($k,$sData);
        }


        $final=[
            array_search(reset($randdomKeyEx),$data[0])  ,
            implode('',$strSecret),
            array_search( end($randdomKeyEx),$data[0])

        ];

        dd(implode('',$final));


       // var_dump(base64_encode($str));
        //var_dump( implode('_',[$salt,$rstr,$salt2]) );

        return $rstr;

    }

    public static  function  decrypt($str=""){
        $rstr="";

      //  var_dump(password_verify($str));

        $rstr=base64_decode ($str);
      // var_dump($rstr);

        return $rstr;
    }

    public static  function  encryptLimit($str="",$min=30){

        $rstr="";

        return $rstr;

    }

    public static  function  decryptLimit($str="",$min=30){

        $rstr="";

        return $rstr;

    }




}