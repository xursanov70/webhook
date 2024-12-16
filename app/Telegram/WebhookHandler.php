<?php

namespace App\Telegram;

use App\Models\User;
use App\UserPageEnum;
use DefStudio\Telegraph\Handlers\WebhookHandler as HandlersWebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use Illuminate\Http\Request;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\Log;
use Stringable;

class WebhookHandler extends HandlersWebhookHandler
{

    public function createUser($chatId, $firstName, $username): object
    {
        $user = User::where('chat_id', $chatId)->first();
        if (!$user) {
            $newUser = User::create([
                'chat_id' => $chatId,
                'page' => UserPageEnum::START,
                'first_name' => $firstName,
                'username' => $username ?? ""
            ]);
            return $newUser;
        } else {
            $user->update([
                'first_name' => $firstName,
                'username' => $username ?? ""
            ]);
            return $user;
        }
    }

    private function getUser($userId)
    {
        return User::where('chat_id', $userId)->first();
    }

    //     public function handle(Request $request, TelegraphBot $bot): void
    // {
    //     if ($this->message) {
    //         if ($this->message->has('contact')) {
    //             $contact = $this->message->contact();
    //             $userId = $this->message->from()->id();

    //             // Telefon raqamni yangilash
    //             User::where('telegram_id', $userId)->update([
    //                 'phone' => $contact->phoneNumber(),
    //             ]);

    //             $this->chat->message("Telefon raqamingiz saqlandi: " . $contact->phoneNumber())->send();
    //         } elseif ($this->message->text() === 'Info') {
    //             $userId = $this->message->from()->id();
    //             $user = User::where('telegram_id', $userId)->first();

    //             if ($user) {
    //                 $message = "Sizning ma'lumotlaringiz:\n\n";
    //                 $message .= "Ism: " . $user->first_name . "\n";
    //                 $message .= "Username: @" . ($user->username ?? 'Mavjud emas') . "\n";
    //                 $message .= "Telefon raqam: " . ($user->phone ?? 'Mavjud emas') . "\n";
    //                 $message .= "Telegram ID: " . $user->telegram_id;

    //                 $this->chat->message($message)->send();
    //             } else {
    //                 $this->chat->message("Ma'lumot topilmadi!")->send();
    //             }
    //         }
    //     }
    // }



