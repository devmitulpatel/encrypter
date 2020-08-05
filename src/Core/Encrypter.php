<?php


namespace M\Core;
ini_set('memory_limit', '-1');
set_time_limit(-1);
define('MASTER_MODE','aes-256-cbc-hmac-sha1',true);
define('MASTER_KEY',"��'��}�U�ʭ.\b�S��x�d�6�~\�ȩw������}]�dd�*�*�a����\v��O��,�҅,6^*aR�*�!I��/F�t�-��F5�",true);
define('MASTER_IV',(string)"�P�J{:531315",true);




class Encrypter
{


    public static function test($level=2){

        $a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+=-";
       $a="0123456789abcdefghijklmnopqrstuvwxyz";

        $d=str_split($a);

        $array=[];

        for ($x=0;$x< $level ;$x++){
            $array[$x]=[];
            foreach ($d as $k){

               //$rByte=random_bytes(1);
               $rByte=$d[ array_rand($d)];

               while (array_key_exists($rByte,$array[$x])){
                   //$rByte=random_bytes(1);
                   $rByte= $d[ array_rand($d)];

               }

              $array[$x][$rByte]=$k;
            }
        //    if(count($array[$x]) !=77 || false)unset($array[$x]);
           if(count($array[$x]) !=36 )unset($array[$x]);
           // var_dump(" Count ".count($array[$x]));
          //  var_dump(count($array[$x]));
        }


        //$array= array_values($array);

     //   dd($array);
        file_put_contents(str_replace('\\','/',__DIR__).'/algo.bin', serialize($array));

    }
    public static function test2($level=100){

        $a="0123456789abcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+=-";
      // $a="0123456789abcdefghijklmnopqrstuvwxyz";

        $d=str_split($a);
      //  dd($d);
        $array=[];

        for ($x=0;$x<$level;$x++){
            $array[$x]=[];
            foreach ($d as $k){

               //$rByte=random_bytes(1);
               $rByte=$d[ array_rand($d)];

               while (array_key_exists($rByte,$array[$x])){
                   //$rByte=random_bytes(1);
                   $rByte= $d[ array_rand($d)];

               }

              $array[$x][$k]= $rByte;
            }
         //   dd(count($array[$x]));
        //    if(count($array[$x]) !=77 || false)unset($array[$x]);
               if(count($array[$x]) !=51)unset($array[$x]);
          //  var_dump(count($array[$x]));
        }
      //  dd($array);
        



        file_put_contents(str_replace('\\','/',__DIR__).'/algo.bin', serialize($array));

    }

    public static function randStr($digit=4,$type=1){


        switch ($type){
            case 1:
                //$a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+=-";
                $a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                break;
        }
        $aSplit=str_split($a);
        $str=[];
        for ($x=0;$x<$digit;$x++){
            $str[]=$aSplit[array_rand($aSplit)];
        }
        return implode('',$str);

    }

    public static function generateKeys($keyCount=10,$mode=null){





        $alog=['aes-128-cfb8','aes-256-cbc-hmac-sha256','aes-256-cbc-hmac-sha1'];

        if($mode==null)$mode=$alog[array_rand($alog)];
      //  dd(openssl_get_cipher_methods());

        for ($x=0;$x<$keyCount;$x++){

            $data[$x]=[
                'mode'=>$mode,
                'keyRaw'=>self::randStr(128)
            ];
            //dd($mode);
            //dd(openssl_cipher_iv_length($data[$x]['mode']));
            $data[$x]['key']=base64_decode($data[$x]['keyRaw']);
            $data[$x]['iven']=openssl_cipher_iv_length($data[$x]['mode']);
            $data[$x]['iv']=openssl_random_pseudo_bytes($data[$x]['iven']);



          //  $data[$x]['mode']= openssl_encrypt($data[$x]['mode'], MASTER_MODE, MASTER_KEY, 1, MASTER_IV);

            unset($data[$x]['iven']);
            unset($data[$x]['keyRaw']);

            $data[$x]=self::encryptWhole($data[$x]);

           // $data[$x]
          //  dd($data);


        }


   //     dd($data);

        file_put_contents(str_replace('\\','/',__DIR__).'/algo.bin', serialize($data));



    }
    public static function encryptWhole(array $a):array{

        $outArray=[];
        foreach ( $a as $k=>$v){
            $outArray[ openssl_encrypt($k, MASTER_MODE, MASTER_KEY, 1, MASTER_IV) ]=openssl_encrypt($v, MASTER_MODE, MASTER_KEY, 1, MASTER_IV);

        }

        return $outArray;
    }

    public static function decryptWhole(array $a):array{
        $outArray=[];
        foreach ( $a as $k=>$v){
            $outArray[
                openssl_decrypt($k, MASTER_MODE, MASTER_KEY, 1, MASTER_IV)
            ]=
                openssl_decrypt($v, MASTER_MODE, MASTER_KEY, 1, MASTER_IV);

        }

        return $outArray;
    }


    public static function encrypt($str=""){
        $finalStr="";


        $data=  unserialize(file_get_contents(str_replace('\\','/',__DIR__).'/algo.bin'));



        $randomKey=0;
        while(strlen((string)$randomKey)!=2){
            $randomKey=array_rand($data);
        }

        $algoData=self::decryptWhole($data[$randomKey]);


        $secretStr=openssl_encrypt($str, $algoData['mode'], $algoData['key'], 1, $algoData['iv']);

        $randomKeySplit=str_split((string)$randomKey);


        $finalStr=implode('',[reset($randomKeySplit),$secretStr,end($randomKeySplit)]);

        return $finalStr;


    }



    public static function decrypt($str){

        $data=  unserialize(file_get_contents(str_replace('\\','/',__DIR__).'/algo.bin'));
        $decodredStr="";

        $strExpo=str_split($str);

        $randomKeyExpo[]=array_shift($strExpo);
        $randomKeyExpo[]=array_pop($strExpo);

        $randomKey=implode('',$randomKeyExpo);

        $algoData=self::decryptWhole($data[$randomKey]);

        $decodredStr=openssl_decrypt(implode('',$strExpo), $algoData['mode'], $algoData['key'], 1, $algoData['iv']);

        return$decodredStr;
    }




    public static  function  encryptLimit($str="",$min=30){

        $rstr="";

        return $rstr;

    }

    public static  function  decryptLimit($str="",$min=30){

        $rstr="";

        return $rstr;

    }




    //p3xj7fg
    //g7b6ld6
    //t45drvq



}