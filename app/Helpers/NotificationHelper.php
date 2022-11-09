<?php
namespace App\Helpers;

use DB;
use Log;
use Auth;
use Exception;
use Edujugon\PushNotification\PushNotification;

class NotificationHelper {

	const FIREBASE_API_KEY = 'AAAAFZog8Rg:APA91bFMrWWLWtPYkV3JD7FHdyLE83fVT7o2o6HGYYRYiynuQh-pO7S6P47oSA2yI_OqOUBJiOKktMs5oev7g4NzmwMmvgKhHrJzqCqya_Shn99PWiYKBNAdrslbgdOPXOIyJicpF16O';

	public static function send_pushnotification($user,$title,$message,$is_multiple = 0,$data = null,$type = 1){
		try
		{
			$apnUsers = array();
			$fcmUsers = array();
			$notifyTitle   = $title;
			$notifyMessage = ($message) ? $message: 'New message arrived';
			$unread = 0;
			// print_r($user);die();
			if($is_multiple == 1){
				foreach($user as $sendUser){
					if($sendUser->device_name == 'ios' && $sendUser->device_token != '' ) {
						$apnUsers[] = $sendUser->device_token;
						// $unread = $sendUser->notifications()->whereNull('read_at')->count();
					}

					if($sendUser->device_name == 'android' && $sendUser->device_token != '') {
						$fcmUsers[] = $sendUser->device_token;
						// $unread = $sendUser->notifications()->whereNull('read_at')->count();
					}
				}
			}else{

				if($user->device_name == 'ios' && $user->device_token != '' ) {
					$apnUsers[] = $user->device_token;
					// $unread = $user->notifications()->whereNull('read_at')->count();
				}

				if($user->device_name == 'android' && $user->device_token != '') {
					$fcmUsers[] = $user->device_token;
					// $unread = $user->notifications()->whereNull('read_at')->count();
				}
			}

			if(count($apnUsers)>0)
			{
				$apnPush = self::send_method_in_fcm_service($apnUsers, $notifyTitle, $notifyMessage,$data,$unread,$type);

				Log::info('FCM push notification result:');
				Log::info($apnPush);
			}

			if(count($fcmUsers)>0)
			{
				$fcmPush = self::send_method_in_fcm_service($fcmUsers, $notifyTitle, $notifyMessage,$data,$unread,$type);
				Log::info('FCM push notification result:');
				Log::info($fcmPush);
			}

			$apnUsers = array();
			$fcmUsers = array();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			Log::info("push notifty send exception:".$e->getMessage()."\n".$e->getTraceAsString());
		}  
	}

	public static function send_method_in_fcm_service($userlist, $title, $message,$data,$unread,$type)
	{
		$push = new PushNotification('fcm');
		$array = [
			'title' => $title,
			'message'  => $message,
			'type' => 0,
			'badge' => $unread,
			'sound' => 'default'
		];
		// if($data){
		// 	$array = [
		// 		'title' => $title,
		// 		'message'  => $message,
		// 		'type' => ($type) ? $type : 1,
		// 		'order_id' => $data,
		// 		'badge' => $unread,
		// 		'sound' => 'default'
		// 	];
		// }
		// print_r(json_encode($array));die();
		$push->setMessage([
			'notification' => [
				'title' => $title,
				'body'  => $message,
				'sound' => 'default',
				'badge' => $unread,
			],
			'data' => $array
		])
		->setApiKey(self::FIREBASE_API_KEY)
		->setDevicesToken($userlist)
		->send();

		return (isset($push->getFeedback()->results)) ? $push->getFeedback()->results : $push->getFeedback()->error;
	}

}