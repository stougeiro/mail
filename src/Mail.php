<?php declare(strict_types=1);

    namespace STDW\Mail;

    use STDW\Schema\Schema;
    use Throwable;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;


	final class Mail
	{
        public static function send(array $data): bool
        {
            $schema = new Schema([
                'to' => new Schema([
                    'address' => 'string',
                    'name' => 'string', ]),
                'subject' => 'string',
                'body' => 'string',
            ]);

            if ( ! $schema->validate($data)) {
                throw new Exception('Formato de dados inválidos. Não foi possível enviar o email.');
            }


            $host = config('mail.host');
            $username = config('mail.username');
            $password = config('mail.password');
            $port = config('mail.port');
            $from_address = config('mail.from.address');
            $from_name = config('mail.from.name');

            $charset = 'UTF-8';

            try {
                $charset = config('mail.charset');
            } catch(Throwable $e) {}


            $mailer = new PHPMailer(true);

            ob_start();

                $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
                $mailer->isSMTP();
                $mailer->Host = $host;
                $mailer->SMTPAuth = true;
                $mailer->Username = $username;
                $mailer->Password = $password;
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mailer->Port = $port;
                $mailer->isHTML(true);

                $mailer->setFrom($from_address, $from_name);
                $mailer->addAddress($data['to']['address'], $data['to']['name']);

                $mailer->Subject = $data['subject'];
                $mailer->Body = $data['body'];
                $mailer->CharSet = $charset;

                $sent = $mailer->send();

            ob_clean();
            unset($mailer);

            return $sent;
        }
    }