    public function start(): void
    {
        if ($this->callbackQuery) {
            $firstName = $this->callbackQuery->from()->firstName();
            $username = $this->callbackQuery->from()->username();
            $this->createUser($this->callbackQuery->from()->id(), $firstName, $username);
        } elseif ($this->message) {
            $firstName = $this->message->from()->firstName();
            $username = $this->message->from()->username();
            $this->createUser($this->message->from()->id(), $firstName, $username);
        }

        $this->chat->message('Assalamu alaykum, Botimizga xush kelibsiz!')
            ->replyKeyboard(
                ReplyKeyboard::make()
                    ->button("Ma'lumotlarim")
                    ->button('Telefon raqam yuborish')->requestContact()
                    ->button('Lokatsiya yuborish')->requestLocation()
                    ->button('Quiz test yaratish')->requestQuiz()
                    ->button('So\'rovnoma yaratish')->requestPoll()
                    // ->button('Start WebApp')->webApp('https://web.app.dev')
                    ->chunk(2)
                    ->inputPlaceholder("Assalamu alaykum...")                        //keyboardagi orqadagi soz
                    ->resize()                                                      //chunk
            )->send();


        // $this->chat->message('assalamu alaykum, Botimizga xush kelibsiz!')
        // ->keyboard(Keyboard::make()->buttons([
        //     Button::make('Delete')->action('delete')->param('id', '42'),
        //     Button::make('open')->url('https://test.it'),
        //     Button::make('Web App')->webApp('https://web-app.test.it'),
        //     Button::make('switch')->switchInlineQuery('foo')->currentChat(),                   // botni oziga bot linkini yuborish
        //     Button::make('yuborish')->switchInlineQuery("Iltimos botimizga obuna bo'ling"),    //botni linkini boshqa userga yuborish
        // ])
        // )->send();


        //REPLY KEYBOARDS


        // $shouldRequestLocation = false;              //agar bo'lsa location buttoni korinadi

        // $this->chat->message('hello world')
        //     ->replyKeyboard(
        //         ReplyKeyboard::make()
        //             ->button('Send Contact')->requestContact()
        //             ->when($shouldRequestLocation, fn(ReplyKeyboard $keyboard) => $keyboard->button('Send Location')->requestLocation())                                                   //chunk
        //     )->send();



        // $this->chat->message('hello world')
        //     ->replyKeyboard(
        //         ReplyKeyboard::make()
        //             ->button('Send Contact')->requestContact()
        //             ->button('Send Location')->requestLocation()
        //             ->resize()                                                 // Buttonlarni kichik o'lchamda chiqaradi
        //     )->send();






        // $this->chat->message('hello world')
        //     ->replyKeyboard(ReplyKeyboard::make()
        //     ->row([
        //         ReplyButton::make('Send Contact')->requestContact(),         //yangi qatordan buttonlar chiqarish
        //         ReplyButton::make('Send Location')->requestLocation(),
        //     ])
        //     ->row([
        //         ReplyButton::make('Quiz')->requestQuiz(),
        //     ]))->send();


        // $this->chat->message('hello world')
        //     ->replyKeyboard(ReplyKeyboard::make()->buttons([
        //         ReplyButton::make('foo')->requestPoll(),                //sorovnoma tuzish
        //         ReplyButton::make('bar')->requestQuiz(),                //quiz test tuzish
        //         ReplyButton::make('baz')->webApp('https://webapp.dev'),      //not coinga oxshagan page ochish
        //     ])->resize()->oneTime()->chunk(3))->send();



        //KEYBOARDS

        // $userCanDelete = false;                                    // agar userCanDelete bo'lsa delete buttoni ham korinadi userga
        // $this->chat->message('hello world')
        // ->keyboard(Keyboard::make()
        // ->button('Dismiss')->action('dismiss')->param('id', '42')->width(0.5)
        // ->when($userCanDelete, fn(Keyboard $keyboard) => $keyboard->button('Delete')->action('delete')->param('id', '42')->width(0.5))
        // )->send();





        // $this->chat->message('hello world')
        //     ->keyboard(Keyboard::make()->buttons([
        //         Button::make('Delete')->action('delete')->param('id', '42'),
        //         Button::make('open')->url('https://test.it'),
        //         Button::make('Web App')->webApp('https://web-app.test.it'),
        //         Button::make('switch')->switchInlineQuery('foo')->currentChat(),                   // botni oziga bot linkini yuborish
        //         Button::make('yuborish')->switchInlineQuery("Iltimos botimizga obuna bo'ling"),    //botni linkini boshqa userga yuborish
        //     ])
        //     )->send();



        // $this->chat->message('hello world')
        //     ->keyboard(Keyboard::make()
        //     ->button('Delete')->action('delete')->param('id', '42')
        //     ->button('Dismiss')->action('dismiss')->param('id', '42')
        //     ->button('Share')->action('share')->param('id', '42')
        //     ->button('Solve')->action('solve')->param('id', '42')
        //     ->button('Open')->url('https://test.it')
        //     ->chunk(3)                                                                                 //chunk funksiyasi bir qatordagi buttonlar sonini bildiradi
        //     )->send();



        // $this->chat->message('hello world')
        //     ->keyboard(
        //         Keyboard::make()
        //             ->row([
        //                 Button::make('Delete')->action('delete')->param('id', '42'),                //Row funksiyasi yangi qatordan chiqaradi buttonlarni
        //                 Button::make('Dismiss')->action('dismiss')->param('id', '42'),
        //                 Button::make('test')->action('dismiss')->param('id', '42'),
        //             ])
        //             ->row([
        //                 Button::make('open')->url('https://test.it'),
        //                 Button::make('open')->url('https://test.it'),
        //             ])
        //     )->send();
    }

    public function handleChatMessage(Stringable $text): void
    {
        if (!$this->message) {
            $this->chat->message('Xatolik yuz berdi!')->send();
            return;
        }

        $userId = $this->message->from()->id();

        if ($text == "Ma'lumotlarim") {
            // Foydalanuvchi ma'lumotlarini yuborish
                $message = "Sizning ma'lumotlaringiz:\n\n";
                $message .= "Ism: " . $this->message->from()->firstName() . "\n";
                $message .= "Username: " . ($this->message->from()->username() ? "@" . $this->message->from()->username() : 'Mavjud emas') . "\n";
                $message .= "Telegram ID: " . $userId . "\n";

                $this->chat->message($message)->send();
                $this->chat->message("Ma'lumot topilmadi!")->send();
        } elseif ($this->message->contact()) {
            // Foydalanuvchidan yuborilgan contact ma'lumotini olish
            $contact = $this->message->contact();

            if ($contact) {
                User::where('chat_id', $userId)->update([
                    'phone' => $contact->phoneNumber(),
                ]);

                $this->chat->message("Telefon raqamingiz saqlandi: " . $contact->phoneNumber())->send();
            } else {
                $this->chat->message("Telefon raqamni yuborish xatosi!")->send();
            }
        }
    }


    // public function help(): void
    // {
    //     $this->reply("Sizga qanday yordam bera olamiz!");
    // }

    public function test()
    {
        $this->reply('testg');
    }

    //action ga function nomi yoziladi
    public function delete($id): void
    {
        $this->reply("O'chirildi " . $id);
    }
}
