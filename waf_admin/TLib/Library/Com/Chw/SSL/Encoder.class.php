<?php
namespace Com\Chw\SSL;
class Encoder{
    
	public static function encode($data)
	{
	   $fileName=__FILE__;
	   $dirName=dirname($fileName);
	   $fileName=sprintf('%s/rsa.private',$dirName); 
	   
	   $privateKeyContent=file_get_contents($fileName);
	   $pi_key=openssl_pkey_get_private($privateKeyContent);
	   
	   $encrypted = "";
	   
	   openssl_private_encrypt($data,$encrypted,$pi_key);
	   
	   $data=base64_encode($encrypted);
	   
	   return $data;
	   
	}
	
	
	public static function decode($data)
	{
	   $fileName=__FILE__;
	   $dirName=dirname($fileName);
	   $fileName=sprintf('%s/rsa.public',$dirName); 
	   
	   $publicKeyContent=file_get_contents($fileName);
	   $pu_key=openssl_pkey_get_public($publicKeyContent);
	   
	   $decrypted="";
	   
	   openssl_public_decrypt(base64_decode($data),$decrypted,$pu_key);
	   
	   $data=$decrypted;
	   
	   return $data;
	   
	}
	
	public static function encodeBig($data)
	{

	   
	   $fileName=__FILE__;
	   $dirName=dirname($fileName);
	   $fileName=sprintf('%s/rsa_private',$dirName);
	   $privateKeyContent=file_get_contents($fileName);



	   $pi_key=openssl_pkey_get_private($privateKeyContent);

	   $encrypted = '';
	   $output='';
	   $maxLen=80;
	   while($data)
	   {
		   $input= substr($data,0,$maxLen);
		   $data=substr($data,$maxLen);
		   openssl_private_encrypt($input,$encrypted,$pi_key);
		   $output.=$encrypted;
	   }
	   
	   $data=base64_encode($output);
	   
	   return $data;
	   
	}
	
	
	public static function decodeBig($data)
	{

	   $data=base64_decode($data);

	   $fileName=__FILE__;
	   $dirName=dirname($fileName);

	   $fileName=sprintf('%s/rsa_private',$dirName);
	   
	   $publicKeyContent=file_get_contents($fileName);

	   $pu_key=openssl_pkey_get_private($publicKeyContent);
	   
	   $decrypted='';
	   $output='';
	   $maxLen=128;


	   while($data)
	   {
		   $input= substr($data,0,$maxLen);
		   $data=substr($data,$maxLen);

		   openssl_private_decrypt($input,$decrypted,$pu_key);

		   $output.=$decrypted;
	   }

	   return $output;
	   
	}
	
}