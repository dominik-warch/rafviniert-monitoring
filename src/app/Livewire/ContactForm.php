<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ContactForm extends Component
{
    public string $name;
    public string $email;
    public string $content;
    protected array $rules = [
        "name" => "required",
        "email" => "required|email",
        "content" => "required|min:5",
    ];

    public function contactFormSubmit(): void
    {
        $contact = $this->validate();

        Mail::send("email",
            array(
                "name" => $this->name,
                "email" => $this->email,
                "content" => $this->content,
            ),
            function($message){
                $message->from("dominik.warch@mailbox.org");
                $message->to("dominik.warch@hs-mainz.de", "Support RAFVINIERT")->subject("RAFVINIERT - Supportanfrage");
            }
        );

        Toaster::success('Vielen Dank für Ihre Nachricht, wir werden uns in Kürze bei Ihnen melden!');

        $this->clearFields();
    }

    private function clearFields(): void
    {
        $this->name = "";
        $this->email = "";
        $this->content = "";
    }


    public function render()
    {
        return view("livewire.contact-form");
    }
}
