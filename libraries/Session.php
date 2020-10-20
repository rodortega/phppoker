<?php

namespace app\libraries;

use yii;
use Firebase\JWT\JWT;

/**
 * This library uses HS256 algorithm to generate, fetch, validate, and invalidate JWT cookies.
 * Encryption keys, expiration and session cookie names are fetched from /config/params.php
 *
 *
 * -- GENERATE --
 * Session::generate(array(
 * 	"user_id" => 123,
 * 	"user_type" => "AUTHOR",
 * 	"is_admin" => true
 * ));
 *
 *
 * -- VERIFY --
 * Session::verify('user_type', "AUTHOR"); // returns true if matched, script exits if not.
 * Session::verify('user_type', "AUTHOR", false); // returns true if matched, false if not.
 *
 * 
 * -- GET --
 * Session::get('user_id'); // returns 123 if existing, script exits if not.
 * Session::get('user_id', false); // returns 123 if existing, false if not.
 *
 * 
 * -- REMOVE --
 * Session::remove(); // JWT cookie will be expired as soon as script exits.
 */
class Session
{
	/**
	 * @param  array data to be used in identification. This may include user ID, access priveleges, etc.
	 * @return cookie token that are passed back to client at the end of the script.	 
	 */
	public static function generate(array $data)
	{
		$payload = array();

		foreach ($data as $key => $value)
		{
			$payload[$key] = $value;
		}

		$payload['exp'] = time() + yii::$app->params['token_expire'];

		$_COOKIE[yii::$app->params['token_name']] = \Firebase\JWT\JWT::encode($payload, yii::$app->params['key'], 'HS256');

		Session::refresh();
	}
	/**
	 * @param  string The array key from your generated JWT you want to compare value to.
	 * @param  string The value you want the array key to be compared from
	 * @param  boolean [true] if script should stop if values are mismatched, [false] if script will continue even if mismatched
	 * @return boolean [true] if matched, [false] if not.
	 *
	 * @throws \HttpException Error 440 Invalid session.
	 */
	public static function verify($key, $value, $exitOnInvalid = true)
	{
		if (!empty($key) && !empty($value))
		{
			if (self::get($key, false) == $value)
			{
				return true;
			}
		}

		Session::invalidate($exitOnInvalid);
	}
	/**
	 * @param  string The array key from your generated JWT you want to compare value to. Null will return the JWT object
	 * @param  boolean [true] if script should stop if values are mismatched, [false] if script will continue even if mismatched
	 * @return string|object The value depending on key, will return object if array key is blank.
	 *
	 * @throws \HttpException Error 440 Invalid session.
	 */
	public static function get($key = null, $exitOnInvalid = true)
	{
		if (empty($_COOKIE[yii::$app->params['token_name']]))
		{
			Session::invalidate($exitOnInvalid);
		}

		try
		{
			$data = self::getData($exitOnInvalid);

			if ($key != null)
			{
				if (isset($data->$key))
				{
					Session::refresh();
					return $data->$key;
				}

				else
				{
					Session::invalidate($exitOnInvalid);
				}
			}

			else
			{
				Session::refresh();
				return $data;
			}
		}

		catch (\Exception $e)
		{
			Session::invalidate($exitOnInvalid);
		}
	}
	/**
	 * @return cookie that will expire immediately as soon as script ends.
	 */
	public static function remove()
	{
		setcookie(yii::$app->params['token_name'], '', time() - 3600 * 24, '/');
	}

	private static function getData($exitOnInvalid = true)
	{
		try
		{
			$data = \Firebase\JWT\JWT::decode($_COOKIE[yii::$app->params['token_name']], yii::$app->params['key'], array('HS256'));
			
			if ($data->exp <= time())
			{
				Session::invalidate($exitOnInvalid);
			}

			else
			{
				return $data;
			}
		}

		catch (\Exception $e)
		{
			Session::invalidate($exitOnInvalid);
		}
	}

	private static function refresh()
	{
		$data = self::getData();
		$data->exp = time() + yii::$app->params['token_expire'];

		$payload = \Firebase\JWT\JWT::encode($data, yii::$app->params['key'], 'HS256');

		setcookie(yii::$app->params['token_name'], '', time() - 3600 * 24, '/');
		setcookie(yii::$app->params['token_name'], $payload, $data->exp, '/');
	}

	private static function invalidate($exitOnInvalid = true)
	{
		if ($exitOnInvalid)
		{
			throw new \yii\web\HttpException(440, "Invalid Session.");
		}
		
		return false;
	}
}
?>