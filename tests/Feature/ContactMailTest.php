<?php

namespace Tests\Feature;

use App\Mail\Contact;
use Tests\TestCase;

class ContactMailTest extends TestCase
{
    public function test_contact_mail_is_sent_under_the_farm360_name(): void
    {
        $mail = new Contact(['send_name' => '山田', 'send_email' => 'yamada@example.com', 'send_message' => 'こんにちは']);

        $from = $mail->envelope()->from;
        $this->assertSame('farm360.info@gmail.com', $from->address);
        $this->assertSame('FARM360', $from->name);

        $mail->assertSeeInText('山田 様');
    }
}
