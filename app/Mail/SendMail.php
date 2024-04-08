<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected array $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $cc = $this->data['cc'] ?? [];
        if (count(Config::get('mail.cc.mail_to_address')) > 0) {
            foreach (Config::get('mail.cc.mail_to_address') as $address) {
                $cc[] = $address;
            }
        }
        $email = $this->from(Config::get('mail.from.address'), Config::get('mail.from.name'))
            ->subject($this->data['title'])
            ->to($this->data['to'])
            ->cc($cc)
            ->markdown('email.send_mail')
            ->with('data', $this->data);

        if (!empty($this->data['files'])) {
            foreach ($this->data['files'] as $file) {
                $email->attach($file, [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                ]);
            }
        }
        if (!empty($this->data['template_files'])) {
            foreach ($this->data['template_files'] as $template_file) {
                $email->attachFromStorageDisk('s3', $template_file);
            }
        }

        return $email;
    }
}
