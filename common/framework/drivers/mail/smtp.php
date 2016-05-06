<?php

namespace Rhymix\Framework\Drivers\Mail;

/**
 * The SMTP mail driver.
 */
class SMTP extends Base implements \Rhymix\Framework\Drivers\MailInterface
{
	/**
	 * Direct invocation of the constructor is not permitted.
	 */
	protected function __construct(array $config)
	{
		$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'], $config['secure']);
		$transport->setUsername($config['user']);
		$transport->setPassword($config['pass']);
		$local_domain = $transport->getLocalDomain();
		if (preg_match('/^\*\.(.+)$/', $local_domain, $matches))
		{
			$transport->setLocalDomain($matches[1]);
		}
		$this->mailer = \Swift_Mailer::newInstance($transport);
	}
	
	/**
	 * Check if the current mail driver is supported on this server.
	 * 
	 * This method returns true on success and false on failure.
	 * 
	 * @return bool
	 */
	public static function isSupported()
	{
		return function_exists('proc_open');
	}
	
	/**
	 * Send a message.
	 * 
	 * This method returns true on success and false on failure.
	 * 
	 * @param object $message
	 * @return bool
	 */
	public function send(\Rhymix\Framework\Mail $message)
	{
		try
		{
			$result = $this->mailer->send($message->message, $errors);
		}
		catch(\Exception $e)
		{
			$message->errors[] = $e->getMessage();
			return false;
		}
		
		foreach ($errors as $error)
		{
			$message->errors[] = $error;
		}
		return (bool)$result;
	}
}
