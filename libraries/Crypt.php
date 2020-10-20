<?php 

namespace app\libraries;

use yii;
use Defuse\Crypto\Key;

class Crypt
{
	/**
	 * The key is located in /config/params.php
	 * @return object key
	 */
    private static function loadKey()
	{
		$key = yii::$app->params['key'];
		$key = \Defuse\Crypto\Key::loadFromAsciiSafeString($key);

		return $key;
	}
	/**
	 * @param  string 	text to be encrypted
	 * @return encrypted string
	 *
	 * @throws \TypeError 
	 */
	public static function encrypt($text)
	{
		$key = self::loadKey();
		return \Defuse\Crypto\Crypto::encrypt($text, $key);
	}
	/**
	 * @param  string encrypted text
	 * @return string decrypted text
	 *
	 * @throws \TypeError 
	 */
	public static function decrypt($cipher_text)
	{
		$key = self::loadKey();
		return \Defuse\Crypto\Crypto::decrypt($cipher_text, $key);
	}
	/**
	 * @param  string /path/of/temp/file.jpg
	 * @param  string /path/to/encrypted/file.jpg
	 * @throws Ex\WrongKeyOrModifiedCiphertextException
	 */
	public static function encryptFile($file, $destination)
	{
		$key = self::loadKey();
		\Defuse\Crypto\File::encryptFile($file, $destination, $key);
	}
	/**
	 * @param  string /path/of/encrypted/file.jpg
	 * @param  string /path/to/decrypted/file.jpg
	 * @throws Ex\WrongKeyOrModifiedCiphertextException
	 */
	public static function decryptFile($file, $destination)
	{
		$key = self::loadKey();
		\Defuse\Crypto\File::decryptFile($file, $destination, $key);
	}
}