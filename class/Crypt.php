<?php
class Crypt
{

	public $alphastr, $alphabet, $vigenere_key, $openssl_key, $secretHash;

	public function __construct($key1="MediaC01", $key2="MediaC02") {
		$this->alphastr='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+';
		$this->alphabet =  preg_split("//", $this->alphastr, -1, PREG_SPLIT_NO_EMPTY);

		//key for layer 1
		$this->vigenere_key = $this->Hash($key1); //key is hashed

		//key for layer 2
		$this->openssl_key = $this->Hash($key2); //key is hashed
	}

	//this is an extra encryption function to hash the keys used in Encryption functions
	public function Hash($data)
	{
		$hashkey = '20140722'; //hashing key
		$hash = hash_hmac('sha256', $data, $hashkey);
		return $hash;
	}

	public function VigenereEncode($msg, $key)
	{
		$msg =  preg_split("//",$msg, -1, PREG_SPLIT_NO_EMPTY);
		$key =  preg_split("//",$key, -1, PREG_SPLIT_NO_EMPTY);

		$N = count($key);
		$M = count($this->alphabet);

		$encrypted = array();
		foreach($msg as $p=>$c){
			//$p index
			//$c value
			if(strpos($this->alphastr,$c)===false){
				$encrypted[] = $c;
			}else{
				$e = (strpos($this->alphastr,$c) + strpos($this->alphastr,$key[$p%$N]))%$M;
				//echo $c."->".$this->alphabet[$e]."<br>";
				$encrypted[] = $this->alphabet[$e];
			}
		}

		$encrypted_text =  join('',$encrypted);
		return $encrypted_text;
	}


	function VigenereDecode($msg, $key){

		$msg =  preg_split("//",$msg, -1, PREG_SPLIT_NO_EMPTY);
		$key =  preg_split("//",$key, -1, PREG_SPLIT_NO_EMPTY);

		$N = count($key);
		$M = count($this->alphabet);
		$encrypted = array();

		foreach($msg as $p=>$e){
			if(strpos($this->alphastr,$e)===false){
				$encrypted[] = $e;
			}else{
				$c = (strpos($this->alphastr, $e) - strpos($this->alphastr, $key[$p%$N]))%$M;
				if ($c<0) $c += $M;
				$encrypted[] = $this->alphabet[$c];
			}
		}

		$decrypted_text =  join('',$encrypted);
		return $decrypted_text;
	}

	function AesEncode($msg, $method, $key){
		//print $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
		//$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$iv = "1q2w3e4r5t6y7u8i";
		$output = openssl_encrypt($msg, $method, $key, 0 , $iv);
		$output = $iv.$output;
		return $output;
	}

	function AesDecode($msg, $method, $key){
		//$iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);//(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$iv_size = 16;
		$iv = substr($msg, 0, $iv_size);
		//$iv = "1234567812345678";
		$msg = substr($msg, $iv_size);
		$output = openssl_decrypt($msg, $method, $key, 0 , $iv);
		return $output;
	}

	function MediaEncrypt($data)
	{
		//First layer
		$vigenere_encrypted = $this->VigenereEncode($data, $this->vigenere_key);

		//Second layer
		$double_encrypted = $this->AesEncode($vigenere_encrypted, "AES-256-CBC", $this->openssl_key);
		return $double_encrypted;
	}

	function MediaDecrypt($data)
	{
		//Second layer
		$openssl_dcrypted = $this->AesDecode($data, "AES-256-CBC", $this->openssl_key);

		//First layer
		$original = $this->VigenereDecode($openssl_dcrypted, $this->vigenere_key);
		return $original;
	}

}

//usage


// $crypt = new Crypt();
// $input= "A=#48TvZ";
// echo "<br>";
// $enc = $crypt->MediaEncrypt($input);
// echo "<br>encrypted data: ". $enc."<br>";
// echo "<br>".gettype($enc) ."<br>";
// $dec = $crypt->MediaDecrypt("1q2w3e4r5t6y7u8iPyAhyhq/4b7nz5nnQj1few==");
// echo "<br>decrypted data: ". $dec."<br>";


?>